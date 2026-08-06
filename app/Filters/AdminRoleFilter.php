<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class AdminRoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! in_array(session('admin_role'), ['ADMIN', 'SUPERADMIN'], true)) {
            return redirect()->to(site_url('admin/dashboard'))
                ->with('error', 'Halaman tersebut hanya dapat diakses oleh administrator.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
