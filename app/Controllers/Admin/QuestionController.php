<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use App\Models\QuestionModel;
use App\Services\QuestionService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use RuntimeException;

final class QuestionController extends BaseController
{
    private const PER_PAGE = 10;

    public function index(): string
    {
        $filters = $this->listFilters();
        $questionModel = model(QuestionModel::class);
        $total = $questionModel->countAdminList($filters);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = max(1, min((int) ($this->request->getGet('page') ?: 1), $totalPages));

        return view('admin/questions/index', [
            'title'       => 'Pertanyaan',
            'subtitle'    => 'Kelola bank soal dan pilihan jawaban quiz.',
            'questions'   => $questionModel->getAdminList($filters, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'summary'     => $questionModel->getSummary(),
            'materials'   => model(MaterialModel::class)->orderBy('title', 'ASC')->findAll(),
            'filters'     => $filters,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'totalResult' => $total,
        ]);
    }

    public function create(): string
    {
        return $this->formView(null, [], $this->materialContextId($this->request->getGet('material_id')));
    }

    public function store(): RedirectResponse
    {
        [$question, $options, $correctIndex, $errors] = $this->validatedPayload();

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $question['created_by'] = (int) session('admin_id');

        try {
            (new QuestionService())->create($question, $options, $correctIndex);
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        $returnMaterialId = $this->materialContextId($this->request->getPost('return_material_id'));

        return redirect()->to($this->questionListUrl($returnMaterialId))
            ->with('success', 'Pertanyaan dan pilihan jawaban berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $questionModel = model(QuestionModel::class);

        return $this->formView($this->findQuestion($id), $questionModel->getOptions($id));
    }

    public function update(int $id): RedirectResponse
    {
        $this->findQuestion($id);
        $questionModel = model(QuestionModel::class);

        if ($questionModel->hasAnswers($id)) {
            return redirect()->back()->with('error', 'Pertanyaan sudah dijawab peserta sehingga tidak dapat diubah untuk menjaga riwayat hasil.');
        }

        [$question, $options, $correctIndex, $errors] = $this->validatedPayload();

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        try {
            (new QuestionService())->update($id, $question, $options, $correctIndex);
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->to(site_url('admin/questions'))->with('success', 'Pertanyaan dan pilihan jawaban berhasil diperbarui.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $question = $this->findQuestion($id);
        model(QuestionModel::class)->update($id, ['is_active' => (int) ! (bool) $question['is_active']]);

        return redirect()->to(site_url('admin/questions'))->with('success', 'Status pertanyaan berhasil diubah.');
    }

    public function delete(int $id): RedirectResponse
    {
        $question = $this->findQuestion($id);
        $questionModel = model(QuestionModel::class);

        if ($questionModel->hasUsage($id)) {
            return redirect()->to(site_url('admin/questions'))->with('error', 'Pertanyaan ini masih digunakan oleh quiz atau riwayat peserta sehingga tidak dapat dihapus.');
        }

        $questionModel->delete($question['id']);

        return redirect()->to(site_url('admin/questions'))->with('success', 'Pertanyaan dan pilihan jawabannya berhasil dihapus.');
    }

    private function formView(?array $question, array $options, int $materialContextId = 0): string
    {
        return view('admin/questions/form', [
            'title'             => $question === null ? 'Tambah Pertanyaan' : 'Edit Pertanyaan',
            'subtitle'          => 'Susun soal, pilihan jawaban, dan kunci jawaban.',
            'question'          => $question,
            'options'           => $options,
            'materials'         => model(MaterialModel::class)->orderBy('title', 'ASC')->findAll(),
            'materialContextId' => $materialContextId,
        ]);
    }

    private function materialContextId(mixed $value): int
    {
        $materialId = max(0, (int) $value);

        return $materialId > 0 && model(MaterialModel::class)->find($materialId) !== null ? $materialId : 0;
    }

    private function questionListUrl(int $materialId): string
    {
        return site_url('admin/questions') . ($materialId > 0 ? '?' . http_build_query(['material_id' => $materialId]) : '');
    }

    /** @return array<string, mixed> */
    private function findQuestion(int $id): array
    {
        $question = model(QuestionModel::class)->find($id);

        if ($question === null) {
            throw PageNotFoundException::forPageNotFound('Pertanyaan tidak ditemukan.');
        }

        return $question;
    }

    /** @return array{search: string, material_id: int, difficulty: string, type: string, status: string} */
    private function listFilters(): array
    {
        $difficulty = strtoupper((string) $this->request->getGet('difficulty'));
        $type = strtoupper((string) $this->request->getGet('type'));
        $status = (string) $this->request->getGet('status');

        return [
            'search'      => mb_substr(trim((string) $this->request->getGet('q')), 0, 200),
            'material_id' => max(0, (int) $this->request->getGet('material_id')),
            'difficulty'  => in_array($difficulty, ['EASY', 'MEDIUM', 'HARD'], true) ? $difficulty : '',
            'type'        => in_array($type, ['MULTIPLE_CHOICE', 'TRUE_FALSE'], true) ? $type : '',
            'status'      => in_array($status, ['active', 'inactive'], true) ? $status : '',
        ];
    }

    /** @return array{0: array<string, mixed>, 1: list<string>, 2: int, 3: array<string, string>} */
    private function validatedPayload(): array
    {
        $validation = service('validation');
        $validation->setRules([
            'material_id'   => 'required|is_natural_no_zero',
            'question_type' => 'required|in_list[MULTIPLE_CHOICE,TRUE_FALSE]',
            'question_text' => 'required|min_length[5]|max_length[5000]',
            'explanation'   => 'permit_empty|max_length[5000]',
            'default_score' => 'required|decimal|greater_than[0]|less_than_equal_to[10000]',
            'difficulty'    => 'required|in_list[EASY,MEDIUM,HARD]',
        ]);

        $postData = $this->request->getPost();
        $errors = $validation->run($postData) ? [] : $validation->getErrors();
        $materialId = (int) ($postData['material_id'] ?? 0);

        if ($materialId > 0 && model(MaterialModel::class)->find($materialId) === null) {
            $errors['material_id'] = 'Material yang dipilih tidak ditemukan.';
        }

        $type = (string) ($postData['question_type'] ?? 'MULTIPLE_CHOICE');
        $rawOptions = array_values((array) ($postData['option_text'] ?? []));
        $correctIndex = filter_var($postData['correct_option'] ?? null, FILTER_VALIDATE_INT);

        if ($type === 'TRUE_FALSE') {
            $rawOptions = ['Benar', 'Salah'];
        }

        if (count($rawOptions) < 2 || count($rawOptions) > 5) {
            $errors['options'] = 'Pilihan jawaban harus berjumlah 2 sampai 5.';
        }

        $options = [];
        foreach ($rawOptions as $index => $option) {
            $option = trim((string) $option);
            if ($option === '') {
                $errors['options'] = 'Semua pilihan jawaban wajib diisi.';
            } elseif (mb_strlen($option) > 2000) {
                $errors['options'] = 'Setiap pilihan jawaban maksimal 2.000 karakter.';
            }
            $options[$index] = $option;
        }

        if ($correctIndex === false || ! array_key_exists($correctIndex, $options)) {
            $errors['correct_option'] = 'Pilih satu jawaban yang benar.';
            $correctIndex = 0;
        }

        $explanation = trim((string) ($postData['explanation'] ?? ''));

        return [[
            'material_id'   => $materialId,
            'question_type' => $type,
            'question_text' => trim((string) ($postData['question_text'] ?? '')),
            'explanation'   => $explanation !== '' ? $explanation : null,
            'default_score' => (float) ($postData['default_score'] ?? 0),
            'difficulty'    => (string) ($postData['difficulty'] ?? ''),
            'is_active'     => ($postData['is_active'] ?? '0') === '1' ? 1 : 0,
        ], $options, (int) $correctIndex, $errors];
    }
}
