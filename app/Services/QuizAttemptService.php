<?php

namespace App\Services;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use RuntimeException;

final class QuizAttemptService
{
    /** @return array<string, mixed> */
    public function startOrResume(int $participantId, int $sessionId): array
    {
        $database = db_connect();
        $database->transBegin();

        $participant = $database->query(
            'SELECT participants.id, participants.session_id, quiz_sessions.quiz_id, quiz_sessions.status AS session_status, quizzes.duration_minutes, quizzes.shuffle_questions
             FROM participants
             INNER JOIN quiz_sessions ON quiz_sessions.id = participants.session_id
             INNER JOIN quizzes ON quizzes.id = quiz_sessions.quiz_id
             WHERE participants.id = ? AND participants.session_id = ? FOR UPDATE',
            [$participantId, $sessionId],
        )->getRowArray();

        if ($participant === null) {
            $database->transRollback();
            throw new DomainException('Data peserta tidak ditemukan pada sesi ini.');
        }

        if ($participant['session_status'] !== 'OPEN') {
            $database->transRollback();
            throw new DomainException('Sesi quiz belum dibuka atau sudah ditutup.');
        }

        $activeAttempt = $database->table('quiz_attempts')
            ->where('participant_id', $participantId)
            ->where('session_id', $sessionId)
            ->where('status', 'IN_PROGRESS')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if ($activeAttempt !== null) {
            $database->transCommit();

            return $activeAttempt;
        }

        $questions = $database->table('quiz_questions')
            ->select('question_id, score, sort_order')
            ->where('quiz_id', (int) $participant['quiz_id'])
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        if ($questions === []) {
            $database->transRollback();
            throw new DomainException('Quiz belum memiliki pertanyaan.');
        }

        if ((bool) $participant['shuffle_questions']) {
            shuffle($questions);
        }

        $timezone = new DateTimeZone('Asia/Jakarta');
        $startedAt = new DateTimeImmutable('now', $timezone);
        $expiresAt = $startedAt->add(new DateInterval('PT' . max(1, (int) $participant['duration_minutes']) . 'M'));
        $maxScore = array_sum(array_map(static fn (array $question): float => (float) $question['score'], $questions));

        $database->table('quiz_attempts')->insert([
            'participant_id' => $participantId,
            'session_id'     => $sessionId,
            'quiz_id'        => (int) $participant['quiz_id'],
            'started_at'     => $startedAt->format('Y-m-d H:i:s'),
            'expires_at'     => $expiresAt->format('Y-m-d H:i:s'),
            'total_questions'=> count($questions),
            'max_score'      => $maxScore,
            'status'         => 'IN_PROGRESS',
        ]);
        $attemptId = (int) $database->insertID();

        $snapshot = [];
        foreach ($questions as $index => $question) {
            $snapshot[] = [
                'attempt_id'    => $attemptId,
                'question_id'   => (int) $question['question_id'],
                'question_order'=> $index + 1,
                'score'         => (float) $question['score'],
            ];
        }
        $database->table('attempt_questions')->insertBatch($snapshot);

        if (! $database->transStatus()) {
            $database->transRollback();
            throw new RuntimeException('Attempt quiz gagal dibuat.');
        }

        $database->transCommit();

        return $database->table('quiz_attempts')->where('id', $attemptId)->get()->getRowArray();
    }

    /** @param array<int|string, mixed> $submittedAnswers */
    public function submit(int $attemptId, int $participantId, array $submittedAnswers): array
    {
        $database = db_connect();
        $database->transBegin();

        $attempt = $database->query(
            'SELECT quiz_attempts.*, quizzes.passing_score
             FROM quiz_attempts
             INNER JOIN quizzes ON quizzes.id = quiz_attempts.quiz_id
             WHERE quiz_attempts.id = ? AND quiz_attempts.participant_id = ? FOR UPDATE',
            [$attemptId, $participantId],
        )->getRowArray();

        if ($attempt === null) {
            $database->transRollback();
            throw new DomainException('Attempt quiz tidak ditemukan.');
        }

        if ($attempt['status'] === 'SUBMITTED') {
            $database->transCommit();

            return $attempt;
        }

        if ($attempt['status'] !== 'IN_PROGRESS') {
            $database->transRollback();
            throw new DomainException('Attempt quiz ini sudah tidak dapat dikirim.');
        }

        $questions = $database->table('attempt_questions')
            ->where('attempt_id', $attemptId)
            ->orderBy('question_order', 'ASC')
            ->get()
            ->getResultArray();

        $questionIds = array_map(static fn (array $question): int => (int) $question['question_id'], $questions);
        $options = $database->table('question_options')
            ->select('id, question_id, is_correct')
            ->whereIn('question_id', $questionIds)
            ->get()
            ->getResultArray();
        $optionsById = [];
        foreach ($options as $option) {
            $optionsById[(int) $option['id']] = $option;
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
        $answerRows = [];
        $totalAnswered = 0;
        $totalCorrect = 0;
        $totalScore = 0.0;

        foreach ($questions as $question) {
            $questionId = (int) $question['question_id'];
            $optionId = (int) ($submittedAnswers[$questionId] ?? 0);
            $option = $optionsById[$optionId] ?? null;

            if ($option === null || (int) $option['question_id'] !== $questionId) {
                continue;
            }

            $isCorrect = (bool) $option['is_correct'];
            $score = $isCorrect ? (float) $question['score'] : 0.0;
            $totalAnswered++;
            $totalCorrect += $isCorrect ? 1 : 0;
            $totalScore += $score;
            $answerRows[] = [
                'attempt_id'       => $attemptId,
                'question_id'      => $questionId,
                'selected_option_id'=> $optionId,
                'is_correct'       => (int) $isCorrect,
                'score_received'   => $score,
                'answered_at'      => $now->format('Y-m-d H:i:s'),
            ];
        }

        if ($answerRows !== []) {
            $database->table('participant_answers')->insertBatch($answerRows);
        }

        $maxScore = (float) $attempt['max_score'];
        $finalScore = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0.0;
        $database->table('quiz_attempts')->where('id', $attemptId)->update([
            'submitted_at' => $now->format('Y-m-d H:i:s'),
            'total_answered'=> $totalAnswered,
            'total_correct' => $totalCorrect,
            'total_wrong'   => $totalAnswered - $totalCorrect,
            'total_score'   => $totalScore,
            'final_score'   => $finalScore,
            'passed'        => (int) ($finalScore >= (float) $attempt['passing_score']),
            'status'        => 'SUBMITTED',
        ]);

        if (! $database->transStatus()) {
            $database->transRollback();
            throw new RuntimeException('Jawaban quiz gagal disimpan.');
        }

        $database->transCommit();

        return $database->table('quiz_attempts')->where('id', $attemptId)->get()->getRowArray();
    }
}
