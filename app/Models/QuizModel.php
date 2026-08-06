<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

final class QuizModel extends Model
{
    protected $table = 'quizzes';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'material_id',
        'title',
        'description',
        'duration_minutes',
        'passing_score',
        'shuffle_questions',
        'shuffle_options',
        'show_score',
        'show_correct_answer',
        'show_explanation',
        'allow_review',
        'created_by',
        'status',
    ];

    protected $useTimestamps = true;

    /** @return list<array<string, mixed>> */
    public function getAdminList(array $filters, int $limit, int $offset): array
    {
        return $this->applyFilters($this->adminListBuilder(), $filters)
            ->orderBy('quizzes.updated_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function countAdminList(array $filters): int
    {
        return $this->applyFilters($this->builder(), $filters)->countAllResults();
    }

    /** @return array{total: int, active: int, draft: int, inactive: int} */
    public function getSummary(): array
    {
        $row = $this->builder()
            ->select("COUNT(*) AS total, SUM(status = 'ACTIVE') AS active, SUM(status = 'DRAFT') AS draft, SUM(status = 'INACTIVE') AS inactive", false)
            ->get()
            ->getRowArray();

        return [
            'total'    => (int) ($row['total'] ?? 0),
            'active'   => (int) ($row['active'] ?? 0),
            'draft'    => (int) ($row['draft'] ?? 0),
            'inactive' => (int) ($row['inactive'] ?? 0),
        ];
    }

    /** @return array<string, mixed>|null */
    public function findAdminDetail(int $id): ?array
    {
        $quiz = $this->adminListBuilder()
            ->where('quizzes.id', $id)
            ->get()
            ->getRowArray();

        return $quiz ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function getQuizQuestions(int $quizId): array
    {
        return $this->db->table('quiz_questions')
            ->select('quiz_questions.id, quiz_questions.score, quiz_questions.sort_order, questions.id AS question_id, questions.question_text, questions.question_type, questions.difficulty, questions.is_active')
            ->select('(SELECT COUNT(*) FROM question_options WHERE question_options.question_id = questions.id) AS option_count', false)
            ->select('(SELECT option_text FROM question_options WHERE question_options.question_id = questions.id AND question_options.is_correct = 1 ORDER BY sort_order ASC LIMIT 1) AS correct_answer', false)
            ->join('questions', 'questions.id = quiz_questions.question_id')
            ->where('quiz_questions.quiz_id', $quizId)
            ->orderBy('quiz_questions.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** @return list<array<string, mixed>> */
    public function getSessions(int $quizId, int $limit = 8): array
    {
        return $this->db->table('quiz_sessions')
            ->select('quiz_sessions.*')
            ->select('(SELECT COUNT(*) FROM participants WHERE participants.session_id = quiz_sessions.id) AS participant_count', false)
            ->select('(SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.session_id = quiz_sessions.id) AS attempt_count', false)
            ->select("(SELECT ROUND(AVG(quiz_attempts.final_score), 1) FROM quiz_attempts WHERE quiz_attempts.session_id = quiz_sessions.id AND quiz_attempts.status = 'SUBMITTED') AS average_score", false)
            ->where('quiz_sessions.quiz_id', $quizId)
            ->orderBy('quiz_sessions.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /** @return array{total: int, submitted: int, passed: int, pass_rate: float, average: float, highest: float, lowest: float} */
    public function getPerformance(int $quizId): array
    {
        $row = $this->db->table('quiz_attempts')
            ->select("COUNT(*) AS total, SUM(status = 'SUBMITTED') AS submitted, SUM(status = 'SUBMITTED' AND passed = 1) AS passed", false)
            ->select("AVG(CASE WHEN status = 'SUBMITTED' THEN final_score END) AS average", false)
            ->select("MAX(CASE WHEN status = 'SUBMITTED' THEN final_score END) AS highest", false)
            ->select("MIN(CASE WHEN status = 'SUBMITTED' THEN final_score END) AS lowest", false)
            ->where('quiz_id', $quizId)
            ->get()
            ->getRowArray();

        $submitted = (int) ($row['submitted'] ?? 0);
        $passed = (int) ($row['passed'] ?? 0);

        return [
            'total'     => (int) ($row['total'] ?? 0),
            'submitted' => $submitted,
            'passed'    => $passed,
            'pass_rate' => $submitted > 0 ? round(($passed / $submitted) * 100, 1) : 0.0,
            'average'   => round((float) ($row['average'] ?? 0), 1),
            'highest'   => round((float) ($row['highest'] ?? 0), 1),
            'lowest'    => round((float) ($row['lowest'] ?? 0), 1),
        ];
    }

    /** @return array{submitted_attempts: int, questions: list<array<string, mixed>>} */
    public function getQuestionAnalysis(int $quizId): array
    {
        $quizId = max(0, $quizId);
        $submittedAttempts = $this->db->table('quiz_attempts')
            ->where('quiz_id', $quizId)
            ->where('status', 'SUBMITTED')
            ->countAllResults();

        $questions = $this->db->table('quiz_questions')
            ->select('quiz_questions.question_id, quiz_questions.sort_order, quiz_questions.score, questions.question_text, questions.difficulty, questions.question_type')
            ->select('(SELECT option_text FROM question_options WHERE question_options.question_id = questions.id AND question_options.is_correct = 1 ORDER BY sort_order ASC LIMIT 1) AS correct_answer', false)
            ->select("(SELECT COUNT(*) FROM participant_answers INNER JOIN quiz_attempts ON quiz_attempts.id = participant_answers.attempt_id WHERE quiz_attempts.quiz_id = {$quizId} AND quiz_attempts.status = 'SUBMITTED' AND participant_answers.question_id = questions.id) AS answered_count", false)
            ->select("(SELECT COUNT(*) FROM participant_answers INNER JOIN quiz_attempts ON quiz_attempts.id = participant_answers.attempt_id WHERE quiz_attempts.quiz_id = {$quizId} AND quiz_attempts.status = 'SUBMITTED' AND participant_answers.question_id = questions.id AND participant_answers.is_correct = 1) AS correct_count", false)
            ->select("(SELECT COUNT(*) FROM participant_answers INNER JOIN quiz_attempts ON quiz_attempts.id = participant_answers.attempt_id WHERE quiz_attempts.quiz_id = {$quizId} AND quiz_attempts.status = 'SUBMITTED' AND participant_answers.question_id = questions.id AND participant_answers.is_correct = 0) AS wrong_count", false)
            ->join('questions', 'questions.id = quiz_questions.question_id')
            ->where('quiz_questions.quiz_id', $quizId)
            ->orderBy('quiz_questions.sort_order', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($questions as &$question) {
            $answered = (int) $question['answered_count'];
            $correct = (int) $question['correct_count'];
            $wrong = (int) $question['wrong_count'];
            $question['unanswered_count'] = max(0, $submittedAttempts - $answered);
            $question['correct_rate'] = $answered > 0 ? round(($correct / $answered) * 100, 1) : 0.0;
            $question['wrong_rate'] = $answered > 0 ? round(($wrong / $answered) * 100, 1) : 0.0;
        }
        unset($question);

        return [
            'submitted_attempts' => $submittedAttempts,
            'questions'          => $questions,
        ];
    }

    private function adminListBuilder(): BaseBuilder
    {
        return $this->builder()
            ->select('quizzes.*, materials.title AS material_title, materials.code AS material_code, users.name AS creator_name')
            ->select('(SELECT COUNT(*) FROM quiz_questions WHERE quiz_questions.quiz_id = quizzes.id) AS question_count', false)
            ->select('(SELECT COUNT(*) FROM quiz_sessions WHERE quiz_sessions.quiz_id = quizzes.id) AS session_count', false)
            ->select("(SELECT COUNT(*) FROM quiz_sessions WHERE quiz_sessions.quiz_id = quizzes.id AND quiz_sessions.status IN ('OPEN', 'WAITING')) AS active_session_count", false)
            ->select('(SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.quiz_id = quizzes.id) AS attempt_count', false)
            ->select("(SELECT ROUND(AVG(quiz_attempts.final_score), 1) FROM quiz_attempts WHERE quiz_attempts.quiz_id = quizzes.id AND quiz_attempts.status = 'SUBMITTED') AS average_score", false)
            ->join('materials', 'materials.id = quizzes.material_id')
            ->join('users', 'users.id = quizzes.created_by', 'left');
    }

    private function applyFilters(BaseBuilder $builder, array $filters): BaseBuilder
    {
        if ($filters['search'] !== '') {
            $builder->groupStart()
                ->like('quizzes.title', $filters['search'])
                ->orLike('quizzes.description', $filters['search'])
                ->groupEnd();
        }

        if ($filters['material_id'] > 0) {
            $builder->where('quizzes.material_id', $filters['material_id']);
        }

        if ($filters['status'] !== '') {
            $builder->where('quizzes.status', $filters['status']);
        }

        return $builder;
    }
}
