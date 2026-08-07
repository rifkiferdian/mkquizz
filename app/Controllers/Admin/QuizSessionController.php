<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuizModel;
use App\Models\QuizSessionModel;
use App\Services\QuestionReportService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use DateTimeImmutable;
use DateTimeZone;
use Throwable;

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

    public function share(int $id): string
    {
        $quizSession = model(QuizSessionModel::class)->findAdminDetail($id);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        return view('admin/sessions/share', [
            'title'       => 'QR Peserta',
            'quizSession' => $quizSession,
        ]);
    }

    public function leaderboard(int $id): string
    {
        $sessionModel = model(QuizSessionModel::class);
        $quizSession = $sessionModel->findAdminDetail($id);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        return view('admin/sessions/leaderboard', [
            'title'        => 'Leaderboard Sesi',
            'subtitle'     => 'Peringkat dan hasil terbaik peserta pada sesi quiz.',
            'quizSession'  => $quizSession,
            'participants' => $sessionModel->getParticipants($id),
            'performance'  => $sessionModel->getPerformance($id),
        ]);
    }

    public function report(int $id): string
    {
        $sessionModel = model(QuizSessionModel::class);
        $quizSession = $sessionModel->findAdminDetail($id);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        $report = (new QuestionReportService())->prepare(
            $sessionModel->getQuestionAnalysis($id, (int) $quizSession['quiz_id']),
            $this->request->getGet('sort'),
        );

        return view('admin/sessions/report', [
            'title'           => 'Report Evaluasi Soal',
            'subtitle'        => 'Analisis jawaban benar dan salah untuk evaluasi kualitas soal.',
            'questions'       => $report['questions'],
            'summary'         => $report['summary'],
            'hardestQuestion' => $report['hardest_question'],
            'questionSort'    => $report['sort'],
            'reportContext'   => [
                'back_url'    => site_url('admin/sessions/' . $id),
                'back_label'  => 'Kembali ke Detail Sesi',
                'filter_url'  => site_url('admin/sessions/' . $id . '/report'),
                'scope_label' => $quizSession['session_name'],
                'quiz_title'  => $quizSession['quiz_title'],
                'note'        => 'Persentase dihitung dari seluruh attempt selesai pada sesi ini.',
            ],
        ]);
    }

    public function create(): string
    {
        $timezone = new DateTimeZone('Asia/Jakarta');

        return view('admin/sessions/form', [
            'title'          => 'Tambah Sesi Quiz',
            'subtitle'       => 'Atur jadwal, kapasitas, dan akses sesi quiz baru.',
            'session'        => null,
            'isQuizLocked'   => false,
            'quizzes'        => $this->availableQuizzes(),
            'selectedQuizId' => max(0, (int) $this->request->getGet('quiz_id')),
            'defaultStart'   => (new DateTimeImmutable('now', $timezone))->format('Y-m-d\\TH:i'),
        ]);
    }

    public function edit(int $id): string
    {
        $quizSession = model(QuizSessionModel::class)->findAdminDetail($id);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        $timezone = new DateTimeZone('Asia/Jakarta');

        return view('admin/sessions/form', [
            'title'          => 'Edit Sesi Quiz',
            'subtitle'       => 'Perbarui jadwal, kapasitas, dan pengaturan sesi quiz.',
            'session'        => $quizSession,
            'isQuizLocked'   => (int) $quizSession['participant_count'] > 0,
            'quizzes'        => $this->availableQuizzes((int) $quizSession['quiz_id']),
            'selectedQuizId' => (int) $quizSession['quiz_id'],
            'defaultStart'   => (new DateTimeImmutable($quizSession['pin_valid_from'], $timezone))->format('Y-m-d\\TH:i'),
        ]);
    }

    public function store(): RedirectResponse
    {
        if (! $this->validate($this->sessionRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $quizId = (int) $this->request->getPost('quiz_id');
        $quiz = model(QuizModel::class)->where('id', $quizId)->where('status', 'ACTIVE')->first();

        if ($quiz === null) {
            return redirect()->back()->withInput()->with('errors', ['quiz_id' => 'Sesi hanya dapat dibuat dari quiz yang aktif.']);
        }

        if (db_connect()->table('quiz_questions')->where('quiz_id', $quizId)->countAllResults() === 0) {
            return redirect()->back()->withInput()->with('errors', ['quiz_id' => 'Quiz harus memiliki minimal satu pertanyaan.']);
        }

        $timezone = new DateTimeZone('Asia/Jakarta');
        $scheduledAt = DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i', (string) $this->request->getPost('pin_valid_from'), $timezone);
        $dateErrors = DateTimeImmutable::getLastErrors();

        if ($scheduledAt === false || (is_array($dateErrors) && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))) {
            return redirect()->back()->withInput()->with('errors', ['pin_valid_from' => 'Jadwal mulai tidak valid.']);
        }

        $now = new DateTimeImmutable('now', $timezone);
        $status = (string) $this->request->getPost('status');

        if ($status === 'OPEN' && $scheduledAt > $now) {
            return redirect()->back()->withInput()->with('errors', ['status' => 'Sesi dengan jadwal mendatang belum dapat langsung dibuka.']);
        }

        $validMinutes = (int) $this->request->getPost('pin_valid_minutes');
        $maxParticipants = trim((string) $this->request->getPost('max_participants'));
        $session = [
            'quiz_id'             => $quizId,
            'session_name'        => trim((string) $this->request->getPost('session_name')),
            'pin_valid_minutes'   => $validMinutes,
            'pin_valid_from'      => $scheduledAt->format('Y-m-d H:i:s'),
            'pin_valid_until'     => $scheduledAt->modify('+' . $validMinutes . ' minutes')->format('Y-m-d H:i:s'),
            'max_participants'    => $maxParticipants === '' ? null : (int) $maxParticipants,
            'allow_duplicate_name'=> $this->request->getPost('allow_duplicate_name') === '1' ? 1 : 0,
            'status'              => $status,
            'created_by'          => (int) session('admin_id'),
            'opened_at'           => $status === 'OPEN' ? $now->format('Y-m-d H:i:s') : null,
            'closed_at'           => null,
        ];

        try {
            $sessionId = model(QuizSessionModel::class)->createWithCredentials($session);
        } catch (Throwable $exception) {
            log_message('error', 'Gagal membuat sesi quiz: {message}', ['message' => $exception->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'Sesi quiz belum dapat disimpan. Silakan coba kembali.');
        }

        return redirect()->to(site_url('admin/sessions/' . $sessionId))->with('success', 'Sesi quiz berhasil dibuat. PIN dan token akses sudah tersedia.');
    }

    public function update(int $id): RedirectResponse
    {
        $sessionModel = model(QuizSessionModel::class);
        $quizSession = $sessionModel->findAdminDetail($id);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        if (! $this->validate($this->sessionRules(true))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $quizId = (int) $this->request->getPost('quiz_id');
        $currentQuizId = (int) $quizSession['quiz_id'];

        if ((int) $quizSession['participant_count'] > 0 && $quizId !== $currentQuizId) {
            return redirect()->back()->withInput()->with('errors', ['quiz_id' => 'Quiz tidak dapat diganti karena sesi sudah memiliki peserta.']);
        }

        $quizModel = model(QuizModel::class);
        $quiz = $quizModel->find($quizId);

        if ($quiz === null || ($quizId !== $currentQuizId && $quiz['status'] !== 'ACTIVE')) {
            return redirect()->back()->withInput()->with('errors', ['quiz_id' => 'Pilih quiz aktif yang valid.']);
        }

        if (db_connect()->table('quiz_questions')->where('quiz_id', $quizId)->countAllResults() === 0) {
            return redirect()->back()->withInput()->with('errors', ['quiz_id' => 'Quiz harus memiliki minimal satu pertanyaan.']);
        }

        $timezone = new DateTimeZone('Asia/Jakarta');
        $scheduledAt = DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i', (string) $this->request->getPost('pin_valid_from'), $timezone);
        $dateErrors = DateTimeImmutable::getLastErrors();

        if ($scheduledAt === false || (is_array($dateErrors) && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))) {
            return redirect()->back()->withInput()->with('errors', ['pin_valid_from' => 'Jadwal mulai tidak valid.']);
        }

        $now = new DateTimeImmutable('now', $timezone);
        $status = (string) $this->request->getPost('status');

        if ($status === 'OPEN' && $scheduledAt > $now) {
            return redirect()->back()->withInput()->with('errors', ['status' => 'Sesi dengan jadwal mendatang belum dapat langsung dibuka.']);
        }

        $validMinutes = (int) $this->request->getPost('pin_valid_minutes');
        $maxParticipants = trim((string) $this->request->getPost('max_participants'));

        if ($maxParticipants !== '' && (int) $maxParticipants < (int) $quizSession['participant_count']) {
            return redirect()->back()->withInput()->with('errors', [
                'max_participants' => 'Kapasitas tidak boleh kurang dari jumlah peserta yang sudah bergabung.',
            ]);
        }

        $data = [
            'quiz_id'              => $quizId,
            'session_name'         => trim((string) $this->request->getPost('session_name')),
            'pin_valid_minutes'    => $validMinutes,
            'pin_valid_from'       => $scheduledAt->format('Y-m-d H:i:s'),
            'pin_valid_until'      => $scheduledAt->modify('+' . $validMinutes . ' minutes')->format('Y-m-d H:i:s'),
            'max_participants'     => $maxParticipants === '' ? null : (int) $maxParticipants,
            'allow_duplicate_name' => $this->request->getPost('allow_duplicate_name') === '1' ? 1 : 0,
            'status'               => $status,
            'opened_at'            => $status === 'OPEN'
                ? ($quizSession['opened_at'] ?: $now->format('Y-m-d H:i:s'))
                : $quizSession['opened_at'],
            'closed_at'            => $status === 'CLOSED'
                ? ($quizSession['closed_at'] ?: $now->format('Y-m-d H:i:s'))
                : null,
        ];

        try {
            if (! $sessionModel->update($id, $data)) {
                throw new \RuntimeException('Pembaruan sesi ditolak oleh model.');
            }
        } catch (Throwable $exception) {
            log_message('error', 'Gagal memperbarui sesi quiz {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Perubahan sesi quiz belum dapat disimpan. Silakan coba kembali.');
        }

        return redirect()->to(site_url('admin/sessions/' . $id))->with('success', 'Sesi quiz berhasil diperbarui.');
    }

    public function extendPin(int $id): RedirectResponse
    {
        if (! $this->validate([
            'additional_minutes' => 'required|is_natural_no_zero|less_than_equal_to[1440]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Tambahan waktu harus antara 1 sampai 1.440 menit.');
        }

        $additionalMinutes = (int) $this->request->getPost('additional_minutes');

        try {
            $validUntil = model(QuizSessionModel::class)->extendPinValidity($id, $additionalMinutes);
        } catch (Throwable $exception) {
            log_message('error', 'Gagal memperpanjang PIN sesi {id}: {message}', [
                'id'      => $id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->to(site_url('admin/sessions/' . $id))->with(
            'success',
            'Masa berlaku PIN berhasil ditambah ' . $additionalMinutes . ' menit hingga ' . date('d M Y, H:i', strtotime($validUntil)) . ' WIB.',
        );
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

    /** @return list<array<string, mixed>> */
    private function availableQuizzes(int $includeQuizId = 0): array
    {
        $quizModel = model(QuizModel::class);
        $quizModel
            ->select('quizzes.id, quizzes.title, quizzes.duration_minutes, quizzes.passing_score, materials.title AS material_title, materials.code AS material_code')
            ->select('(SELECT COUNT(*) FROM quiz_questions WHERE quiz_questions.quiz_id = quizzes.id) AS question_count', false)
            ->join('materials', 'materials.id = quizzes.material_id')
            ->groupStart()
                ->where('quizzes.status', 'ACTIVE');

        if ($includeQuizId > 0) {
            $quizModel->orWhere('quizzes.id', $includeQuizId);
        }

        return $quizModel
            ->groupEnd()
            ->orderBy('quizzes.title', 'ASC')
            ->findAll();
    }

    /** @return array<string, string> */
    private function sessionRules(bool $includeClosed = false): array
    {
        $statuses = $includeClosed ? 'DRAFT,WAITING,OPEN,CLOSED' : 'DRAFT,WAITING,OPEN';

        return [
            'quiz_id'              => 'required|is_natural_no_zero|is_not_unique[quizzes.id]',
            'session_name'         => 'required|max_length[200]',
            'pin_valid_from'       => 'required',
            'pin_valid_minutes'    => 'required|is_natural_no_zero|less_than_equal_to[10080]',
            'max_participants'     => 'permit_empty|is_natural_no_zero|less_than_equal_to[100000]',
            'allow_duplicate_name' => 'permit_empty|in_list[0,1]',
            'status'               => 'required|in_list[' . $statuses . ']',
        ];
    }
}
