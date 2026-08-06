<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

final class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $builder = $this->db->table('users');
        $accounts = [
            [
                'name'     => 'Administrator',
                'email'    => 'admin@mkquizz.edu',
                'password' => 'admin123',
                'role'     => 'SUPERADMIN',
            ],
            [
                'name'     => 'Presenter Quiz',
                'email'    => 'presenter@mkquizz.edu',
                'password' => 'presenter123',
                'role'     => 'PRESENTER',
            ],
        ];

        foreach ($accounts as $account) {
            $existing = $builder->where('email', $account['email'])->get()->getRowArray();
            $now = date('Y-m-d H:i:s');
            $data = [
                'name'       => $account['name'],
                'email'      => $account['email'],
                'password'   => password_hash($account['password'], PASSWORD_DEFAULT),
                'role'       => $account['role'],
                'is_active'  => 1,
                'updated_at' => $now,
            ];

            if ($existing !== null) {
                $builder->where('id', $existing['id'])->update($data);
                continue;
            }

            $data['created_at'] = $now;
            $builder->insert($data);
        }
    }
}
