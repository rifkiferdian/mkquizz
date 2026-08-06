<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;
use App\Models\QuizSessionModel;
use App\Services\ParticipantAccessService;
use App\Services\QuizAttemptService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use Throwable;

final class QuizAccessController extends BaseController
{
    public function show(string $token): string
    {
        $quizSession = $this->findSession($token);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        return view('participant/access/join', [
            'title'       => 'Masuk Quiz',
            'quizSession' => $quizSession,
        ]);
    }

    public function join(string $token): RedirectResponse
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[150]',
            'pin'  => 'required|exact_length[6]|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = preg_replace('/\\s+/u', ' ', trim((string) $this->request->getPost('name'))) ?: '';
        $pin = trim((string) $this->request->getPost('pin'));
        $throttleKey = 'participant-join-' . hash('sha256', $this->request->getIPAddress() . ':' . $token);

        if (! service('throttler')->check($throttleKey, 10, MINUTE)) {
            return redirect()->back()->withInput()->with('error', 'Terlalu banyak percobaan. Silakan tunggu satu menit.');
        }

        try {
            $participant = (new ParticipantAccessService())->join(
                $token,
                $name,
                $pin,
                $this->request->getIPAddress(),
                mb_substr($this->request->getHeaderLine('User-Agent'), 0, 1000),
            );
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'Pendaftaran peserta gagal: {message}', ['message' => $exception->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'Data peserta belum dapat diproses. Silakan coba kembali.');
        }

        session()->set([
            'participant_id'         => $participant['id'],
            'participant_token'      => $participant['token'],
            'participant_name'       => $participant['name'],
            'participant_session_id' => $participant['session_id'],
        ]);

        try {
            $attempt = (new QuizAttemptService())->startOrResume((int) $participant['id'], (int) $participant['session_id']);

            return redirect()->to(site_url('quiz/' . rawurlencode($token) . '/attempt/' . $attempt['id']));
        } catch (Throwable $exception) {
            log_message('error', 'Attempt peserta gagal dibuat: {message}', ['message' => $exception->getMessage()]);

            return redirect()->to(site_url('quiz/' . rawurlencode($token) . '/lobby'))
                ->with('error', $exception instanceof DomainException ? $exception->getMessage() : 'Quiz belum dapat dimulai. Silakan coba kembali.');
        }
    }

    public function lobby(string $token): string|RedirectResponse
    {
        $quizSession = $this->findSession($token);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        if ((int) session('participant_session_id') !== (int) $quizSession['id'] || ! session('participant_id')) {
            return redirect()->to(site_url('quiz/' . rawurlencode($token)))
                ->with('error', 'Silakan masukkan nama dan PIN terlebih dahulu.');
        }

        return view('participant/access/lobby', [
            'title'       => 'Siap Mengikuti Quiz',
            'quizSession' => $quizSession,
            'participant' => ['name' => (string) session('participant_name')],
        ]);
    }

    public function start(string $token): RedirectResponse
    {
        $quizSession = $this->findSession($token);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        $participant = $this->findCurrentParticipant((int) $quizSession['id']);
        if ($participant === null) {
            return $this->participantLoginRedirect($token);
        }

        try {
            $attempt = (new QuizAttemptService())->startOrResume((int) $participant['id'], (int) $quizSession['id']);
        } catch (DomainException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'Attempt peserta gagal dibuat: {message}', ['message' => $exception->getMessage()]);

            return redirect()->back()->with('error', 'Quiz belum dapat dimulai. Silakan coba kembali.');
        }

        return redirect()->to(site_url('quiz/' . rawurlencode($token) . '/attempt/' . $attempt['id']));
    }

    public function attempt(string $token, int $attemptId): string|RedirectResponse
    {
        $quizSession = $this->findSession($token);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        $participant = $this->findCurrentParticipant((int) $quizSession['id']);
        if ($participant === null) {
            return $this->participantLoginRedirect($token);
        }

        $attempt = $this->findAttempt($attemptId, (int) $participant['id'], (int) $quizSession['id']);
        if ($attempt === null) {
            throw PageNotFoundException::forPageNotFound('Attempt quiz tidak ditemukan.');
        }

        if ($attempt['status'] === 'SUBMITTED') {
            return $this->resultRedirect($token, $attemptId);
        }

        $timezone = new DateTimeZone('Asia/Jakarta');
        $now = new DateTimeImmutable('now', $timezone);
        $expiresAt = new DateTimeImmutable($attempt['expires_at'], $timezone);
        if ($now >= $expiresAt) {
            (new QuizAttemptService())->submit($attemptId, (int) $participant['id'], []);

            return $this->resultRedirect($token, $attemptId);
        }

        return view('participant/quiz/attempt', [
            'title'            => 'Mengerjakan Quiz',
            'quizSession'      => $quizSession,
            'participant'      => $participant,
            'attempt'          => $attempt,
            'questions'        => $this->getAttemptQuestions($attemptId, (bool) $attempt['shuffle_options']),
            'remainingSeconds' => max(0, $expiresAt->getTimestamp() - $now->getTimestamp()),
        ]);
    }

    public function submit(string $token, int $attemptId): RedirectResponse
    {
        $quizSession = $this->findSession($token);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        $participant = $this->findCurrentParticipant((int) $quizSession['id']);
        if ($participant === null) {
            return $this->participantLoginRedirect($token);
        }

        try {
            (new QuizAttemptService())->submit(
                $attemptId,
                (int) $participant['id'],
                (array) $this->request->getPost('answers'),
            );
        } catch (DomainException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'Jawaban peserta gagal dikirim: {message}', ['message' => $exception->getMessage()]);

            return redirect()->back()->with('error', 'Jawaban belum dapat dikirim. Silakan coba kembali.');
        }

        return $this->resultRedirect($token, $attemptId);
    }

    public function result(string $token, int $attemptId): string|RedirectResponse
    {
        $quizSession = $this->findSession($token);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        $participant = $this->findCurrentParticipant((int) $quizSession['id']);
        if ($participant === null) {
            return $this->participantLoginRedirect($token);
        }

        $attempt = $this->findAttempt($attemptId, (int) $participant['id'], (int) $quizSession['id']);
        if ($attempt === null) {
            throw PageNotFoundException::forPageNotFound('Hasil quiz tidak ditemukan.');
        }

        if ($attempt['status'] === 'IN_PROGRESS') {
            return redirect()->to(site_url('quiz/' . rawurlencode($token) . '/attempt/' . $attemptId));
        }

        return view('participant/quiz/result', [
            'title'       => 'Hasil Quiz',
            'quizSession' => $quizSession,
            'participant' => $participant,
            'attempt'     => $attempt,
        ]);
    }

    public function leaderboard(string $token): string
    {
        $quizSession = $this->findSession($token);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        $sessionModel = model(QuizSessionModel::class);

        return view('admin/sessions/leaderboard', [
            'title'        => 'Leaderboard Sesi',
            'quizSession'  => $quizSession,
            'participants' => $sessionModel->getParticipants((int) $quizSession['id']),
            'performance'  => $sessionModel->getPerformance((int) $quizSession['id']),
        ]);
    }

    /** @return array<string, mixed>|null */
    private function findSession(string $token): ?array
    {
        $session = db_connect()->table('quiz_sessions')
            ->select('quiz_sessions.*, quizzes.title AS quiz_title, quizzes.description AS quiz_description, quizzes.duration_minutes, quizzes.passing_score, quizzes.status AS quiz_status, materials.title AS material_title, materials.code AS material_code')
            ->select('(SELECT COUNT(*) FROM quiz_questions WHERE quiz_questions.quiz_id = quizzes.id) AS question_count', false)
            ->join('quizzes', 'quizzes.id = quiz_sessions.quiz_id')
            ->join('materials', 'materials.id = quizzes.material_id')
            ->where('quiz_sessions.session_token', $token)
            ->get()
            ->getRowArray();

        return $session ?: null;
    }

    /** @return array<string, mixed>|null */
    private function findCurrentParticipant(int $sessionId): ?array
    {
        $participantId = (int) session('participant_id');
        $participantToken = (string) session('participant_token');

        if ($participantId <= 0 || $participantToken === '' || (int) session('participant_session_id') !== $sessionId) {
            return null;
        }

        $participant = db_connect()->table('participants')
            ->where('id', $participantId)
            ->where('session_id', $sessionId)
            ->where('participant_token', $participantToken)
            ->get()
            ->getRowArray();

        return $participant ?: null;
    }

    /** @return array<string, mixed>|null */
    private function findAttempt(int $attemptId, int $participantId, int $sessionId): ?array
    {
        $attempt = db_connect()->table('quiz_attempts')
            ->select('quiz_attempts.*, quizzes.title AS quiz_title, quizzes.passing_score, quizzes.show_score, quizzes.show_correct_answer, quizzes.show_explanation, quizzes.allow_review, quizzes.shuffle_options')
            ->select('TIMESTAMPDIFF(SECOND, quiz_attempts.started_at, COALESCE(quiz_attempts.submitted_at, NOW())) AS duration_seconds', false)
            ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id')
            ->where('quiz_attempts.id', $attemptId)
            ->where('quiz_attempts.participant_id', $participantId)
            ->where('quiz_attempts.session_id', $sessionId)
            ->get()
            ->getRowArray();

        return $attempt ?: null;
    }

    /** @return list<array<string, mixed>> */
    private function getAttemptQuestions(int $attemptId, bool $shuffleOptions): array
    {
        $database = db_connect();
        $questions = $database->table('attempt_questions')
            ->select('attempt_questions.question_id, attempt_questions.question_order, attempt_questions.score, questions.question_text, questions.question_type')
            ->join('questions', 'questions.id = attempt_questions.question_id')
            ->where('attempt_questions.attempt_id', $attemptId)
            ->orderBy('attempt_questions.question_order', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($questions as &$question) {
            $options = $database->table('question_options')
                ->select('id, question_id, option_key, option_text')
                ->where('question_id', (int) $question['question_id'])
                ->orderBy('sort_order', 'ASC')
                ->get()
                ->getResultArray();

            if ($shuffleOptions) {
                usort($options, static fn (array $first, array $second): int => strcmp(
                    hash('sha256', $attemptId . ':' . $first['id']),
                    hash('sha256', $attemptId . ':' . $second['id']),
                ));
            }

            $question['options'] = $options;
        }
        unset($question);

        return $questions;
    }

    private function participantLoginRedirect(string $token): RedirectResponse
    {
        return redirect()->to(site_url('quiz/' . rawurlencode($token)))
            ->with('error', 'Silakan masukkan nama dan PIN terlebih dahulu.');
    }

    private function resultRedirect(string $token, int $attemptId): RedirectResponse
    {
        return redirect()->to(site_url('quiz/' . rawurlencode($token) . '/result/' . $attemptId));
    }
}
