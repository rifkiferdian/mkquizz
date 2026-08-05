<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

final class QuestionModel extends Model
{
    protected $table = 'questions';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'material_id',
        'question_type',
        'question_text',
        'explanation',
        'default_score',
        'difficulty',
        'created_by',
        'is_active',
    ];

    protected $useTimestamps = true;

    /** @return list<array<string, mixed>> */
    public function getAdminList(array $filters, int $limit, int $offset): array
    {
        return $this->applyFilters($this->adminListBuilder(), $filters)
            ->orderBy('questions.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function countAdminList(array $filters): int
    {
        return $this->applyFilters($this->builder(), $filters)->countAllResults();
    }

    /** @return array{total: int, active: int, multiple_choice: int, true_false: int} */
    public function getSummary(): array
    {
        $row = $this->builder()
            ->select("COUNT(*) AS total, SUM(is_active = 1) AS active, SUM(question_type = 'MULTIPLE_CHOICE') AS multiple_choice, SUM(question_type = 'TRUE_FALSE') AS true_false", false)
            ->get()
            ->getRowArray();

        return [
            'total'           => (int) ($row['total'] ?? 0),
            'active'          => (int) ($row['active'] ?? 0),
            'multiple_choice' => (int) ($row['multiple_choice'] ?? 0),
            'true_false'      => (int) ($row['true_false'] ?? 0),
        ];
    }

    /** @return list<array<string, mixed>> */
    public function getOptions(int $questionId): array
    {
        return $this->db->table('question_options')
            ->where('question_id', $questionId)
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function hasAnswers(int $questionId): bool
    {
        return $this->db->table('participant_answers')->where('question_id', $questionId)->countAllResults() > 0;
    }

    public function hasUsage(int $questionId): bool
    {
        if ($this->db->table('quiz_questions')->where('question_id', $questionId)->countAllResults() > 0) {
            return true;
        }

        if ($this->db->table('attempt_questions')->where('question_id', $questionId)->countAllResults() > 0) {
            return true;
        }

        return $this->hasAnswers($questionId);
    }

    private function adminListBuilder(): BaseBuilder
    {
        return $this->builder()
            ->select('questions.*, materials.title AS material_title, materials.code AS material_code, users.name AS creator_name')
            ->select('(SELECT COUNT(*) FROM question_options WHERE question_options.question_id = questions.id) AS option_count', false)
            ->select('(SELECT option_text FROM question_options WHERE question_options.question_id = questions.id AND question_options.is_correct = 1 ORDER BY sort_order ASC LIMIT 1) AS correct_answer', false)
            ->join('materials', 'materials.id = questions.material_id')
            ->join('users', 'users.id = questions.created_by', 'left');
    }

    private function applyFilters(BaseBuilder $builder, array $filters): BaseBuilder
    {
        if ($filters['search'] !== '') {
            $builder->like('questions.question_text', $filters['search']);
        }

        if ($filters['material_id'] > 0) {
            $builder->where('questions.material_id', $filters['material_id']);
        }

        if ($filters['difficulty'] !== '') {
            $builder->where('questions.difficulty', $filters['difficulty']);
        }

        if ($filters['type'] !== '') {
            $builder->where('questions.question_type', $filters['type']);
        }

        if ($filters['status'] !== '') {
            $builder->where('questions.is_active', $filters['status'] === 'active' ? 1 : 0);
        }

        return $builder;
    }
}
