<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;
use App\Services\ParticipantAccessService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use DomainException;
use Throwable;

final class QuizAccessController extends BaseController
{
    public function show(string $token): string
    {
        $quizSession = $this->findSession($token);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        return view('participant/access/join', [
            'title'       => 'Masuk Quiz',
            'quizSession' => $quizSession,
        ]);
    }

    public function join(string $token): RedirectResponse
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[150]',
            'pin'  => 'required|exact_length[6]|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = preg_replace('/\\s+/u', ' ', trim((string) $this->request->getPost('name'))) ?: '';
        $pin = trim((string) $this->request->getPost('pin'));
        $throttleKey = 'participant-join-' . hash('sha256', $this->request->getIPAddress() . ':' . $token);

        if (! service('throttler')->check($throttleKey, 10, MINUTE)) {
            return redirect()->back()->withInput()->with('error', 'Terlalu banyak percobaan. Silakan tunggu satu menit.');
        }

        try {
            $participant = (new ParticipantAccessService())->join(
                $token,
                $name,
                $pin,
                $this->request->getIPAddress(),
                mb_substr($this->request->getHeaderLine('User-Agent'), 0, 1000),
            );
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            log_message('error', 'Pendaftaran peserta gagal: {message}', ['message' => $exception->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'Data peserta belum dapat diproses. Silakan coba kembali.');
        }

        session()->set([
            'participant_id'         => $participant['id'],
            'participant_token'      => $participant['token'],
            'participant_name'       => $participant['name'],
            'participant_session_id' => $participant['session_id'],
        ]);

        return redirect()->to(site_url('quiz/' . rawurlencode($token) . '/lobby'));
    }

    public function lobby(string $token): string|RedirectResponse
    {
        $quizSession = $this->findSession($token);

        if ($quizSession === null) {
            throw PageNotFoundException::forPageNotFound('Sesi quiz tidak ditemukan.');
        }

        if ((int) session('participant_session_id') !== (int) $quizSession['id'] || ! session('participant_id')) {
            return redirect()->to(site_url('quiz/' . rawurlencode($token)))
                ->with('error', 'Silakan masukkan nama dan PIN terlebih dahulu.');
        }

        return view('participant/access/lobby', [
            'title'       => 'Siap Mengikuti Quiz',
            'quizSession' => $quizSession,
            'participant' => ['name' => (string) session('participant_name')],
        ]);
    }

    /** @return array<string, mixed>|null */
    private function findSession(string $token): ?array
    {
        $session = db_connect()->table('quiz_sessions')
            ->select('quiz_sessions.*, quizzes.title AS quiz_title, quizzes.description AS quiz_description, quizzes.duration_minutes, quizzes.passing_score, quizzes.status AS quiz_status, materials.title AS material_title, materials.code AS material_code')
            ->select('(SELECT COUNT(*) FROM quiz_questions WHERE quiz_questions.quiz_id = quizzes.id) AS question_count', false)
            ->join('quizzes', 'quizzes.id = quiz_sessions.quiz_id')
            ->join('materials', 'materials.id = quizzes.material_id')
            ->where('quiz_sessions.session_token', $token)
            ->get()
            ->getRowArray();

        return $session ?: null;
    }
}
