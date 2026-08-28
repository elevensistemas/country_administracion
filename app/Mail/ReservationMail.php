<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $type; // 'admin_new' or 'owner_status'

    /**
     * Create a new message instance.
     */
    public function __construct(Reservation $reservation, string $type)
    {
        $this->reservation = $reservation;
        $this->type = $type;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = '';
        $view = '';

        if ($this->type === 'admin_new') {
            $subject = 'Nueva Solicitud de Reserva - ' . $this->reservation->commonArea->name;
            $view = 'emails.admin_reservation_created';
        } else {
            $status = $this->reservation->status === 'confirmed' ? 'Confirmada' : 'Pendiente';
            $subject = 'Estado de tu Reserva: ' . $status . ' - ' . $this->reservation->commonArea->name;
            $view = 'emails.owner_reservation_status';
        }

        return $this->subject($subject)
                    ->view($view);
    }
}
