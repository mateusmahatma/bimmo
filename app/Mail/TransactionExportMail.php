<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionExportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $excelData;
    public $fileName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($excelData, $fileName = 'arus_kas.xlsx')
    {
        $this->excelData = $excelData;
        $this->fileName = $fileName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Export Data Transaksi - BIMMO')
            ->view('emails.transaction_export') // We'll create this simple view
            ->attachData($this->excelData, $this->fileName, [
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
