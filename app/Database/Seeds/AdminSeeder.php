<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

final class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@mkquizz.edu';
        $data = [
            'name'       => 'Administrator',
            'email'      => $email,
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'role'       => 'SUPERADMIN',
            'is_active'  => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $builder = $this->db->table('users');
        $existing = $builder->where('email', $email)->get()->getRowArray();

        if ($existing !== null) {
            $builder->where('id', $existing['id'])->update([
                'password'   => $data['password'],
                'updated_at' => $data['updated_at'],
            ]);

            return;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $builder->insert($data);
    }
}
