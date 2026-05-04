<?php

namespace App\Notifications;

use App\Models\FaturaFornecedor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FaturaPagaNotification extends Notification
{
    use Queueable;

    protected $fatura;
    protected $comprovativoPath;

    public function __construct(FaturaFornecedor $fatura, $comprovativoPath)
    {
        $this->fatura = $fatura;
        $this->comprovativoPath = $comprovativoPath;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject("Comprovativo de Pagamento - Fatura {$this->fatura->numero}")
            ->greeting("Olá, {$notifiable->nome}!")
            ->line("Enviamos em anexo o comprovativo de pagamento da fatura **{$this->fatura->numero}**.")
            ->line("Qualquer questão, entre em contacto connosco.")
            ->salutation("Cumprimentos,\n" . config('app.name'));

        // Anexar o comprovativo se existir
        if ($this->comprovativoPath && \Storage::disk('public')->exists($this->comprovativoPath)) {
            $mailMessage->attach(\Storage::disk('public')->path($this->comprovativoPath));
        }

        return $mailMessage;
    }
}
