<?php

namespace App\Services;

use App\Models\Member;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\LoanExtension;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LoanService
{
    /**
     * Validasi apakah anggota boleh meminjam.
     * Returns ['valid' => bool, 'message' => string]
     */
    public function validateMember(Member $member): array
    {
        if ($member->status !== 'aktif') {
            return ['valid' => false, 'message' => "Anggota berstatus '{$member->status}', tidak bisa meminjam."];
        }

        if ($member->expired_at && $member->expired_at->isPast()) {
            return ['valid' => false, 'message' => 'Keanggotaan sudah kadaluarsa.'];
        }

        // Cek denda belum lunas
        $unpaidFines = $member->loans()
            ->with('items.fines')
            ->get()
            ->flatMap(fn($loan) => $loan->items)
            ->flatMap(fn($item) => $item->fines)
            ->where('status', 'belum_lunas')
            ->count();

        if ($unpaidFines > 0) {
            return ['valid' => false, 'message' => "Anggota masih memiliki {$unpaidFines} denda yang belum lunas."];
        }

        // Cek kuota
        $maxPinjam   = Setting::get('max_pinjam', 2);
        $activeItems = $member->loans()
            ->aktif()
            ->withCount(['items as unreturned' => fn($q) => $q->whereNull('returned_at')])
            ->get()
            ->sum('unreturned');

        if ($activeItems >= $maxPinjam) {
            return ['valid' => false, 'message' => "Anggota sudah meminjam {$activeItems} buku (maks {$maxPinjam})."];
        }

        return ['valid' => true, 'message' => 'OK', 'quota_remaining' => $maxPinjam - $activeItems];
    }

    /**
     * Hitung due date berdasarkan loan_type dan hari kerja.
     */
    public function calculateDueDate(Carbon $loanDate, string $loanType): Carbon
    {
        if ($loanType === 'lomba') {
            return $loanDate->copy()->addDays(30);
        }

        $lamaHari = Setting::get('lama_pinjam_hari', 3);
        $rules     = Setting::get('aktif_rules_rabu_kamis', true);

        $due = $loanDate->copy()->addDays($lamaHari);

        // Jika dipinjam Rabu (3) atau Kamis (4), jatuh tempo Senin berikutnya
        if ($rules && in_array($loanDate->dayOfWeek, [3, 4])) {
            $due = $loanDate->copy()->next(Carbon::MONDAY);
        }

        // Jika jatuh di Sabtu/Minggu, geser ke Senin
        if ($due->isWeekend()) {
            $due = $due->next(Carbon::MONDAY);
        }

        return $due;
    }

    /**
     * Buat transaksi peminjaman baru.
     */
    public function create(Member $member, array $copyCodes, User $createdBy, string $loanType = 'pembaca'): Loan
    {
        return DB::transaction(function () use ($member, $copyCodes, $createdBy, $loanType) {
            $loanDate = now()->toDateString();
            $dueDate  = $this->calculateDueDate(now(), $loanType);

            $loan = Loan::create([
                'loan_code'  => $this->generateLoanCode(),
                'member_id'  => $member->id,
                'loan_date'  => $loanDate,
                'due_date'   => $dueDate,
                'loan_type'  => $loanType,
                'status'     => 'aktif',
                'created_by' => $createdBy->id,
            ]);

            foreach ($copyCodes as $copyCode) {
                $copy = BookCopy::where('barcode', $copyCode)
                    ->orWhere('copy_code', $copyCode)
                    ->firstOrFail();

                $copy->update(['status' => 'dipinjam']);

                LoanItem::create([
                    'loan_id' => $loan->id,
                    'copy_id' => $copy->id,
                ]);

                // Update counter popularitas buku
                $copy->book()->increment('total_loans');
            }

            return $loan->load('items.copy.book');
        });
    }

    /**
     * Perpanjang masa pinjam.
     */
    public function extend(Loan $loan, User $extendedBy): Loan
    {
        $maxExtend = Setting::get('max_perpanjangan', 1);

        if ($loan->extended_count >= $maxExtend) {
            throw new \Exception("Maks perpanjangan ({$maxExtend}x) sudah tercapai.");
        }

        $oldDue = $loan->effectiveDueDate();
        $newDue = $this->calculateDueDate($oldDue, $loan->loan_type);

        DB::transaction(function () use ($loan, $oldDue, $newDue, $extendedBy) {
            LoanExtension::create([
                'loan_id'     => $loan->id,
                'old_due_date'=> $oldDue,
                'new_due_date'=> $newDue,
                'extended_by' => $extendedBy->id,
            ]);

            $loan->update([
                'extended_count'   => $loan->extended_count + 1,
                'extended_due_date'=> $newDue,
                'status'           => 'diperpanjang',
            ]);
        });

        return $loan->fresh();
    }

    private function generateLoanCode(): string
    {
        $date  = now()->format('Ymd');
        $count = Loan::whereDate('created_at', today())->count() + 1;
        return sprintf('TRX-%s-%03d', $date, $count);
    }
}
