<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

final class MaterialModel extends Model
{
    protected $table = 'materials';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'code',
        'title',
        'description',
        'created_by',
        'is_active',
    ];

    protected $useTimestamps = true;

    /** @return list<array<string, mixed>> */
    public function getAdminList(string $search, string $status, int $limit, int $offset): array
    {
        return $this->applyFilters($this->adminListBuilder(), $search, $status)
            ->orderBy('materials.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function countAdminList(string $search, string $status): int
    {
        return $this->applyFilters($this->builder(), $search, $status)->countAllResults();
    }

    /** @return array{total: int, active: int, inactive: int} */
    public function getStatusSummary(): array
    {
        $row = $this->builder()
            ->select('COUNT(*) AS total, SUM(is_active = 1) AS active, SUM(is_active = 0) AS inactive', false)
            ->get()
            ->getRowArray();

        return [
            'total'    => (int) ($row['total'] ?? 0),
            'active'   => (int) ($row['active'] ?? 0),
            'inactive' => (int) ($row['inactive'] ?? 0),
        ];
    }

    public function codeIsUsed(string $code, ?int $exceptId = null): bool
    {
        $builder = $this->builder()->where('code', $code);

        if ($exceptId !== null) {
            $builder->where('id !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }

    public function hasRelations(int $id): bool
    {
        $questionCount = $this->db->table('questions')->where('material_id', $id)->countAllResults();
        $quizCount = $this->db->table('quizzes')->where('material_id', $id)->countAllResults();

        return $questionCount > 0 || $quizCount > 0;
    }

    private function adminListBuilder(): BaseBuilder
    {
        return $this->builder()
            ->select('materials.*, users.name AS creator_name')
            ->select('(SELECT COUNT(*) FROM questions WHERE questions.material_id = materials.id) AS question_count', false)
            ->select('(SELECT COUNT(*) FROM quizzes WHERE quizzes.material_id = materials.id) AS quiz_count', false)
            ->join('users', 'users.id = materials.created_by', 'left');
    }

    private function applyFilters(BaseBuilder $builder, string $search, string $status): BaseBuilder
    {
        if ($search !== '') {
            $builder->groupStart()
                ->like('materials.title', $search)
                ->orLike('materials.code', $search)
                ->groupEnd();
        }

        if ($status === 'active') {
            $builder->where('materials.is_active', 1);
        } elseif ($status === 'inactive') {
            $builder->where('materials.is_active', 0);
        }

        return $builder;
    }
}
