<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuizModel;
use App\Models\QuizSessionModel;
use CodeIgniter\Exceptions\PageNotFoundException;

final class QuizSessionController extends BaseController
{
    private const PER_PAGE = 10;

    public function index(): string
    {
        $filters = $this->filters();
        $sessionModel = model(QuizSessionModel::class);
        $total = $sessionModel->countAdminList($filters);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = max(1, min((int) ($this->request->getGet('page') ?: 1), $totalPages));

        return view('admin/sessions/index', [
            'title'       => 'Sesi Quiz',
            'subtitle'    => 'Pantau sesi, PIN, peserta, dan aktivitas pengerjaan quiz.',
            'sessions'    => $sessionModel->getAdminList($filters, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'summary'     => $sessionModel->getSummary(),
            'quizzes'     => model(QuizModel::class)->select('id, title')->orderBy('title', 'ASC')->findAll(),
            'filters'     => $filters,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'totalResult' => $total,
        ]);
    }

    public function show(int $id): string
    {
        $sessionModel = model(QuizSessionModel::class);
        $quizSession = $sessionModel->findAdminDetail($id);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        return view('admin/sessions/show', [
            'title'        => 'Detail Sesi Quiz',
            'subtitle'     => 'Informasi akses, peserta, dan performa sesi.',
            'quizSession'  => $quizSession,
            'participants' => $sessionModel->getParticipants($id),
            'performance'  => $sessionModel->getPerformance($id),
        ]);
    }

    /** @return array{search: string, quiz_id: int, status: string} */
    private function filters(): array
    {
        $status = strtoupper((string) $this->request->getGet('status'));

        return [
            'search'  => mb_substr(trim((string) $this->request->getGet('q')), 0, 200),
            'quiz_id' => max(0, (int) $this->request->getGet('quiz_id')),
            'status'  => in_array($status, ['DRAFT', 'WAITING', 'OPEN', 'CLOSED'], true) ? $status : '',
        ];
    }
}
