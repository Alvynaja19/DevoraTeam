<?php

namespace App\Services;

use App\Models\Fine;
use App\Models\FinePayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FineService
{
    /**
     * Bayar denda (bisa sebagian/cicil).
     */
    public function pay(Fine $fine, int $amountPaid, User $receivedBy, ?string $receiptNumber = null): FinePayment
    {
        return DB::transaction(function () use ($fine, $amountPaid, $receivedBy, $receiptNumber) {
            $payment = FinePayment::create([
                'fine_id'        => $fine->id,
                'amount_paid'    => $amountPaid,
                'payment_date'   => today(),
                'receipt_number' => $receiptNumber,
                'received_by'    => $receivedBy->id,
            ]);

            $totalPaid = $fine->payments()->sum('amount_paid');

            if ($totalPaid >= $fine->amount) {
                $fine->update([
                    'status'       => 'lunas',
                    'paid_at'      => now(),
                    'confirmed_by' => $receivedBy->id,
                ]);
            }

            return $payment;
        });
    }

    /**
     * Bebaskan denda (waive).
     */
    public function free(Fine $fine, string $reason, User $freedBy): Fine
    {
        $fine->update([
            'status'       => 'dibebaskan',
            'freed_by'     => $freedBy->id,
            'freed_reason' => $reason,
        ]);

        return $fine->fresh();
    }

    /**
     * Total denda belum lunas untuk member tertentu.
     */
    public function totalUnpaid(int $memberId): int
    {
        return Fine::whereHas('loanItem.loan', fn($q) => $q->where('member_id', $memberId))
            ->where('status', 'belum_lunas')
            ->sum('amount');
    }
}
