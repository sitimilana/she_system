<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusCutiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cuti;
    public $statusKeputusan; // 'Disetujui' atau 'Ditolak'

    public function __construct($cuti, $statusKeputusan)
    {
        $this->cuti = $cuti;
        $this->statusKeputusan = $statusKeputusan;
    }

    public function build()
    {
        return $this->subject('Pemberitahuan Status ' . $this->cuti->jenis_cuti)
                    ->view('emails.status_cuti'); // Buat file view ini
    }
}