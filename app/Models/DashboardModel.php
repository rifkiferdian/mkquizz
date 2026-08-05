<?php

namespace App\Models;

use CodeIgniter\Model;

final class DashboardModel extends Model
{
    protected $table = 'quizzes';

    protected $returnType = 'array';

    /** @return array<string, int> */
    public function getSummary(): array
    {
        $database = $this->db;

        return [
            'quizzes'      => $database->table('quizzes')->countAllResults(),
            'materials'    => $database->table('materials')->where('is_active', 1)->countAllResults(),
            'questions'    => $database->table('questions')->where('is_active', 1)->countAllResults(),
            'participants' => $database->table('participants')->countAllResults(),
        ];
    }

    /** @return list<array<string, mixed>> */
    public function getRecentQuizzes(int $limit = 5): array
    {
        return $this->select('quizzes.id, quizzes.title, quizzes.status, quizzes.duration_minutes, quizzes.created_at, materials.title AS material_title')
            ->join('materials', 'materials.id = quizzes.material_id')
            ->orderBy('quizzes.created_at', 'DESC')
            ->findAll($limit);
    }

    /** @return list<array<string, mixed>> */
    public function getActiveSessions(int $limit = 4): array
    {
        return $this->db->table('quiz_sessions')
            ->select('quiz_sessions.id, quiz_sessions.session_name, quiz_sessions.pin, quiz_sessions.status, quizzes.title AS quiz_title, COUNT(participants.id) AS participant_count')
            ->join('quizzes', 'quizzes.id = quiz_sessions.quiz_id')
            ->join('participants', 'participants.session_id = quiz_sessions.id', 'left')
            ->whereIn('quiz_sessions.status', ['OPEN', 'WAITING'])
            ->groupBy('quiz_sessions.id')
            ->orderBy('quiz_sessions.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
