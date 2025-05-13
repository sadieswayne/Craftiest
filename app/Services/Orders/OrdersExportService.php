<?php

namespace App\Services\Orders;

namespace App\Services\Orders;

use App\Models\UserOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class OrdersExportService
{
    public function __construct()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '300');
    }

    public function exportAsPdf(null|Collection|UserOrder $invoices = null): RedirectResponse|BinaryFileResponse|JsonResponse
    {
        $userOrders = $invoices ?? UserOrder::where('user_id', auth()->user()->id)->get();
        if ($userOrders->count() > 0) {
            $zipFileName = 'user_orders.zip';
            $zipPath = 'upload/zips/' . $zipFileName;

            if (! file_exists('upload/zips') && ! mkdir('upload/zips', 0777, true) && ! is_dir('upload/zips')) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', 'upload/zips'));
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE|ZipArchive::OVERWRITE) !== true) {
                return back()->with(['message' => __('Could not create ZIP file'), 'type' => 'error']);
            }

            $invoicesPath = 'upload/invoices';
            if (! file_exists($invoicesPath) && ! mkdir($invoicesPath, 0777, true) && ! is_dir($invoicesPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $invoicesPath));
            }

            $pdfFiles = [];

            foreach ($userOrders as $order) {
                $html = $this->getInvoice($order->id);
                $pdf = app('dompdf.wrapper');
                $pdf->loadHTML($html);
                $pdf->setPaper('A4', 'portrait');
                $pdf->render();
                $pdfPath = $invoicesPath . '/' . $order->order_id . '.pdf';
                $pdf->save($pdfPath);

                $zip->addFile($pdfPath, $order->order_id . '.pdf');
                $pdfFiles[] = $pdfPath;
            }

            $zip->close();

            // Create response and delete PDFs after sending
            $response = response()->download($zipPath)->deleteFileAfterSend(true);

            // Delete PDF files after ZIP is created
            foreach ($pdfFiles as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            return $response;
        }

        return back()->with(['message' => __('No invoices found'), 'type' => 'error']);
    }

    private function getInvoice($id): string
    {
        $invoice = UserOrder::findOrFail($id);

        return view('panel.user.orders.invoice-single', compact('invoice'))->render();
    }
}
