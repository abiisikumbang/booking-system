<?php

use Livewire\Volt\Component;
use App\Models\Booking;
use Carbon\Carbon;

new class extends Component {
    public $searchCode = '';
    public $filterDate;
    public $showAllDates = false;

    public function render(): mixed
    {

        $query = \App\Models\Booking::query();


        if (!$this->showAllDates) {
            $query->where('booking_date', $this->filterDate);
        }

        if (!empty($this->searchCode)) {
            $query->where('booking_code', 'like', '%' . $this->searchCode . '%');
        }


        $bookings = $query->orderBy('booking_date', 'desc')
                        ->orderBy('booking_slot', 'asc')
                        ->paginate(10);

        return view('livewire.admin.booking-index', [
            'bookings' => $bookings
        ]);
    }


    public function toggleAllDates()
    {
        $this->showAllDates = !$this->showAllDates;
    }

    public function mount()
    {

        $this->filterDate = Carbon::today()->toDateString();
    }

    public function checkIn($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking && $booking->status === 'pending') {
            $booking->update(['status' => 'checked_in']);
            session()->flash('message', "Booking {$booking->booking_code} berhasil Check-In!");
        }
    }


    public function completeBooking($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking && $booking->status === 'checked_in') {
            $booking->update(['status' => 'completed']);
            session()->flash('message', "Booking {$booking->booking_code} telah selesai!");
        }

    }

    public function cancelBooking($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking && $booking->status === 'pending') {
            $booking->update(['status' => 'canceled']);
            session()->flash('message', "Booking {$booking->booking_code} telah dibatalkan.");
        }
    }

    public function with(): array
    {
        $query = Booking::with('barber')
            ->where('booking_date', $this->filterDate)
            ->orderBy('booking_slot', 'asc');
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

        <div class="flex flex-wrap items-center gap-2">
            <button type="button"
                wire:click="toggleAllDates"
                class="px-3 py-2 text-xs font-semibold rounded-md border transition duration-150 shadow-sm
                    {{ $showAllDates
                        ? 'bg-blue-600 border-blue-600 text-white hover:bg-blue-700'
                        : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                    }}">
                @if($showAllDates)
                    📅 Lihat Per Hari
                @else
                Lihat Semua Riwayat
                @endif
            </button>

            <input type="date"
                wire:model.live="filterDate"
                @if($showAllDates) disabled @endif
                class="rounded-md border-gray-300 shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 @if($showAllDates) opacity-40 cursor-not-allowed @endif">

            <input type="text"
                wire:model.live="searchCode"
                placeholder="Cari Kode Unik..."
                class="rounded-md border-gray-300 shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
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
                    @if($showAllDates)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                    @endif
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
                    <tr class="hover:bg-gray-200 dark:hover:bg-gray-750">

                        @if($showAllDates)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-medium">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d M Y') }}
                            </td>
                        @endif

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
                                <button wire:click="checkIn({{ $booking->id }})" class="mr-2 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs transition font-semibold">
                                    Check In
                                </button>
                                <button wire:click="cancelBooking({{ $booking->id }})" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs transition">
                                    Batal
                                </button>
                            @elseif($booking->status === 'checked_in')
                                <button wire:click="completeBooking({{ $booking->id }})" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs transition font-semibold shadow">
                                    Bayar & Selesai
                                </button>
                            @elseif($booking->status === 'completed' || $booking->status === 'selesai')
                                <span class="text-green-400/80 text-xs font-medium italic">Selesai</span>
                            @elseif($booking->status === 'canceled' || $booking->status === 'batal')
                                <span class="text-red-400/80 text-xs font-medium italic">Dibatalkan</span>
                            @else
                                <span class="text-gray-500 text-xs italic">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $showAllDates ? '7' : '6' }}" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data booking yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
