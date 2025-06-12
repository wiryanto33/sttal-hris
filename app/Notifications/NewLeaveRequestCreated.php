<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use App\Models\LeaveRequest;

class NewLeaveRequestCreated extends Notification implements ShouldQueue
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

        $leaveType = $leaveTypes[$this->leaveRequest->type] ?? ucfirst($this->leaveRequest->type);

        return (new MailMessage)
            ->subject('Pengajuan Cuti/Izin Baru - ' . $this->leaveRequest->user->name)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Ada pengajuan cuti/izin baru yang memerlukan persetujuan Anda.')
            ->line('**Detail Pengajuan:**')
            ->line('Nama Karyawan: ' . $this->leaveRequest->user->name)
            ->line('Jenis: ' . $leaveType)
            ->line('Tanggal: ' . \Carbon\Carbon::parse($this->leaveRequest->start_date)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($this->leaveRequest->end_date)->format('d/m/Y'))
            ->line('Durasi: ' . $this->leaveRequest->duration . ' hari')
            ->line('Alasan: ' . $this->leaveRequest->reason)
            ->action('Lihat Detail', route('leave_requests.show', $this->leaveRequest->id))
            ->line('Silakan segera tinjau dan berikan persetujuan.')
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

        $leaveType = $leaveTypes[$this->leaveRequest->type] ?? ucfirst($this->leaveRequest->type);

        return [
            'title' => 'Pengajuan Cuti/Izin Baru',
            'message' => $this->leaveRequest->user->name . ' mengajukan ' . $leaveType . ' selama ' . $this->leaveRequest->duration . ' hari',
            'leave_request_id' => $this->leaveRequest->id,
            'user_name' => $this->leaveRequest->user->name,
            'type' => $leaveType,
            'start_date' => $this->leaveRequest->start_date,
            'end_date' => $this->leaveRequest->end_date,
            'duration' => $this->leaveRequest->duration,
            'reason' => $this->leaveRequest->reason,
            'action_url' => route('leave_requests.show', $this->leaveRequest->id),
            'icon' => 'calendar-plus',
            'color' => 'blue'
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
