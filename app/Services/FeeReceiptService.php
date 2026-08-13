<?php

namespace App\Services;

use App\Repositories\FeeReceiptRepository;
use Barryvdh\DomPDF\Facade\Pdf;

class FeeReceiptService
{
    public function __construct(
        protected FeeReceiptRepository $repository
    ) {
    }

    /**
     * Get receipt list.
     */
    public function list(array $filters = [])
    {
        return $this->repository->list($filters);
    }

    /**
     * Get receipt details.
     */
    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Generate receipt PDF.
     */
    public function pdf(int $id)
    {
        $payment = $this->repository->find($id);

        $pdf = Pdf::loadView(
            'fee-collection.receipt',
            [
                'payment' => $payment,
            ]
        );

        $pdf->setPaper(
            'A4',
            'portrait'
        );

        return $pdf;
    }

    /**
     * Download receipt PDF.
     */
    public function download(int $id)
    {
        $payment = $this->repository->find($id);

        $pdf = Pdf::loadView(
            'fee-collection.receipt',
            [
                'payment' => $payment,
            ]
        );

        $pdf->setPaper(
            'A4',
            'portrait'
        );

        return $pdf->download(
            $payment->receipt_no . '.pdf'
        );
    }
}