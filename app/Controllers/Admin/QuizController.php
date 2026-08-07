<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use App\Models\QuestionModel;
use App\Models\QuizModel;
use App\Services\QuestionReportService;
use App\Services\QuizService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

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

    public function report(int $id): string
    {
        $quizModel = model(QuizModel::class);
        $quiz = $quizModel->findAdminDetail($id);

        if ($quiz === null) {
            throw PageNotFoundException::forPageNotFound('Quiz tidak ditemukan.');
        }

        $report = (new QuestionReportService())->prepare(
            $quizModel->getQuestionAnalysis($id),
            $this->request->getGet('sort'),
        );

        return view('admin/sessions/report', [
            'title'           => 'Report Evaluasi Quiz',
            'subtitle'        => 'Gabungan analisis jawaban dari seluruh sesi quiz.',
            'questions'       => $report['questions'],
            'summary'         => $report['summary'],
            'hardestQuestion' => $report['hardest_question'],
            'questionSort'    => $report['sort'],
            'reportContext'   => [
                'back_url'    => site_url('admin/quizzes/' . $id),
                'back_label'  => 'Kembali ke Detail Quiz',
                'filter_url'  => site_url('admin/quizzes/' . $id . '/report'),
                'scope_label' => 'Gabungan ' . (int) $quiz['session_count'] . ' sesi',
                'quiz_title'  => $quiz['title'],
                'note'        => 'Persentase dihitung dari seluruh attempt selesai pada semua sesi yang menggunakan quiz ini.',
            ],
        ]);
    }

    public function create(): string
    {
        return view('admin/quizzes/form', [
            'title'         => 'Tambah Quiz',
            'subtitle'      => 'Susun konfigurasi dan pilih pertanyaan untuk quiz baru.',
            'quiz'          => null,
            'isLocked'      => false,
            'materials'     => model(MaterialModel::class)->where('is_active', 1)->orderBy('title', 'ASC')->findAll(),
            'questions'     => $this->questionCandidates(),
            'quizQuestions' => [],
        ]);
    }

    public function edit(int $id): string
    {
        $quizModel = model(QuizModel::class);
        $quiz = $quizModel->findAdminDetail($id);

        if ($quiz === null) {
            throw PageNotFoundException::forPageNotFound('Quiz tidak ditemukan.');
        }

        return view('admin/quizzes/form', [
            'title'         => 'Edit Quiz',
            'subtitle'      => 'Perbarui informasi, pengaturan, dan pertanyaan quiz.',
            'quiz'          => $quiz,
            'isLocked'      => (int) $quiz['attempt_count'] > 0,
            'materials'     => model(MaterialModel::class)->orderBy('title', 'ASC')->findAll(),
            'questions'     => $this->questionCandidates(),
            'quizQuestions' => $quizModel->getQuizQuestions($id),
        ]);
    }

    public function store(): RedirectResponse
    {
        if (! $this->validate($this->quizRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $materialId = (int) $this->request->getPost('material_id');
        $material = model(MaterialModel::class)->where('id', $materialId)->where('is_active', 1)->first();
        $questionIds = array_values(array_unique(array_filter(
            array_map('intval', (array) $this->request->getPost('question_ids')),
            static fn (int $id): bool => $id > 0,
        )));

        if ($material === null) {
            return redirect()->back()->withInput()->with('errors', ['material_id' => 'Material harus aktif dan tersedia.']);
        }

        if ($questionIds === []) {
            return redirect()->back()->withInput()->with('errors', ['question_ids' => 'Pilih minimal satu pertanyaan untuk quiz.']);
        }

        $questionModel = model(QuestionModel::class);
        $questionRows = $questionModel->select('id, default_score')
            ->where('material_id', $materialId)
            ->where('is_active', 1)
            ->whereIn('id', $questionIds)
            ->findAll();

        if (count($questionRows) !== count($questionIds)) {
            return redirect()->back()->withInput()->with('errors', ['question_ids' => 'Semua pertanyaan harus aktif dan berasal dari material yang dipilih.']);
        }

        $questionsById = [];
        foreach ($questionRows as $question) {
            $questionsById[(int) $question['id']] = $question;
        }
        $selectedQuestions = array_map(static fn (int $id): array => $questionsById[$id], $questionIds);

        $quiz = [
            'material_id'         => $materialId,
            'title'               => trim((string) $this->request->getPost('title')),
            'description'         => trim((string) $this->request->getPost('description')) ?: null,
            'duration_minutes'    => (int) $this->request->getPost('duration_minutes'),
            'passing_score'       => (float) $this->request->getPost('passing_score'),
            'shuffle_questions'   => $this->booleanPost('shuffle_questions'),
            'shuffle_options'     => $this->booleanPost('shuffle_options'),
            'show_score'          => $this->booleanPost('show_score'),
            'show_correct_answer' => $this->booleanPost('show_correct_answer'),
            'show_explanation'    => $this->booleanPost('show_explanation'),
            'allow_review'        => $this->booleanPost('allow_review'),
            'created_by'          => (int) session('admin_id'),
            'status'              => (string) $this->request->getPost('status'),
        ];

        try {
            $quizId = (new QuizService())->create($quiz, $selectedQuestions);
        } catch (Throwable $exception) {
            log_message('error', 'Gagal membuat quiz: {message}', ['message' => $exception->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'Quiz belum dapat disimpan. Silakan coba kembali.');
        }

        return redirect()->to(site_url('admin/quizzes/' . $quizId))->with('success', 'Quiz berhasil dibuat.');
    }

    public function update(int $id): RedirectResponse
    {
        $quizModel = model(QuizModel::class);
        $existingQuiz = $quizModel->findAdminDetail($id);

        if ($existingQuiz === null) {
            throw PageNotFoundException::forPageNotFound('Quiz tidak ditemukan.');
        }

        if (! $this->validate($this->quizRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $isLocked = (int) $existingQuiz['attempt_count'] > 0;
        $materialId = (int) $this->request->getPost('material_id');

        if ($isLocked && $materialId !== (int) $existingQuiz['material_id']) {
            return redirect()->back()->withInput()->with('errors', ['material_id' => 'Material tidak dapat diganti karena quiz sudah memiliki riwayat pengerjaan.']);
        }

        $material = model(MaterialModel::class)->find($materialId);
        if ($material === null || (! (bool) $material['is_active'] && $materialId !== (int) $existingQuiz['material_id'])) {
            return redirect()->back()->withInput()->with('errors', ['material_id' => 'Material harus aktif dan tersedia.']);
        }

        $selectedQuestions = [];
        if (! $isLocked) {
            $questionIds = array_values(array_unique(array_filter(
                array_map('intval', (array) $this->request->getPost('question_ids')),
                static fn (int $questionId): bool => $questionId > 0,
            )));

            if ($questionIds === []) {
                return redirect()->back()->withInput()->with('errors', ['question_ids' => 'Pilih minimal satu pertanyaan untuk quiz.']);
            }

            $questionRows = model(QuestionModel::class)->select('id, default_score')
                ->where('material_id', $materialId)
                ->where('is_active', 1)
                ->whereIn('id', $questionIds)
                ->findAll();

            if (count($questionRows) !== count($questionIds)) {
                return redirect()->back()->withInput()->with('errors', ['question_ids' => 'Semua pertanyaan harus aktif dan berasal dari material yang dipilih.']);
            }

            $questionsById = [];
            foreach ($questionRows as $question) {
                $questionsById[(int) $question['id']] = $question;
            }
            $selectedQuestions = array_map(static fn (int $questionId): array => $questionsById[$questionId], $questionIds);
        }

        $description = trim((string) $this->request->getPost('description'));
        $quiz = [
            'material_id'         => $materialId,
            'title'               => trim((string) $this->request->getPost('title')),
            'description'         => $description !== '' ? $description : null,
            'duration_minutes'    => (int) $this->request->getPost('duration_minutes'),
            'passing_score'       => (float) $this->request->getPost('passing_score'),
            'shuffle_questions'   => $this->booleanPost('shuffle_questions'),
            'shuffle_options'     => $this->booleanPost('shuffle_options'),
            'show_score'          => $this->booleanPost('show_score'),
            'show_correct_answer' => $this->booleanPost('show_correct_answer'),
            'show_explanation'    => $this->booleanPost('show_explanation'),
            'allow_review'        => $this->booleanPost('allow_review'),
            'status'              => (string) $this->request->getPost('status'),
        ];

        try {
            (new QuizService())->update($id, $quiz, $selectedQuestions, ! $isLocked);
        } catch (Throwable $exception) {
            log_message('error', 'Gagal memperbarui quiz {id}: {message}', ['id' => $id, 'message' => $exception->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'Perubahan quiz belum dapat disimpan. Silakan coba kembali.');
        }

        return redirect()->to(site_url('admin/quizzes/' . $id))->with('success', 'Quiz berhasil diperbarui.');
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

    /** @return list<array<string, mixed>> */
    private function questionCandidates(): array
    {
        return model(QuestionModel::class)
            ->select('questions.id, questions.material_id, questions.question_text, questions.question_type, questions.difficulty, questions.default_score, materials.title AS material_title, materials.code AS material_code')
            ->join('materials', 'materials.id = questions.material_id')
            ->where('questions.is_active', 1)
            ->where('materials.is_active', 1)
            ->orderBy('materials.title', 'ASC')
            ->orderBy('questions.created_at', 'DESC')
            ->findAll();
    }

    private function booleanPost(string $field): int
    {
        return $this->request->getPost($field) === '1' ? 1 : 0;
    }

    /** @return array<string, string> */
    private function quizRules(): array
    {
        return [
            'material_id'         => 'required|is_natural_no_zero|is_not_unique[materials.id]',
            'title'               => 'required|max_length[200]',
            'description'         => 'permit_empty|max_length[5000]',
            'duration_minutes'    => 'required|is_natural_no_zero|less_than_equal_to[480]',
            'passing_score'       => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'status'              => 'required|in_list[DRAFT,ACTIVE,INACTIVE]',
            'shuffle_questions'   => 'permit_empty|in_list[0,1]',
            'shuffle_options'     => 'permit_empty|in_list[0,1]',
            'show_score'          => 'permit_empty|in_list[0,1]',
            'show_correct_answer' => 'permit_empty|in_list[0,1]',
            'show_explanation'    => 'permit_empty|in_list[0,1]',
            'allow_review'        => 'permit_empty|in_list[0,1]',
        ];
    }
}
