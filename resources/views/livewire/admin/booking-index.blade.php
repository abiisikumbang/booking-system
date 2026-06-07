<?php

use Livewire\Volt\Component;
use App\Models\Booking;
use Carbon\Carbon;

new class extends Component {
    public $searchCode = '';
    public $filterDate;

    public function mount()
    {
        // Default filter ke tanggal hari ini
        $this->filterDate = Carbon::today()->toDateString();
    }

    // Fungsi untuk Check-In Pelanggan berdasarkan ID Booking
    public function checkIn($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking && $booking->status === 'pending') {
            $booking->update(['status' => 'checked_in']);
            session()->flash('message', "Booking {$booking->booking_code} berhasil Check-In!");
        }
    }

    //fungsi untuk menyelesaikan booking
    public function completeBooking($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking && $booking->status === 'checked_in') {
            $booking->update(['status' => 'completed']);
            session()->flash('message', "Booking {$booking->booking_code} telah selesai!");
        }

    }

    // Fungsi untuk Batalkan Booking
    public function cancelBooking($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking && $booking->status === 'pending') {
            $booking->update(['status' => 'canceled']);
            session()->flash('message', "Booking {$booking->booking_code} telah dibatalkan.");
        }
    }

    // Mengambil data secara dinamis (computed property)
    public function with(): array
    {
        $query = Booking::with('barber')
            ->where('booking_date', $this->filterDate)
            ->orderBy('booking_slot', 'asc');

        // Jika admin mencari berdasarkan kode unik
        if (!empty($this->searchCode)) {
            $query->where('booking_code', 'like', '%' . $this->searchCode . '%');
        }

        return [
            'bookings' => $query->get()
        ];
    }
}; ?>


<div class="p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Daftar Antrean Barbershop</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Kelola reservasi pelanggan Mr. Klimis secara real-time.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <input type="date" wire:model.live="filterDate" class="rounded-md border-gray-300 shadow-sm dark:bg-gray-800 dark:text-gray-300">
            <input type="text" wire:model.live="searchCode" placeholder="Cari Kode Unik..." class="rounded-md border-gray-300 shadow-sm dark:bg-gray-800 dark:text-gray-300">
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg dark:bg-green-900 dark:text-green-200">
            {{ session('message') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jam Slot</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kode Unik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pelanggan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barber</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-zinc-60 0 dark:hover:bg-gray-750">

                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($booking->booking_slot)->format('H:i') }} WIB
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-sm font-mono font-bold bg-blue-100 text-blue-800 rounded dark:bg-blue-900 dark:text-blue-200">
                                {{ $booking->booking_code }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                            <div class="font-medium">{{ $booking->customer_name }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->customer_phone }}</div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                            {{ $booking->barber->name }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($booking->status === 'pending')
                                <span class="px-2.5 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-200">Pending</span>
                            @elseif($booking->status === 'checked_in')
                                <span class="px-2.5 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900 dark:text-blue-200">Hadir (Sedang Dipangkas)</span>
                            @elseif($booking->status === 'completed')
                                <span class="px-2.5 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-200">Selesai</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full dark:bg-red-900 dark:text-red-200">Batal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                           @if($booking->status === 'pending')
                                <!-- Jika status pending, tombolnya Check-In & Batal -->
                                <button wire:click="checkIn({{ $booking->id }})" class="mr-2 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs transition font-semibold">
                                    Check In
                                </button>
                                <button wire:click="cancelBooking({{ $booking->id }})" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs transition">
                                    Batal
                                </button>
                            @elseif($booking->status === 'checked_in')
                                <!-- Jika pelanggan sedang dicukur, ganti jadi tombol Selesai -->
                                <button wire:click="completeBooking({{ $booking->id }})" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs transition font-semibold shadow">
                                    Bayar & Selesai
                                </button>
                            @else
                                <!-- Jika status sudah 'selesai' atau 'canceled' -->
                                <span class="text-gray-400 text-xs italic">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data booking pada tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
