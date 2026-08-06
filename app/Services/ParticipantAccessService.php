<?php

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use RuntimeException;

final class ParticipantAccessService
{
    /** @return array{id: int, token: string, name: string, session_id: int} */
    public function join(string $sessionToken, string $name, string $pin, ?string $ipAddress, ?string $userAgent): array
    {
        $database = db_connect();
        $database->transBegin();

        $quizSession = $database->query(
            'SELECT * FROM quiz_sessions WHERE session_token = ? FOR UPDATE',
            [$sessionToken],
        )->getRowArray();

        if ($quizSession === null) {
            $database->transRollback();
            throw new DomainException('Sesi quiz tidak ditemukan.');
        }

        $this->assertAccessible($quizSession, $pin);

        $participants = $database->table('participants');
        if ($quizSession['max_participants'] !== null
            && $participants->where('session_id', $quizSession['id'])->countAllResults() >= (int) $quizSession['max_participants']) {
            $database->transRollback();
            throw new DomainException('Kapasitas peserta pada sesi ini sudah penuh.');
        }

        if (! (bool) $quizSession['allow_duplicate_name']) {
            $duplicate = $participants
                ->where('session_id', $quizSession['id'])
                ->where('LOWER(name)', mb_strtolower($name))
                ->countAllResults() > 0;

            if ($duplicate) {
                $database->transRollback();
                throw new DomainException('Nama tersebut sudah terdaftar pada sesi ini. Gunakan nama lengkap yang berbeda.');
            }
        }

        do {
            $participantToken = 'PART-' . strtoupper(bin2hex(random_bytes(16)));
        } while ($participants->where('participant_token', $participantToken)->countAllResults() > 0);

        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
        $participants->insert([
            'session_id'        => (int) $quizSession['id'],
            'name'              => $name,
            'participant_token' => $participantToken,
            'ip_address'        => $ipAddress,
            'user_agent'        => $userAgent,
            'joined_at'         => $now->format('Y-m-d H:i:s'),
        ]);
        $participantId = (int) $database->insertID();

        if (! $database->transStatus()) {
            $database->transRollback();
            throw new RuntimeException('Data peserta gagal disimpan.');
        }

        $database->transCommit();

        return [
            'id'         => $participantId,
            'token'      => $participantToken,
            'name'       => $name,
            'session_id' => (int) $quizSession['id'],
        ];
    }

    private function assertAccessible(array $quizSession, string $pin): void
    {
        if ($quizSession['status'] !== 'OPEN') {
            throw new DomainException('Sesi quiz belum dibuka atau sudah ditutup.');
        }

        $timezone = new DateTimeZone('Asia/Jakarta');
        $now = new DateTimeImmutable('now', $timezone);
        $validFrom = new DateTimeImmutable($quizSession['pin_valid_from'], $timezone);
        $validUntil = new DateTimeImmutable($quizSession['pin_valid_until'], $timezone);

        if ($now < $validFrom) {
            throw new DomainException('PIN sesi belum mulai berlaku.');
        }

        if ($now > $validUntil) {
            throw new DomainException('PIN sesi sudah kedaluwarsa. Silakan hubungi presenter.');
        }

        if (! hash_equals((string) $quizSession['pin'], $pin)) {
            throw new DomainException('PIN quiz tidak sesuai.');
        }
    }
}
