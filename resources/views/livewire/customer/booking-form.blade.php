<?php

use Livewire\Volt\Component;
use App\Models\Barber;
use App\Models\Booking;
use Illuminate\Support\Str;
use Carbon\Carbon;

new class extends Component {
    // Properti Form Input
    public $selectedBarber = '';
    public $selectedDate = '';
    public $selectedSlot = '';
    public $customerName = '';
    public $customerPhone = '';
    public $currentBookingId = null;

    // Properti Status Alur Form
    public $bookingResult = null;
    public $bookedSlots = [];
    public $maxDate = '';

    public function mount()
    {
        $this->selectedDate = Carbon::today()->toDateString();
        $this->maxDate = Carbon::today()->addDays(14)->toDateString();
    }

    public function getAvailableSlotsProperty()
    {
        $allSlots = [
        '10:00', '10:30', '11:00', '11:30',
        '13:00', '13:30', '14:00', '14:30',
        '15:00', '15:30', '16:00', '16:30',
        '17:00', '17:30', '19:00', '19:30',
        '20:00', '20:30'
        ];

        $filteredSlots = [];

        // Pastikan zona waktu mengikuti waktu lokal (Asia/Jakarta atau Asia/Medan / WIB)
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i');

        foreach ($allSlots as $slot) {
            // JIKA tanggal yang dipilih pelanggan adalah HARI INI...
            if ($this->selectedDate === $today) {
                // ...maka slot yang jamnya sudah LEWAT atau SAMA DENGAN jam sekarang akan dilewati
                if ($slot <= $currentTime) {
                    continue;
                }
            }

            // Jalankan pengecekan database
            $isBooked = \App\Models\Booking::where('booking_date', $this->selectedDate)
                ->where('barber_id', $this->selectedBarber)
                ->where('booking_slot', $slot)
                ->whereIn('status', ['pending', 'checked_in', 'completed'])
                ->exists();

            $filteredSlots[] = [
                'time' => $slot,
                'is_booked' => $isBooked
            ];
        }
        return $filteredSlots;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'selectedBarber' || $propertyName === 'selectedDate') {
            $this->reset('selectedSlot');
            $this->checkBookedSlots();
        }
    }

    public function checkBookedSlots()
    {
        if ($this->selectedBarber && $this->selectedDate) {
            $this->bookedSlots = Booking::where('barber_id', $this->selectedBarber)
                ->where('booking_date', $this->selectedDate)
                ->whereIn('status', ['pending', 'checked_in'])
                ->pluck('booking_slot')
                ->map(fn($time) => Carbon::parse($time)->format('H:i'))
                ->toArray();
        } else {
            $this->bookedSlots = [];
        }
    }

    public function submitBooking()
    {

        $this->validate([
            'selectedBarber' => 'required|exists:barbers,id',
            'selectedDate' => 'required|date|after_or_equal:today|before_or_equal:' . $this->maxDate,
            'selectedSlot' => 'required',
            'customerName' => 'required|string|max:50',
            'customerPhone' => 'required|numeric|digits_between:10,14',
        ], [
            'required' => 'Kolom ini wajib diisi.',
            'customerPhone.numeric' => 'Nomor HP harus berupa angka.',
            'customerPhone.digits_between' => 'Nomor HP harus terdiri dari 10-14 digit.',
            'selectedDate.before_or_equal' => 'Pemesanan hanya dapat dilakukan max 14 hari ke depan.',
        ]);

        $isAlreadyBooked = Booking::where('barber_id', $this->selectedBarber)
            ->where('booking_date', $this->selectedDate)
            ->where('booking_slot', $this->selectedSlot)
            ->whereIn('status', ['pending', 'checked_in'])
            ->exists();

        if ($isAlreadyBooked) {
            session()->flash('error', 'Maaf, slot waktu ini baru saja dipesan orang lain. Silakan pilih slot lain.');
            $this->checkBookedSlots();
            return;
        }

        $uniqueCode = 'KLM-' . strtoupper(Str::random(6));

        $booking = Booking::create([
            'barber_id' => $this->selectedBarber,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'booking_date' => $this->selectedDate,
            'booking_slot' => $this->selectedSlot,
            'booking_code' => $uniqueCode,
            'status' => 'pending'
        ]);

        $this->currentBookingId = $booking->id;

        $this->bookingResult = [
            'code' => $uniqueCode,
            'name' => $this->customerName,
            'barber' => Barber::find($this->selectedBarber)->name,
            'date' => Carbon::parse($this->selectedDate)->translatedFormat('d F Y'),
            'slot' => $this->selectedSlot
        ];
    }

    public function cancelMyBooking()
    {
        if ($this->currentBookingId) {
            $booking = Booking::find($this->currentBookingId);

            if ($booking && $booking->status === 'pending') {
                $booking->update(['status' => 'canceled']);

                session()->flash('info', 'Pemesanan Anda telah berhasil dibatalkan.');
                $this->reset(['bookingResult', 'currentBookingId', 'selectedSlot', 'customerName', 'customerPhone']);
                $this->checkBookedSlots();
            }
        }
    }

    public function with(): array
    {
        return [
            'barbers' => Barber::orderBy('is_active', 'desc')->get()
        ];
    }
}; ?>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 md:p-8">
    @if($bookingResult)
        <div class="text-center py-6">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Booking Berhasil!</h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-6">Tunjukkan kode/angka unik di bawah ini kepada admin kasir saat tiba di Barbershop Mr. Klimis.</p>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg inline-block border-2 border-dashed border-blue-400 mb-6">
                <span class="text-3xl font-mono font-bold text-blue-600 dark:text-blue-400 tracking-wider">
                    {{ $bookingResult['code'] }}
                </span>
            </div>

            <div class="max-w-md mx-auto text-left border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2 text-sm text-gray-700 dark:text-gray-300 mb-6">
                <div class="flex justify-between"><span>Nama Pelanggan:</span> <span class="font-semibold">{{ $bookingResult['name'] }}</span></div>
                <div class="flex justify-between"><span>Barber Terpilih:</span> <span class="font-semibold">{{ $bookingResult['barber'] }}</span></div>
                <div class="flex justify-between"><span>Tanggal Jadwal:</span> <span class="font-semibold">{{ $bookingResult['date'] }}</span></div>
                <div class="flex justify-between"><span>Jam Kedatangan:</span> <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $bookingResult['slot'] }} WIB</span></div>
            </div>

            <!-- KONTROL TOMBOL: SELESAI ATAU BATAL -->
            <div class="flex flex-col sm:flex-row justify-center items-center gap-3 mt-8">
                <button onclick="window.location.reload()" class="w-full sm:w-auto px-6 py-2.5 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-900 transition font-medium">
                    Selesai & Tutup
                </button>

                <!-- Tombol Batalkan Pesanan Mandiri -->
                <button type="button"
                        wire:click="cancelMyBooking"
                        wire:confirm="Apakah Anda yakin ingin membatalkan pemesanan layanan pangkas rambut ini?"
                        class="w-full sm:w-auto px-6 py-2.5 bg-red-100 text-red-700 rounded-md text-sm hover:bg-red-200 transition font-medium">
                    Batalkan Pemesanan Ini
                </button>
            </div>
        </div>
    @else
        <form wire:submit.prevent="submitBooking" class="space-y-6">
            @if (session()->has('info'))
                <div class="p-4 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium mb-4">
                    {{ session('info') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="p-4 bg-red-100 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif
            <!-- 1. Pilih Tanggal Kedatangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">1. Pilih Tanggal Kedatangan</label>
                <input type="date"
                       wire:model.live="selectedDate"
                       min="{{ date('Y-m-d') }}"
                       max="{{ $maxDate }}"
                       class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500">
                @error('selectedDate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- 2. Pilih Barber -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">2. Pilih Barber Terbaik Anda</label>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4  ">
                    @foreach($barbers as $barber)
                        <div class="relative border-2 rounded-xl p-4 flex flex-col items-center text-center transition bg-gray-900 dark:bg-gray-750
                            {{ $selectedBarber == $barber->id ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-200 dark:border-gray-700' }}
                            {{ !$barber->is_active ? 'opacity-50 bg-gray-50 dark:bg-gray-900' : '' }}
                        ">
                            <!-- Foto/Avatar Barber Menggunakan Placeholder SVG -->
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-xl mb-3 overflow-hidden text-gray-600 dark:text-gray-300 font-bold">
                                {{ strtoupper(substr($barber->name, 0, 2)) }}
                            </div>

                            <!-- Detail Barber -->
                            <div class="font-bold text-gray-900 dark:text-gray-300 text-sm">{{ $barber->name }}</div>

                            <!-- Status Kehadiran Barber -->
                            <div class="text-xs mt-1 mb-4">
                                @if($barber->is_active)
                                    <span class="text-green-600 dark:text-green-400 font-medium">● Sedang Aktif</span>
                                @else
                                    <span class="text-red-500 dark:text-red-400 font-medium">✕ Sedang Tidak Aktif</span>
                                @endif
                            </div>

                            <!-- Tombol Aksi Pilihan -->
                            @if($barber->is_active)
                                <button type="button"
                                        wire:click="$set('selectedBarber', '{{ $barber->id }}')"
                                        class="mt-auto w-full py-1.5 rounded-lg text-xs font-bold transition
                                            {{ $selectedBarber == $barber->id ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-blue-500' }}
                                        ">
                                    {{ $selectedBarber == $barber->id ? 'Terpilih' : 'Pilih Barber' }}
                                </button>
                            @else
                                <button type="button" disabled class="mt-auto w-full py-1.5 rounded-lg text-xs font-bold bg-gray-200 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed">
                                    Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                @error('selectedBarber') <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span> @enderror
            </div>


            @if($selectedBarber && $selectedDate)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">3. Pilih Jam Slot yang Tersedia</label>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach($this->availableSlots as $slot)
                            {{-- Ambil nilai status booked langsung dari array backend baru --}}
                            @php $isBooked = $slot['is_booked']; @endphp

                            <button type="button"
                                wire:click="$set('selectedSlot', '{{ $slot['time'] }}')"
                                @if($isBooked) disabled @endif
                                class="py-2.5 text-xs font-semibold rounded-lg border text-center transition
                                    {{ $selectedSlot === $slot['time'] ? 'bg-blue-600 text-white border-blue-600 shadow' : '' }}
                                    {{ !$isBooked && $selectedSlot !== $slot['time'] ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:border-blue-500' : '' }}
                                    {{ $isBooked ? 'bg-gray-100 dark:bg-gray-900 text-gray-400 dark:text-gray-600 border-gray-200 line-through cursor-not-allowed' : '' }}
                                ">
                                {{-- Cetak teks jam slot --}}
                                {{ $slot['time'] }}

                                <div class="text-[9px] font-normal tracking-tight">
                                    {{ $isBooked ? 'Penuh' : 'Tersedia' }}
                                </div>
                            </button>
                        @endforeach
                    </div>
                    @error('selectedSlot') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- 4. Form Input Nama & Nomor HP untuk Konfirmasi Booking -->
            @if($selectedSlot)
                <div class="border-t border-gray-100 dark:border-gray-700 pt-4 space-y-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">4. Isi Identitas untuk Konfirmasi Kedatangan</label>

                    <div>
                        <input type="text" wire:model="customerName" placeholder="Masukkan Nama Anda" class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500">
                        @error('customerName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <input type="text" wire:model="customerPhone" placeholder="Masukkan Nomor WhatsApp (Contoh: 0812345678)" class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500">
                        @error('customerPhone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition shadow-md">
                        Konfirmasi Booking Layanan
                    </button>
                </div>
            @endif
        </form>
    @endif
</div>
