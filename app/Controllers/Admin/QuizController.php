<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use App\Models\QuizModel;
use CodeIgniter\Exceptions\PageNotFoundException;

final class QuizController extends BaseController
{
    private const PER_PAGE = 10;

    public function index(): string
    {
        $filters = $this->filters();
        $quizModel = model(QuizModel::class);
        $total = $quizModel->countAdminList($filters);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = max(1, min((int) ($this->request->getGet('page') ?: 1), $totalPages));

        return view('admin/quizzes/index', [
            'title'       => 'Quiz',
            'subtitle'    => 'Pantau daftar quiz, sesi, dan aktivitas pengerjaan.',
            'quizzes'     => $quizModel->getAdminList($filters, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'summary'     => $quizModel->getSummary(),
            'materials'   => model(MaterialModel::class)->orderBy('title', 'ASC')->findAll(),
            'filters'     => $filters,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'totalResult' => $total,
        ]);
    }

    public function show(int $id): string
    {
        $quizModel = model(QuizModel::class);
        $quiz = $quizModel->findAdminDetail($id);

        if ($quiz === null) {
            throw PageNotFoundException::forPageNotFound('Quiz tidak ditemukan.');
        }

        return view('admin/quizzes/show', [
            'title'       => 'Detail Quiz',
            'subtitle'    => 'Informasi, konfigurasi, pertanyaan, dan aktivitas quiz.',
            'quiz'        => $quiz,
            'questions'   => $quizModel->getQuizQuestions($id),
            'sessions'    => $quizModel->getSessions($id),
            'performance' => $quizModel->getPerformance($id),
        ]);
    }

    /** @return array{search: string, material_id: int, status: string} */
    private function filters(): array
    {
        $status = strtoupper((string) $this->request->getGet('status'));

        return [
            'search'      => mb_substr(trim((string) $this->request->getGet('q')), 0, 200),
            'material_id' => max(0, (int) $this->request->getGet('material_id')),
            'status'      => in_array($status, ['DRAFT', 'ACTIVE', 'INACTIVE'], true) ? $status : '',
        ];
    }
}
