<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

final class QuizSessionModel extends Model
{
    protected $table = 'quiz_sessions';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'quiz_id',
        'session_name',
        'session_token',
        'pin',
        'pin_valid_minutes',
        'pin_valid_from',
        'pin_valid_until',
        'max_participants',
        'allow_duplicate_name',
        'status',
        'created_by',
        'opened_at',
        'closed_at',
    ];

    protected $useTimestamps = true;

    /** @return list<array<string, mixed>> */
    public function getAdminList(array $filters, int $limit, int $offset): array
    {
        return $this->applyFilters($this->adminListBuilder(), $filters)
            ->orderBy('quiz_sessions.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function countAdminList(array $filters): int
    {
        return $this->applyFilters($this->builder(), $filters)->countAllResults();
    }

    /** @return array{total: int, open: int, waiting: int, closed: int} */
    public function getSummary(): array
    {
        $row = $this->builder()
            ->select("COUNT(*) AS total, SUM(status = 'OPEN') AS open, SUM(status = 'WAITING') AS waiting, SUM(status = 'CLOSED') AS closed", false)
            ->get()
            ->getRowArray();

        return [
            'total'   => (int) ($row['total'] ?? 0),
            'open'    => (int) ($row['open'] ?? 0),
            'waiting' => (int) ($row['waiting'] ?? 0),
            'closed'  => (int) ($row['closed'] ?? 0),
        ];
    }

    /** @return array<string, mixed>|null */
    public function findAdminDetail(int $id): ?array
    {
        $session = $this->adminListBuilder()
            ->where('quiz_sessions.id', $id)
            ->get()
            ->getRowArray();

        return $session ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function getParticipants(int $sessionId): array
    {
        return $this->db->table('participants')
            ->select('participants.*')
            ->select('(SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.session_id = participants.session_id) AS attempt_count', false)
            ->select("(SELECT MAX(final_score) FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.session_id = participants.session_id AND quiz_attempts.status = 'SUBMITTED') AS best_score", false)
            ->select("(SELECT status FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.session_id = participants.session_id ORDER BY id DESC LIMIT 1) AS latest_attempt_status", false)
            ->select("(SELECT passed FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.session_id = participants.session_id AND quiz_attempts.status = 'SUBMITTED' ORDER BY id DESC LIMIT 1) AS latest_passed", false)
            ->where('participants.session_id', $sessionId)
            ->orderBy('participants.joined_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /** @return array{participants: int, attempts: int, submitted: int, passed: int, pass_rate: float, average: float, highest: float} */
    public function getPerformance(int $sessionId): array
    {
        $participantCount = $this->db->table('participants')->where('session_id', $sessionId)->countAllResults();
        $row = $this->db->table('quiz_attempts')
            ->select("COUNT(*) AS attempts, SUM(status = 'SUBMITTED') AS submitted, SUM(status = 'SUBMITTED' AND passed = 1) AS passed", false)
            ->select("AVG(CASE WHEN status = 'SUBMITTED' THEN final_score END) AS average", false)
            ->select("MAX(CASE WHEN status = 'SUBMITTED' THEN final_score END) AS highest", false)
            ->where('session_id', $sessionId)
            ->get()
            ->getRowArray();

        $submitted = (int) ($row['submitted'] ?? 0);
        $passed = (int) ($row['passed'] ?? 0);

        return [
            'participants' => $participantCount,
            'attempts'     => (int) ($row['attempts'] ?? 0),
            'submitted'    => $submitted,
            'passed'       => $passed,
            'pass_rate'    => $submitted > 0 ? round(($passed / $submitted) * 100, 1) : 0.0,
            'average'      => round((float) ($row['average'] ?? 0), 1),
            'highest'      => round((float) ($row['highest'] ?? 0), 1),
        ];
    }

    private function adminListBuilder(): BaseBuilder
    {
        return $this->builder()
            ->select('quiz_sessions.*, quizzes.title AS quiz_title, quizzes.duration_minutes, quizzes.passing_score, quizzes.status AS quiz_status, materials.title AS material_title, materials.code AS material_code, users.name AS creator_name')
            ->select('(quiz_sessions.pin_valid_until < NOW()) AS pin_expired', false)
            ->select('(SELECT COUNT(*) FROM participants WHERE participants.session_id = quiz_sessions.id) AS participant_count', false)
            ->select('(SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.session_id = quiz_sessions.id) AS attempt_count', false)
            ->select("(SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.session_id = quiz_sessions.id AND quiz_attempts.status = 'SUBMITTED') AS submitted_count", false)
            ->select("(SELECT ROUND(AVG(final_score), 1) FROM quiz_attempts WHERE quiz_attempts.session_id = quiz_sessions.id AND quiz_attempts.status = 'SUBMITTED') AS average_score", false)
            ->join('quizzes', 'quizzes.id = quiz_sessions.quiz_id')
            ->join('materials', 'materials.id = quizzes.material_id')
            ->join('users', 'users.id = quiz_sessions.created_by', 'left');
    }

    private function applyFilters(BaseBuilder $builder, array $filters): BaseBuilder
    {
        if ($filters['search'] !== '') {
            $builder->groupStart()
                ->like('quiz_sessions.session_name', $filters['search'])
                ->orLike('quiz_sessions.pin', $filters['search'])
                ->orLike('quiz_sessions.session_token', $filters['search'])
                ->groupEnd();
        }

        if ($filters['quiz_id'] > 0) {
            $builder->where('quiz_sessions.quiz_id', $filters['quiz_id']);
        }

        if ($filters['status'] !== '') {
            $builder->where('quiz_sessions.status', $filters['status']);
        }

        return $builder;
    }
}
