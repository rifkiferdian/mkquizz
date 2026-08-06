<?php

namespace App\Models;

use CodeIgniter\Model;

final class UserModel extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $useTimestamps = true;

    public function findActivePanelUserByEmail(string $email): ?array
    {
        $user = $this->where('email', strtolower($email))
            ->where('is_active', 1)
            ->whereIn('role', ['ADMIN', 'SUPERADMIN', 'PRESENTER'])
            ->first();

        return $user ?: null;
    }
}
