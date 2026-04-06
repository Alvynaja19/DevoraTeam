<?php

namespace App\Console\Commands;

use App\Models\FcmToken;
use App\Models\LoanItem;
use App\Models\MemberNotification;
use App\Services\FcmService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendLoanReminders extends Command
{
    protected $signature   = 'loans:send-reminders';
    protected $description = 'Kirim push notification reminder pengembalian buku';

    public function handle(FcmService $fcm): void
    {
        $today = Carbon::today();

        // Ambil semua item pinjaman yang belum dikembalikan
        $activeItems = LoanItem::with(['loan.member.user', 'copy.book'])
            ->whereNull('returned_at')
            ->whereHas('loan', fn($q) => $q->whereIn('status', ['aktif', 'diperpanjang', 'terlambat']))
            ->get();

        $this->info("Checking {$activeItems->count()} active loan items...");

        foreach ($activeItems as $item) {
            $dueDate = $item->loan?->effectiveDueDate();
            if (!$dueDate) continue;

            $dueDate  = Carbon::parse($dueDate)->startOfDay();
            $diff     = $today->diffInDays($dueDate, false); // negatif = sudah lewat
            $member   = $item->loan->member;
            $bookTitle = $item->copy?->book?->title ?? 'Buku';

            if (!$member) continue;

            if ($diff === 1) {
                // H-1: reminder pengembalian
                $this->sendNotification(
                    $fcm, $member, $item, 'reminder_pengembalian',
                    '⏰ Pengingat Pengembalian',
                    "\"$bookTitle\" harus dikembalikan besok. Jangan sampai terlambat!",
                    ['loan_id' => (string) $item->loan_id, 'days_left' => '1']
                );
            } elseif ($diff === 0) {
                // H-0: hari pengembalian
                $this->sendNotification(
                    $fcm, $member, $item, 'reminder_pengembalian',
                    '📚 Hari Pengembalian',
                    "\"$bookTitle\" harus dikembalikan hari ini!",
                    ['loan_id' => (string) $item->loan_id, 'days_left' => '0']
                );
            } elseif ($diff < 0) {
                // Sudah terlambat
                $daysLate = abs($diff);
                $this->sendNotification(
                    $fcm, $member, $item, 'terlambat_pengembalian',
                    '⚠️ Buku Terlambat Dikembalikan',
                    "\"$bookTitle\" sudah terlambat $daysLate hari. Segera kembalikan!",
                    ['loan_id' => (string) $item->loan_id, 'days_late' => (string) $daysLate]
                );
            }
        }

        $this->info('Done sending loan reminders.');
    }

    private function sendNotification(
        FcmService $fcm,
        $member,
        LoanItem $item,
        string $type,
        string $title,
        string $body,
        array $data = []
    ): void {
        // Cek apakah notifikasi serupa sudah dikirim hari ini
        $alreadySent = MemberNotification::where('member_id', $member->id)
            ->where('type', $type)
            ->whereJsonContains('data->loan_id', $data['loan_id'] ?? '')
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadySent) return;

        // Simpan notifikasi ke database
        MemberNotification::create([
            'member_id'  => $member->id,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'data'       => $data,
            'is_read'    => false,
            'sent_at'    => now(),
            'created_at' => now(),
        ]);

        // Kirim push notification jika punya FCM token
        $tokens = FcmToken::where('user_id', $member->user_id ?? null)
            ->orWhereHas('user', fn($q) => $q->whereHas('member', fn($q2) => $q2->where('id', $member->id)))
            ->pluck('token')
            ->toArray();

        if (!empty($tokens)) {
            $fcm->sendMultiple($tokens, $title, $body, $data);
        }

        $this->line("  → [{$type}] Sent to {$member->id}: $title");
    }
}
