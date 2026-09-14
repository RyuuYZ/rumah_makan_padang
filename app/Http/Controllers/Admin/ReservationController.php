<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['branch', 'table'])->latest('reservation_time');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->paginate(10)->withQueryString();

        return view('admin.reservations.index', compact('reservations'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'table_id' => 'nullable|exists:tables,id'
        ]);

        $reservation = Reservation::findOrFail($id);
        
        $updateData = ['status' => $validated['status']];
        if ($request->filled('table_id')) {
            $updateData['table_id'] = $validated['table_id'];
        }

        $reservation->update($updateData);

        return redirect()->back()->with('success', "Status reservasi berhasil diperbarui menjadi {$validated['status']}.");
    }
    
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        
        return redirect()->back()->with('success', "Reservasi berhasil dihapus.");
    }
}
