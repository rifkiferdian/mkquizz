<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

final class MaterialController extends BaseController
{
    private const PER_PAGE = 10;

    public function index(): string
    {
        $search = trim((string) $this->request->getGet('q'));
        $status = (string) $this->request->getGet('status');
        $status = in_array($status, ['active', 'inactive'], true) ? $status : '';
        $materialModel = model(MaterialModel::class);
        $total = $materialModel->countAdminList($search, $status);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = max(1, min((int) ($this->request->getGet('page') ?: 1), $totalPages));

        return view('admin/materials/index', [
            'title'       => 'Materials',
            'subtitle'    => 'Kelola materi sebagai dasar penyusunan quiz.',
            'materials'   => $materialModel->getAdminList($search, $status, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'summary'     => $materialModel->getStatusSummary(),
            'search'      => $search,
            'status'      => $status,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'totalResult' => $total,
        ]);
    }

    public function create(): string
    {
        return view('admin/materials/form', [
            'title'    => 'Tambah Material',
            'subtitle' => 'Buat materi baru untuk mengelompokkan pertanyaan dan quiz.',
            'material' => null,
        ]);
    }

    public function store(): RedirectResponse
    {
        $data = $this->validatedData();

        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $materialModel = model(MaterialModel::class);

        if ($data['code'] !== null && $materialModel->codeIsUsed($data['code'])) {
            return redirect()->back()->withInput()->with('errors', ['code' => 'Kode material sudah digunakan.']);
        }

        $data['created_by'] = (int) session('admin_id');
        $materialModel->insert($data);

        return redirect()->to(site_url('admin/materials'))->with('success', 'Material berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/materials/form', [
            'title'    => 'Edit Material',
            'subtitle' => 'Perbarui informasi material yang dipilih.',
            'material' => $this->findMaterial($id),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $this->findMaterial($id);
        $data = $this->validatedData();

        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $materialModel = model(MaterialModel::class);

        if ($data['code'] !== null && $materialModel->codeIsUsed($data['code'], $id)) {
            return redirect()->back()->withInput()->with('errors', ['code' => 'Kode material sudah digunakan.']);
        }

        $materialModel->update($id, $data);

        return redirect()->to(site_url('admin/materials'))->with('success', 'Material berhasil diperbarui.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $material = $this->findMaterial($id);
        $newStatus = (int) ! (bool) $material['is_active'];
        model(MaterialModel::class)->update($id, ['is_active' => $newStatus]);

        return redirect()->to(site_url('admin/materials'))->with('success', 'Status material berhasil diubah.');
    }

    public function delete(int $id): RedirectResponse
    {
        $materialModel = model(MaterialModel::class);
        $material = $this->findMaterial($id);

        if ($materialModel->hasRelations($id)) {
            return redirect()->to(site_url('admin/materials'))->with('error', 'Material “' . $material['title'] . '” masih digunakan oleh pertanyaan atau quiz.');
        }

        $materialModel->delete($id);

        return redirect()->to(site_url('admin/materials'))->with('success', 'Material berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function findMaterial(int $id): array
    {
        $material = model(MaterialModel::class)->find($id);

        if ($material === null) {
            throw PageNotFoundException::forPageNotFound('Material tidak ditemukan.');
        }

        return $material;
    }

    /** @return array{code: ?string, title: string, description: ?string, is_active: int}|null */
    private function validatedData(): ?array
    {
        $rules = [
            'code'        => 'permit_empty|max_length[30]|regex_match[/^[A-Za-z0-9_-]+$/]',
            'title'       => 'required|min_length[3]|max_length[200]',
            'description' => 'permit_empty|max_length[2000]',
        ];

        if (! $this->validate($rules)) {
            return null;
        }

        $code = strtoupper(trim((string) $this->request->getPost('code')));
        $description = trim((string) $this->request->getPost('description'));

        return [
            'code'        => $code !== '' ? $code : null,
            'title'       => trim((string) $this->request->getPost('title')),
            'description' => $description !== '' ? $description : null,
            'is_active'   => $this->request->getPost('is_active') === '1' ? 1 : 0,
        ];
    }
}
