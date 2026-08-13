<?php
if (!function_exists('generateReceiptNo')) {
function generateReceiptNo(): string
{
    $year = now()->format('Y');

    $last = FeePayment::query()
        ->where(
            'school_id',
            auth()->user()->school_id
        )
        ->whereYear(
            'created_at',
            $year
        )
        ->latest('id')
        ->first();

    $number = $last
        ? ((int) substr($last->receipt_no, -6)) + 1
        : 1;

    return sprintf(
        'RCPT-%s-%06d',
        $year,
        $number
    );
}
}