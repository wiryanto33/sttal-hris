<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use App\Models\LeaveRequest;

class LeaveRequestStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $leaveRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
        $this->queue = 'notifications';
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $leaveTypes = [
            'annual' => 'Cuti Tahunan',
            'sick' => 'Cuti Sakit',
            'personal' => 'Cuti Pribadi',
            'maternity' => 'Cuti Melahirkan',
            'emergency' => 'Cuti Darurat'
        ];

        $statusLabels = [
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan'
        ];

        $leaveType = $leaveTypes[$this->leaveRequest->type] ?? ucfirst($this->leaveRequest->type);
        $statusLabel = $statusLabels[$this->leaveRequest->status] ?? ucfirst($this->leaveRequest->status);

        $isApproved = $this->leaveRequest->status === 'approved';
        $subject = $isApproved ? 'Pengajuan Cuti/Izin Disetujui' : 'Pengajuan Cuti/Izin Ditolak';

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Status pengajuan cuti/izin Anda telah diperbarui.')
            ->line('**Detail Pengajuan:**')
            ->line('Jenis: ' . $leaveType)
            ->line('Tanggal: ' . \Carbon\Carbon::parse($this->leaveRequest->start_date)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($this->leaveRequest->end_date)->format('d/m/Y'))
            ->line('Durasi: ' . $this->leaveRequest->duration . ' hari')
            ->line('Status: **' . $statusLabel . '**');

        if ($this->leaveRequest->admin_notes) {
            $mail->line('Catatan Admin: ' . $this->leaveRequest->admin_notes);
        }

        if ($isApproved) {
            $mail->line('Selamat! Pengajuan cuti/izin Anda telah disetujui.')
                ->line('Pastikan untuk mengatur pekerjaan Anda sebelum tanggal cuti.')
                ->success();
        } else {
            $mail->line('Mohon maaf, pengajuan cuti/izin Anda tidak dapat disetujui.')
                ->line('Anda dapat mengajukan kembali dengan penyesuaian yang diperlukan.')
                ->error();
        }

        return $mail->action('Lihat Detail', route('leave_requests.show', $this->leaveRequest->id))
            ->salutation('Terima kasih!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        $leaveTypes = [
            'annual' => 'Cuti Tahunan',
            'sick' => 'Cuti Sakit',
            'personal' => 'Cuti Pribadi',
            'maternity' => 'Cuti Melahirkan',
            'emergency' => 'Cuti Darurat'
        ];

        $statusLabels = [
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan'
        ];

        $leaveType = $leaveTypes[$this->leaveRequest->type] ?? ucfirst($this->leaveRequest->type);
        $statusLabel = $statusLabels[$this->leaveRequest->status] ?? ucfirst($this->leaveRequest->status);

        $isApproved = $this->leaveRequest->status === 'approved';
        $isRejected = $this->leaveRequest->status === 'rejected';

        $title = $isApproved ? 'Cuti/Izin Disetujui' : ($isRejected ? 'Cuti/Izin Ditolak' : 'Status Cuti/Izin Diperbarui');
        $message = $leaveType . ' Anda tanggal ' . \Carbon\Carbon::parse($this->leaveRequest->start_date)->format('d/m/Y') . ' telah ' . strtolower($statusLabel);

        $color = $isApproved ? 'green' : ($isRejected ? 'red' : 'gray');
        $icon = $isApproved ? 'check-circle' : ($isRejected ? 'x-circle' : 'clock');

        return [
            'title' => $title,
            'message' => $message,
            'leave_request_id' => $this->leaveRequest->id,
            'status' => $this->leaveRequest->status,
            'status_label' => $statusLabel,
            'type' => $leaveType,
            'start_date' => $this->leaveRequest->start_date,
            'end_date' => $this->leaveRequest->end_date,
            'duration' => $this->leaveRequest->duration,
            'admin_notes' => $this->leaveRequest->admin_notes,
            'processed_by' => $this->leaveRequest->processedBy?->name,
            'processed_at' => $this->leaveRequest->processed_at,
            'action_url' => route('leave_requests.show', $this->leaveRequest->id),
            'icon' => $icon,
            'color' => $color
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
