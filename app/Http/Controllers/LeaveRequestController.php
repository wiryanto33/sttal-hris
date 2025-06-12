<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{

    public function index()
    {
        try {
            // Cek apakah user sudah login
            if (!auth()->check()) {
                return redirect()->route('login');
            }

            // Cek apakah model LeaveRequest ada
            if (!class_exists('\App\Models\LeaveRequest')) {
                return redirect()->back()->with('error', 'Model LeaveRequest tidak ditemukan.');
            }

            // Query builder untuk leave requests
            $query = LeaveRequest::with(['user', 'processedBy']);

            // Filter berdasarkan role
            if (!auth()->user()->hasRole('superadmin')) {
                // Non-superadmin hanya bisa melihat data mereka sendiri
                $query->where('user_id', auth()->id());
            }

            // Urutkan berdasarkan created_at terbaru
            $leaveRequests = $query->orderBy('created_at', 'desc')->get();

            // Cek apakah view ada
            if (!view()->exists('leave-requests.index')) {
                return redirect()->back()->with('error', 'Template tidak ditemukan.');
            }

            return view('leave-requests.index', compact('leaveRequests'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data cuti/izin. Error: ' . $e->getMessage());
        }
    }
    /**
     * Show form untuk membuat leave request baru
     */
    public function create()
    {
        // Jika superadmin, sertakan daftar users
        $users = [];
        if (auth()->user()->hasRole('superadmin')) {
            $users = \App\Models\User::where('id', '!=', auth()->id())
                ->orderBy('name')
                ->get();
        }

        return view('leave-requests.create', compact('users'));
    }

    /**
     * Store leave request baru
     */
    public function store(Request $request)
    {
        try {
            // Tentukan user_id berdasarkan role
            $userId = auth()->user()->hasRole('superadmin') ? $request->user_id : auth()->id();

            // Validasi input
            $validationRules = [
                'reason' => 'required|string|in:sakit,cuti,menikah,melahirkan,keluarga,lainnya',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            ];

            $validationMessages = [
                'reason.required' => 'Alasan izin harus dipilih.',
                'reason.in' => 'Alasan izin tidak valid.',
                'start_date.required' => 'Tanggal mulai harus diisi.',
                'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
                'end_date.required' => 'Tanggal selesai harus diisi.',
                'end_date.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
                'description.max' => 'Keterangan maksimal 1000 karakter.',
                'attachment.mimes' => 'File lampiran harus berformat PDF, JPG, JPEG, PNG, DOC, atau DOCX.',
                'attachment.max' => 'Ukuran file lampiran maksimal 2MB.',
            ];

            // Tambahan validasi untuk superadmin
            if (auth()->user()->hasRole('superadmin')) {
                $validationRules['user_id'] = 'required|exists:users,id';
                $validationRules['status'] = 'nullable|string|in:pending,approved,rejected';
                $validationRules['superadmin_notes'] = 'nullable|string|max:1000';

                $validationMessages['user_id.required'] = 'User harus dipilih.';
                $validationMessages['user_id.exists'] = 'User yang dipilih tidak valid.';
                $validationMessages['status.in'] = 'Status tidak valid.';
                $validationMessages['superadmin_notes.max'] = 'Catatan superadmin maksimal 1000 karakter.';
            }

            // Validasi custom reason jika reason adalah 'lainnya'
            if ($request->reason === 'lainnya') {
                $validationRules['custom_reason'] = 'required|string|max:500';
                $validationMessages['custom_reason.required'] = 'Alasan lainnya harus diisi.';
                $validationMessages['custom_reason.max'] = 'Alasan lainnya maksimal 500 karakter.';
            }

            $validated = $request->validate($validationRules, $validationMessages);

            // Hitung durasi hari
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $endDate = \Carbon\Carbon::parse($validated['end_date']);
            $duration = $startDate->diffInDays($endDate) + 1;

            // Cek apakah ada overlap dengan leave request yang sudah approved
            $hasOverlap = LeaveRequest::where('user_id', $userId)
                ->where('status', 'approved')
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('start_date', '<=', $validated['start_date'])
                                ->where('end_date', '>=', $validated['end_date']);
                        });
                })->exists();

            if ($hasOverlap) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tanggal yang dipilih bertabrakan dengan cuti/izin yang sudah disetujui.');
            }

            // Handle file upload
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
            }

            // Gabungkan reason dan custom_reason jika diperlukan
            $finalReason = $validated['reason'];
            if ($validated['reason'] === 'lainnya' && isset($validated['custom_reason'])) {
                $finalReason = $validated['custom_reason'];
            }

            // Tentukan status default
            $status = auth()->user()->hasRole('superadmin') && isset($validated['status'])
                ? $validated['status']
                : 'pending';

            // Simpan leave request
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

            // Tambahan data jika superadmin mengisi status selain pending
            if (auth()->user()->hasRole('superadmin') && $status !== 'pending') {
                $leaveRequestData['superadmin_notes'] = $validated['superadmin_notes'] ?? null;
                $leaveRequestData['processed_at'] = now();
                $leaveRequestData['processed_by'] = auth()->id();
            }

            $leaveRequest = LeaveRequest::create($leaveRequestData);

            // Kirim notifikasi
            if ($status === 'pending') {
                $this->sendNewRequestNotification($leaveRequest);
            } else {
                $this->sendStatusUpdateNotification($leaveRequest);
            }

            $message = auth()->user()->hasRole('superadmin')
                ? 'Leave request berhasil disimpan.'
                : 'Pengajuan cuti/izin berhasil dibuat. Menunggu persetujuan admin.';

            return redirect()->route('leave_requests.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat pengajuan cuti/izin. Silakan coba lagi.');
        }
    }

    /**
     * Show detail leave request
     */
    public function show($id)
    {
        try {
            $leaveRequest = LeaveRequest::with(['user', 'processedBy'])->findOrFail($id);

            // Cek authorization
            if (!auth()->user()->hasRole('superadmin') && $leaveRequest->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat data ini.');
            }

            return view('leave-requests.show', compact('leaveRequest'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data cuti/izin tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat detail cuti/izin.');
        }
    }

    /**
     * Show form untuk edit leave request
     */
    public function edit($id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            // Cek authorization
            if (!auth()->user()->hasRole('superadmin') && $leaveRequest->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit data ini.');
            }

            // Cek apakah masih bisa diedit (hanya yang status pending)
            if ($leaveRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan yang sudah diproses tidak bisa diedit.');
            }

            // Jika superadmin, sertakan daftar users
            $users = [];
            if (auth()->user()->hasRole('superadmin')) {
                $users = \App\Models\User::orderBy('name')->get();
            }

            return view('leave-requests.edit', compact('leaveRequest', 'users'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data cuti/izin tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat form edit cuti/izin.');
        }
    }

    /**
     * Update leave request yang sudah ada
     */
    public function update(Request $request, $id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            // Cek authorization
            if (!auth()->user()->hasRole('superadmin') && $leaveRequest->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit data ini.');
            }

            // Cek apakah masih bisa diedit (hanya yang status pending)
            if ($leaveRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan yang sudah diproses tidak bisa diedit.');
            }

            // Tentukan user_id berdasarkan role
            $userId = auth()->user()->hasRole('superadmin')
                ? ($request->user_id ?? $leaveRequest->user_id)
                : $leaveRequest->user_id;

            // Validasi input
            $validationRules = [
                'reason' => 'required|string|in:sakit,cuti,menikah,melahirkan,keluarga,lainnya',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            ];

            $validationMessages = [
                'reason.required' => 'Alasan izin harus dipilih.',
                'reason.in' => 'Alasan izin tidak valid.',
                'start_date.required' => 'Tanggal mulai harus diisi.',
                'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
                'end_date.required' => 'Tanggal selesai harus diisi.',
                'end_date.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
                'description.max' => 'Keterangan maksimal 1000 karakter.',
                'attachment.mimes' => 'File lampiran harus berformat PDF, JPG, JPEG, PNG, DOC, atau DOCX.',
                'attachment.max' => 'Ukuran file lampiran maksimal 2MB.',
            ];

            // Tambahan validasi untuk superadmin
            if (auth()->user()->hasRole('superadmin')) {
                $validationRules['user_id'] = 'nullable|exists:users,id';
                $validationRules['status'] = 'nullable|string|in:pending,approved,rejected';
                $validationRules['superadmin_notes'] = 'nullable|string|max:1000';

                $validationMessages['user_id.exists'] = 'User yang dipilih tidak valid.';
                $validationMessages['status.in'] = 'Status tidak valid.';
                $validationMessages['superadmin_notes.max'] = 'Catatan superadmin maksimal 1000 karakter.';
            }

            // Validasi custom reason jika reason adalah 'lainnya'
            if ($request->reason === 'lainnya') {
                $validationRules['custom_reason'] = 'required|string|max:500';
                $validationMessages['custom_reason.required'] = 'Alasan lainnya harus diisi.';
                $validationMessages['custom_reason.max'] = 'Alasan lainnya maksimal 500 karakter.';
            }

            $validated = $request->validate($validationRules, $validationMessages);

            // Hitung durasi hari
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $endDate = \Carbon\Carbon::parse($validated['end_date']);
            $duration = $startDate->diffInDays($endDate) + 1;

            // Cek apakah ada overlap dengan leave request yang sudah approved (kecuali request ini sendiri)
            $hasOverlap = LeaveRequest::where('user_id', $userId)
                ->where('status', 'approved')
                ->where('id', '!=', $leaveRequest->id)
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('start_date', '<=', $validated['start_date'])
                                ->where('end_date', '>=', $validated['end_date']);
                        });
                })->exists();

            if ($hasOverlap) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tanggal yang dipilih bertabrakan dengan cuti/izin yang sudah disetujui.');
            }

            // Handle file upload
            $attachmentPath = $leaveRequest->attachment; // Keep existing attachment
            if ($request->hasFile('attachment')) {
                // Delete old attachment if exists
                if ($leaveRequest->attachment && \Storage::disk('public')->exists($leaveRequest->attachment)) {
                    \Storage::disk('public')->delete($leaveRequest->attachment);
                }
                $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
            }

            // Gabungkan reason dan custom_reason jika diperlukan
            $finalReason = $validated['reason'];
            if ($validated['reason'] === 'lainnya' && isset($validated['custom_reason'])) {
                $finalReason = $validated['custom_reason'];
            }

            // Tentukan status
            $status = auth()->user()->hasRole('superadmin') && isset($validated['status'])
                ? $validated['status']
                : $leaveRequest->status;

            // Update leave request
            $updateData = [
                'user_id' => $userId,
                'reason' => $finalReason,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'duration' => $duration,
                'description' => $validated['description'] ?? null,
                'attachment' => $attachmentPath,
                'status' => $status,
            ];

            // Tambahan data jika superadmin mengubah status
            if (auth()->user()->hasRole('superadmin') && $status !== 'pending' && $leaveRequest->status === 'pending') {
                $updateData['superadmin_notes'] = $validated['superadmin_notes'] ?? null;
                $updateData['processed_at'] = now();
                $updateData['processed_by'] = auth()->id();
            }

            $leaveRequest->update($updateData);

            // Kirim notifikasi jika status berubah
            if ($leaveRequest->wasChanged('status') && $status !== 'pending') {
                $this->sendStatusUpdateNotification($leaveRequest);
            }

            $message = auth()->user()->hasRole('superadmin')
                ? 'Leave request berhasil diperbarui.'
                : 'Pengajuan cuti/izin berhasil diperbarui.';

            return redirect()->route('leave_requests.index')->with('success', $message);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data cuti/izin tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pengajuan cuti/izin. Silakan coba lagi.');
        }
    }

    /**
     * Update status leave request (untuk admin)
     */
    public function updateStatus(Request $request, LeaveRequest $leaveRequest)
    {
        // Pastikan hanya superadmin yang bisa mengakses
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah status.');
        }

        try {
            // Cek apakah status masih pending
            if ($leaveRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Status cuti/izin sudah tidak bisa diubah.');
            }

            // Validasi input
            $validated = $request->validate([
                'status' => 'required|string|in:approved,rejected',
                'superadmin_notes' => 'nullable|string|max:1000',
            ], [
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status tidak valid.',
                'superadmin_notes.max' => 'Catatan superadmin maksimal 1000 karakter.',
            ]);

            // Update data
            $leaveRequest->update([
                'status' => $validated['status'],
                'superadmin_notes' => $validated['superadmin_notes'],
                'processed_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            // Kirim notifikasi ke user (if you have this method)
            // $this->sendStatusUpdateNotification($leaveRequest);

            $message = $validated['status'] === 'approved'
                ? 'Cuti/izin berhasil disetujui.'
                : 'Cuti/izin berhasil ditolak.';

            return redirect()->route('leave_requests.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status cuti/izin. Silakan coba lagi.');
        }
    }

    /**
     * Cancel leave request (hanya untuk user pemilik dan status pending)
     */
    public function cancel($id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            // Cek authorization - hanya pemilik yang bisa cancel
            if ($leaveRequest->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membatalkan pengajuan ini.');
            }

            // Cek apakah status masih pending
            if ($leaveRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan yang sudah diproses tidak bisa dibatalkan.');
            }

            // Update status menjadi cancelled
            $leaveRequest->update([
                'status' => 'cancelled',
                'processed_at' => now(),
            ]);

            return redirect()->route('leave_requests.index')
                ->with('success', 'Pengajuan cuti/izin berhasil dibatalkan.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data cuti/izin tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membatalkan pengajuan cuti/izin.');
        }
    }

    /**
     * Delete leave request (untuk superadmin atau user pemilik jika status pending)
     */
    public function destroy($id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            // Cek authorization
            $canDelete = false;

            if (auth()->user()->hasRole('superadmin')) {
                // Superadmin bisa menghapus semua data
                $canDelete = true;
            } elseif ($leaveRequest->user_id === auth()->id() && $leaveRequest->status === 'pending') {
                // User biasa hanya bisa menghapus pengajuan mereka sendiri yang masih pending
                $canDelete = true;
            }

            if (!$canDelete) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
            }

            // Hapus file attachment jika ada
            if ($leaveRequest->attachment && \Storage::disk('public')->exists($leaveRequest->attachment)) {
                \Storage::disk('public')->delete($leaveRequest->attachment);
            }

            // Hapus data leave request
            $leaveRequest->delete();

            $message = auth()->user()->hasRole('superadmin')
                ? 'Data cuti/izin berhasil dihapus.'
                : 'Pengajuan cuti/izin berhasil dihapus.';

            return redirect()->route('leave_requests.index')->with('success', $message);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data cuti/izin tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus pengajuan cuti/izin.');
        }
    }

    /**
     * Download attachment file
     */
    public function downloadAttachment($id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            // Cek authorization
            if (!auth()->user()->hasRole('superadmin') && $leaveRequest->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengunduh file ini.');
            }

            // Cek apakah ada attachment
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh file lampiran.');
        }
    }

    /**
     * Get dashboard statistics (untuk superadmin)
     */
    public function getStatistics()
    {
        if (!auth()->user()->hasRole('superadmin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $stats = [
                'pending' => LeaveRequest::where('status', 'pending')->count(),
                'approved' => LeaveRequest::where('status', 'approved')->count(),
                'rejected' => LeaveRequest::where('status', 'rejected')->count(),
                'total_this_month' => LeaveRequest::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get statistics'], 500);
        }
    }

    /**
     * Kirim notifikasi status update ke user
     */
    private function sendStatusUpdateNotification($leaveRequest)
    {
        try {
            // Pastikan class notification sudah dibuat
            if (class_exists('\App\Notifications\LeaveRequestStatusUpdated')) {
                $leaveRequest->user->notify(new \App\Notifications\LeaveRequestStatusUpdated($leaveRequest));
            }
        } catch (\Exception $e) {
            // Handle silently
        }
    }

    /**
     * Kirim notifikasi pengajuan baru ke admin
     */
    private function sendNewRequestNotification($leaveRequest)
    {
        try {
            // Pastikan class notification sudah dibuat
            if (class_exists('\App\Notifications\NewLeaveRequestCreated')) {
                // Ambil semua superadmin
                $superAdmins = \App\Models\User::role('superadmin')->get();

                // Kirim notifikasi ke setiap superadmin
                foreach ($superAdmins as $admin) {
                    $admin->notify(new \App\Notifications\NewLeaveRequestCreated($leaveRequest));
                }
            }
        } catch (\Exception $e) {
            // Handle silently
        }
    }
}
