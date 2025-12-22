<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LeaveRequestController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        // Middleware for leave request permissions
        // Note: Make sure these permissions are created in your permissions seeder
        return [
            new middleware('permission:view leave requests', ['only' => ['index', 'show']]),
            new middleware('permission:create leave requests', ['only' => ['create', 'store']]),
            new middleware('permission:edit leave requests', ['only' => ['edit', 'update']]),
            new middleware('permission:delete leave requests', ['only' => ['destroy']]),
            new middleware('permission:update status leave requests', ['only' => ['updateStatus']]),
            new middleware('permission:download attachment leave requests', ['only' => ['downloadAttachment']]),
        ];
    }

    /**
     * Helper method to check if the user can manage all leave requests.
     */
    private function canManageLeaveRequests()
    {
        // Superadmin or users with the specific permission can manage all requests
        return auth()->user()->hasRole('superadmin') || auth()->user()->can('manage all leave requests');
    }

    public function index()
    {
        try {
            if (!auth()->check()) {
                return redirect()->route('login');
            }

            $query = LeaveRequest::with(['user', 'processedBy']);

            // If the user cannot manage all requests, they see only their own.
            if (!$this->canManageLeaveRequests()) {
                $query->where('user_id', auth()->id());
            }

            $leaveRequests = $query->orderBy('created_at', 'desc')->get();

            return view('leave-requests.index', compact('leaveRequests'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data cuti/izin. Error: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $users = [];
        // Only users who can manage all requests can create one for another user.
        if ($this->canManageLeaveRequests()) {
            $users = User::where('id', '!=', auth()->id())
                ->orderBy('name')
                ->get();
        }

        return view('leave-requests.create', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            // Determine the user ID based on manager permissions
            $userId = $this->canManageLeaveRequests() ? $request->user_id : auth()->id();

            $validationRules = [
                'reason' => 'required|string|in:sakit,cuti,menikah,melahirkan,keluarga,lainnya',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            ];

            // Add manager-specific validation rules
            if ($this->canManageLeaveRequests()) {
                $validationRules['user_id'] = 'required|exists:users,id';
                $validationRules['status'] = 'nullable|string|in:pending,approved,rejected';
                $validationRules['admin_notes'] = 'nullable|string|max:1000';
            }

            if ($request->reason === 'lainnya') {
                $validationRules['custom_reason'] = 'required|string|max:500';
            }

            $validated = $request->validate($validationRules);

            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $endDate = \Carbon\Carbon::parse($validated['end_date']);
            $duration = $startDate->diffInDays($endDate) + 1;

            // Check for overlapping approved leave requests
            $hasOverlap = LeaveRequest::where('user_id', $userId)
                ->where('status', 'approved')
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhere(fn($q) => $q->where('start_date', '<=', $validated['start_date'])->where('end_date', '>=', $validated['end_date']));
                })->exists();

            if ($hasOverlap) {
                return redirect()->back()->withInput()->with('error', 'Tanggal yang dipilih bertabrakan dengan cuti/izin yang sudah disetujui.');
            }

            $attachmentPath = $request->hasFile('attachment') ? $request->file('attachment')->store('leave-attachments', 'public') : null;

            $finalReason = ($validated['reason'] === 'lainnya' && isset($validated['custom_reason'])) ? $validated['custom_reason'] : $validated['reason'];

            $status = ($this->canManageLeaveRequests() && isset($validated['status'])) ? $validated['status'] : 'pending';

            $leaveRequestData = [
                'user_id' => $userId,
                'reason' => $finalReason,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'duration' => $duration,
                'description' => $validated['description'] ?? null,
                'attachment' => $attachmentPath,
                'status' => $status,
            ];

            if ($this->canManageLeaveRequests() && $status !== 'pending') {
                $leaveRequestData['admin_notes'] = $validated['admin_notes'] ?? null;
                $leaveRequestData['processed_at'] = now();
                $leaveRequestData['processed_by'] = auth()->id();
            }

            $leaveRequest = LeaveRequest::create($leaveRequestData);

            if ($status === 'pending') {
                $this->sendNewRequestNotification($leaveRequest);
            } else {
                $this->sendStatusUpdateNotification($leaveRequest);
            }

            $message = $this->canManageLeaveRequests() ? 'Leave request berhasil disimpan.' : 'Pengajuan cuti/izin berhasil dibuat. Menunggu persetujuan admin.';

            return redirect()->route('leave_requests.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat pengajuan cuti/izin. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $leaveRequest = LeaveRequest::with(['user', 'processedBy'])->findOrFail($id);

            // Authorize: manager or owner
            if (!$this->canManageLeaveRequests() && $leaveRequest->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat data ini.');
            }

            return view('leave-requests.show', compact('leaveRequest'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data cuti/izin tidak ditemukan.');
        }
    }

    public function edit($id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            // Authorize: manager or owner
            if (!$this->canManageLeaveRequests() && $leaveRequest->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit data ini.');
            }

            if ($leaveRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan yang sudah diproses tidak bisa diedit.');
            }

            $users = [];
            if ($this->canManageLeaveRequests()) {
                $users = User::orderBy('name')->get();
            }

            return view('leave-requests.edit', compact('leaveRequest', 'users'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data cuti/izin tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            // Authorize: manager or owner
            if (!$this->canManageLeaveRequests() && $leaveRequest->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit data ini.');
            }

            if ($leaveRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan yang sudah diproses tidak bisa diedit.');
            }

            $userId = $this->canManageLeaveRequests() ? ($request->user_id ?? $leaveRequest->user_id) : $leaveRequest->user_id;

            $validationRules = [
                'reason' => 'required|string|in:sakit,cuti,menikah,melahirkan,keluarga,lainnya',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            ];

            if ($this->canManageLeaveRequests()) {
                $validationRules['user_id'] = 'nullable|exists:users,id';
                $validationRules['status'] = 'nullable|string|in:pending,approved,rejected';
                $validationRules['admin_notes'] = 'nullable|string|max:1000';
            }

            $validated = $request->validate($validationRules);

            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $endDate = \Carbon\Carbon::parse($validated['end_date']);
            $duration = $startDate->diffInDays($endDate) + 1;

            if ($request->hasFile('attachment')) {
                if ($leaveRequest->attachment && \Storage::disk('public')->exists($leaveRequest->attachment)) {
                    \Storage::disk('public')->delete($leaveRequest->attachment);
                }
                $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
            } else {
                $attachmentPath = $leaveRequest->attachment;
            }

            $updateData = [
                'user_id' => $userId,
                'reason' => $validated['reason'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'duration' => $duration,
                'description' => $validated['description'] ?? null,
                'attachment' => $attachmentPath,
                'status' => $this->canManageLeaveRequests() && isset($validated['status']) ? $validated['status'] : $leaveRequest->status,
            ];

            if ($this->canManageLeaveRequests() && $updateData['status'] !== 'pending' && $leaveRequest->status === 'pending') {
                $updateData['admin_notes'] = $validated['admin_notes'] ?? null;
                $updateData['processed_at'] = now();
                $updateData['processed_by'] = auth()->id();
            }

            $leaveRequest->update($updateData);

            if ($leaveRequest->wasChanged('status') && $leaveRequest->status !== 'pending') {
                $this->sendStatusUpdateNotification($leaveRequest);
            }

            return redirect()->route('leave_requests.index')->with('success', 'Leave request berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengajuan. Error: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, LeaveRequest $leaveRequest)
    {
        // This method is protected by 'update status leave requests' permission via middleware
        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Status cuti/izin sudah tidak bisa diubah.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $leaveRequest->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        $this->sendStatusUpdateNotification($leaveRequest);

        $message = $validated['status'] === 'approved' ? 'Cuti/izin berhasil disetujui.' : 'Cuti/izin berhasil ditolak.';
        return redirect()->route('leave_requests.index')->with('success', $message);
    }

    public function cancel($id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            if ($leaveRequest->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membatalkan pengajuan ini.');
            }

            if ($leaveRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan yang sudah diproses tidak bisa dibatalkan.');
            }

            $leaveRequest->update(['status' => 'cancelled', 'processed_at' => now()]);

            return redirect()->route('leave_requests.index')->with('success', 'Pengajuan cuti/izin berhasil dibatalkan.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data cuti/izin tidak ditemukan.');
        }
    }

    public function destroy($id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            // Authorize: manager or (owner if pending)
            $canDelete = $this->canManageLeaveRequests() || ($leaveRequest->user_id === auth()->id() && $leaveRequest->status === 'pending');

            if (!$canDelete) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
            }

            if ($leaveRequest->attachment && \Storage::disk('public')->exists($leaveRequest->attachment)) {
                \Storage::disk('public')->delete($leaveRequest->attachment);
            }

            $leaveRequest->delete();

            return redirect()->route('leave_requests.index')->with('success', 'Data cuti/izin berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data cuti/izin tidak ditemukan.');
        }
    }

    public function downloadAttachment($id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            // Authorize: manager or owner
            if (!$this->canManageLeaveRequests() && $leaveRequest->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengunduh file ini.');
            }

            if (!$leaveRequest->attachment) {
                return redirect()->back()->with('error', 'File lampiran tidak ditemukan.');
            }

            $filePath = storage_path('app/public/' . $leaveRequest->attachment);

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File lampiran tidak ditemukan di server.');
            }

            return response()->download($filePath);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data cuti/izin tidak ditemukan.');
        }
    }

    public function getStatistics()
    {
        if (!$this->canManageLeaveRequests()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
            'total_this_month' => LeaveRequest::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        return response()->json($stats);
    }

    private function sendStatusUpdateNotification($leaveRequest)
    {
        try {
            if (class_exists('\App\Notifications\LeaveRequestStatusUpdated')) {
                $leaveRequest->user->notify(new \App\Notifications\LeaveRequestStatusUpdated($leaveRequest));
            }
        } catch (\Exception $e) {
            // Log this error maybe?
        }
    }

    private function sendNewRequestNotification($leaveRequest)
    {
        try {
            if (class_exists('\App\Notifications\NewLeaveRequestCreated')) {
                // Notify all users who can manage leave requests
                $admins = User::permission('manage all leave requests')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\NewLeaveRequestCreated($leaveRequest));
                }
            }
        } catch (\Exception $e) {
            // Log this error maybe?
        }
    }
}
