<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class KaryawanBaruMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $passwordAsli; // Variabel penampung password

    // Pastikan password dimasukkan ke parameter ini
    public function __construct(User $user, $passwordAsli)
    {
        $this->user = $user;
        $this->passwordAsli = $passwordAsli;
    }

    public function build()
    {
        return $this->subject('Informasi Akun Karyawan Baru - PENDING')
                    ->view('emails.karyawan_baru');
    }
}