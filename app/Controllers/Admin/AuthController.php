<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

final class AuthController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        return view('admin/auth/login', [
            'title' => 'Login Admin',
        ]);
    }

    public function login(): RedirectResponse
    {
        $rules = [
            'email'    => 'required|valid_email|max_length[150]',
            'password' => 'required|min_length[8]|max_length[72]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $key = hash('sha256', $this->request->getIPAddress() . ':' . $email);

        if (! service('throttler')->check('admin-login-' . $key, 5, MINUTE)) {
            return redirect()->back()->withInput()
                ->with('error', 'Terlalu banyak percobaan. Silakan coba lagi dalam satu menit.');
        }

        $user = model(UserModel::class)->findActiveAdminByEmail($email);
        $password = (string) $this->request->getPost('password');

        if ($user === null || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Email atau password tidak sesuai.');
        }

        session()->regenerate(true);
        session()->set([
            'admin_id'        => (int) $user['id'],
            'admin_name'      => $user['name'],
            'admin_email'     => $user['email'],
            'admin_role'      => $user['role'],
            'admin_logged_in' => true,
        ]);

        return redirect()->to(site_url('admin/dashboard'))
            ->with('success', 'Selamat datang kembali, ' . $user['name'] . '!');
    }

    public function logout(): RedirectResponse
    {
        $session = session();
        $session->remove([
            'admin_id',
            'admin_name',
            'admin_email',
            'admin_role',
            'admin_logged_in',
        ]);
        $session->regenerate(true);

        return redirect()->to(site_url('admin/login'))
            ->with('success', 'Anda telah berhasil keluar.');
    }
}
