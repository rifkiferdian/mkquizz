<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DashboardModel;

final class DashboardController extends BaseController
{
    public function index(): string
    {
        $dashboard = model(DashboardModel::class);

        return view('admin/dashboard/index', [
            'title'          => 'Dashboard',
            'summary'        => $dashboard->getSummary(),
            'recentQuizzes'  => $dashboard->getRecentQuizzes(),
            'activeSessions' => $dashboard->getActiveSessions(),
        ]);
    }
}
