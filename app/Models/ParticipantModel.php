<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

final class ParticipantModel extends Model
{
    protected $table = 'participants';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['session_id', 'name', 'participant_token', 'ip_address', 'user_agent', 'joined_at'];

    /** @return list<array<string, mixed>> */
    public function getAdminList(array $filters, int $limit, int $offset): array
    {
        return $this->applyFilters($this->adminListBuilder(), $filters)
            ->orderBy('participants.joined_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function countAdminList(array $filters): int
    {
        $builder = $this->builder()
            ->join('quiz_sessions', 'quiz_sessions.id = participants.session_id');

        return $this->applyFilters($builder, $filters)->countAllResults();
    }

    /** @return array{total: int, today: int, attempted: int, passed: int} */
    public function getSummary(): array
    {
        $row = $this->builder()
            ->select('COUNT(*) AS total, SUM(DATE(joined_at) = CURDATE()) AS today', false)
            ->select('SUM(EXISTS(SELECT 1 FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id)) AS attempted', false)
            ->select("SUM(EXISTS(SELECT 1 FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.status = 'SUBMITTED' AND quiz_attempts.passed = 1)) AS passed", false)
            ->get()
            ->getRowArray();

        return [
            'total'     => (int) ($row['total'] ?? 0),
            'today'     => (int) ($row['today'] ?? 0),
            'attempted' => (int) ($row['attempted'] ?? 0),
            'passed'    => (int) ($row['passed'] ?? 0),
        ];
    }

    /** @return array<string, mixed>|null */
    public function findAdminDetail(int $id): ?array
    {
        $participant = $this->adminListBuilder()->where('participants.id', $id)->get()->getRowArray();

        return $participant ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function getAttempts(int $participantId): array
    {
        return $this->db->table('quiz_attempts')
            ->select('quiz_attempts.*, quizzes.title AS quiz_title, quiz_sessions.session_name')
            ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id')
            ->join('quiz_sessions', 'quiz_sessions.id = quiz_attempts.session_id')
            ->where('quiz_attempts.participant_id', $participantId)
            ->orderBy('quiz_attempts.started_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /** @return array{attempts: int, submitted: int, average: float, highest: float, correct: int, answered: int} */
    public function getPerformance(int $participantId): array
    {
        $row = $this->db->table('quiz_attempts')
            ->select("COUNT(*) AS attempts, SUM(status = 'SUBMITTED') AS submitted", false)
            ->select("AVG(CASE WHEN status = 'SUBMITTED' THEN final_score END) AS average", false)
            ->select("MAX(CASE WHEN status = 'SUBMITTED' THEN final_score END) AS highest", false)
            ->select('SUM(total_correct) AS correct, SUM(total_answered) AS answered', false)
            ->where('participant_id', $participantId)
            ->get()
            ->getRowArray();

        return [
            'attempts'  => (int) ($row['attempts'] ?? 0),
            'submitted' => (int) ($row['submitted'] ?? 0),
            'average'   => round((float) ($row['average'] ?? 0), 1),
            'highest'   => round((float) ($row['highest'] ?? 0), 1),
            'correct'   => (int) ($row['correct'] ?? 0),
            'answered'  => (int) ($row['answered'] ?? 0),
        ];
    }

    private function adminListBuilder(): BaseBuilder
    {
        return $this->builder()
            ->select('participants.*, quiz_sessions.session_name, quiz_sessions.pin AS session_pin, quiz_sessions.status AS session_status, quizzes.id AS quiz_id, quizzes.title AS quiz_title, quizzes.passing_score, materials.title AS material_title, materials.code AS material_code')
            ->select('(SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id) AS attempt_count', false)
            ->select("(SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.status = 'SUBMITTED') AS submitted_count", false)
            ->select("(SELECT MAX(final_score) FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.status = 'SUBMITTED') AS best_score", false)
            ->select('(SELECT status FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id ORDER BY id DESC LIMIT 1) AS latest_attempt_status', false)
            ->select("(SELECT final_score FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.status = 'SUBMITTED' ORDER BY id DESC LIMIT 1) AS latest_score", false)
            ->select("(SELECT passed FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.status = 'SUBMITTED' ORDER BY id DESC LIMIT 1) AS latest_passed", false)
            ->join('quiz_sessions', 'quiz_sessions.id = participants.session_id')
            ->join('quizzes', 'quizzes.id = quiz_sessions.quiz_id')
            ->join('materials', 'materials.id = quizzes.material_id');
    }

    private function applyFilters(BaseBuilder $builder, array $filters): BaseBuilder
    {
        if ($filters['search'] !== '') {
            $builder->groupStart()
                ->like('participants.name', $filters['search'])
                ->orLike('participants.participant_token', $filters['search'])
                ->orLike('participants.ip_address', $filters['search'])
                ->groupEnd();
        }

        if ($filters['quiz_id'] > 0) {
            $builder->where('quiz_sessions.quiz_id', $filters['quiz_id']);
        }

        if ($filters['session_id'] > 0) {
            $builder->where('participants.session_id', $filters['session_id']);
        }

        if ($filters['activity'] === 'NOT_STARTED') {
            $builder->where('NOT EXISTS (SELECT 1 FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id)', null, false);
        } elseif ($filters['activity'] === 'IN_PROGRESS') {
            $builder->where("EXISTS (SELECT 1 FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.status = 'IN_PROGRESS')", null, false);
        } elseif ($filters['activity'] === 'COMPLETED') {
            $builder->where("EXISTS (SELECT 1 FROM quiz_attempts WHERE quiz_attempts.participant_id = participants.id AND quiz_attempts.status = 'SUBMITTED')", null, false);
        }

        return $builder;
    }
}
