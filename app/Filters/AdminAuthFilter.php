<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('admin_logged_in')) {
            return redirect()->to(site_url('admin/login'))
                ->with('error', 'Silakan masuk untuk mengakses dashboard.');
        }

        if (! in_array($session->get('admin_role'), ['ADMIN', 'SUPERADMIN', 'PRESENTER'], true)) {
            $session->destroy();

            return redirect()->to(site_url('admin/login'))
                ->with('error', 'Akun Anda tidak memiliki akses panel.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
