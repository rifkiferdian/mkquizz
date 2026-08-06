<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ParticipantModel;
use App\Models\QuizModel;
use App\Models\QuizSessionModel;
use CodeIgniter\Exceptions\PageNotFoundException;

final class ParticipantController extends BaseController
{
    private const PER_PAGE = 10;

    public function index(): string
    {
        $filters = $this->filters();
        $participantModel = model(ParticipantModel::class);
        $total = $participantModel->countAdminList($filters);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = max(1, min((int) ($this->request->getGet('page') ?: 1), $totalPages));

        return view('admin/participants/index', [
            'title'        => 'Peserta',
            'subtitle'     => 'Pantau peserta, sesi, pengerjaan, dan hasil quiz.',
            'participants' => $participantModel->getAdminList($filters, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'summary'      => $participantModel->getSummary(),
            'quizzes'      => model(QuizModel::class)->select('id, title')->orderBy('title', 'ASC')->findAll(),
            'sessions'     => model(QuizSessionModel::class)->select('id, session_name')->orderBy('session_name', 'ASC')->findAll(),
            'filters'      => $filters,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'totalResult'  => $total,
        ]);
    }

    public function show(int $id): string
    {
        $participantModel = model(ParticipantModel::class);
        $participant = $participantModel->findAdminDetail($id);

        if ($participant === null) {
            throw PageNotFoundException::forPageNotFound('Peserta tidak ditemukan.');
        }

        return view('admin/participants/show', [
            'title'       => 'Detail Peserta',
            'subtitle'    => 'Identitas, aktivitas, dan hasil pengerjaan peserta.',
            'participant' => $participant,
            'attempts'    => $participantModel->getAttempts($id),
            'performance' => $participantModel->getPerformance($id),
        ]);
    }

    /** @return array{search: string, quiz_id: int, session_id: int, activity: string} */
    private function filters(): array
    {
        $activity = strtoupper((string) $this->request->getGet('activity'));

        return [
            'search'     => mb_substr(trim((string) $this->request->getGet('q')), 0, 200),
            'quiz_id'    => max(0, (int) $this->request->getGet('quiz_id')),
            'session_id' => max(0, (int) $this->request->getGet('session_id')),
            'activity'   => in_array($activity, ['NOT_STARTED', 'IN_PROGRESS', 'COMPLETED'], true) ? $activity : '',
        ];
    }
}
