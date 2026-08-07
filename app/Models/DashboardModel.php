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

    /** @return array{submitted: int, average: float, passed: int, pass_rate: float, open_sessions: int} */
    public function getPerformanceSummary(): array
    {
        $row = $this->db->table('quiz_attempts')
            ->select("COUNT(*) AS submitted, AVG(final_score) AS average, SUM(passed = 1) AS passed", false)
            ->where('status', 'SUBMITTED')
            ->get()
            ->getRowArray();
        $submitted = (int) ($row['submitted'] ?? 0);
        $passed = (int) ($row['passed'] ?? 0);

        return [
            'submitted'     => $submitted,
            'average'       => round((float) ($row['average'] ?? 0), 1),
            'passed'        => $passed,
            'pass_rate'     => $submitted > 0 ? round(($passed / $submitted) * 100, 1) : 0.0,
            'open_sessions' => $this->db->table('quiz_sessions')->where('status', 'OPEN')->countAllResults(),
        ];
    }

    /** @return list<array<string, mixed>> */
    public function getDifficultQuestions(int $limit = 5): array
    {
        return $this->db->table('participant_answers')
            ->select('questions.id, questions.question_text, materials.title AS material_title')
            ->select('COUNT(participant_answers.id) AS response_count', false)
            ->select('ROUND(SUM(participant_answers.is_correct = 1) * 100 / COUNT(participant_answers.id), 0) AS accuracy', false)
            ->join('quiz_attempts', 'quiz_attempts.id = participant_answers.attempt_id')
            ->join('questions', 'questions.id = participant_answers.question_id')
            ->join('materials', 'materials.id = questions.material_id')
            ->where('quiz_attempts.status', 'SUBMITTED')
            ->groupBy('questions.id, questions.question_text, materials.title')
            ->orderBy('accuracy', 'ASC')
            ->orderBy('response_count', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
