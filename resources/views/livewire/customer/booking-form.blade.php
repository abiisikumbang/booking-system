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

    // Properti Status Alur Form
    public $bookingResult = null;
    public $bookedSlots = [];

    // Mengatur waktu default tanggal ke hari ini saat komponen dimuat
    public function mount()
    {
        $this->selectedDate = Carbon::today()->toDateString();
    }

    // Definisi daftar slot waktu operasional (Fixed 30 Menit)
    public function getAvailableSlotsProperty()
    {
        return [
            '10:00', '10:30', '11:00', '11:30', 
            '13:00', '13:30', '14:00', '14:30', 
            '15:00', '15:30', '16:00', '16:30', 
            '17:00', '17:30', '19:00', '19:30', 
            '20:00', '20:30'
        ];
    }

    // Setiap kali pelanggan mengubah tanggal atau memilih barber, hitung slot yang sudah terisi
    public function updated($propertyName)
    {
        if ($propertyName === 'selectedBarber' || $propertyName === 'selectedDate') {
            $this->reset('selectedSlot');
            $this->checkBookedSlots();
        }
    }

    // Fungsi memeriksa database untuk slot jam yang sudah dipesan orang lain
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

    // Fungsi Eksekusi Pemesanan (Submit Form)
    public function submitBooking()
    {
        // Validasi input data pelanggan
        $this->validate([
            'selectedBarber' => 'required|exists:barbers,id',
            'selectedDate' => 'required|date|after_or_equal:today',
            'selectedSlot' => 'required',
            'customerName' => 'required|string|max:50',
            'customerPhone' => 'required|numeric|digits_between:10,14',
        ], [
            'required' => 'Kolom ini wajib diisi.',
            'customerPhone.numeric' => 'Nomor HP harus berupa angka.',
            'customerPhone.digits_between' => 'Nomor HP harus terdiri dari 10-14 digit.'
        ]);

        // Proteksi ganda: Pastikan slot belum disalip orang lain tepat sebelum klik submit
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

        // Generate Kode Unik Manual (Misal: KLM-XYZ123)
        $uniqueCode = 'KLM-' . strtoupper(Str::random(6));

        // Simpan ke database
        $booking = Booking::create([
            'barber_id' => $this->selectedBarber,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'booking_date' => $this->selectedDate,
            'booking_slot' => $this->selectedSlot,
            'booking_code' => $uniqueCode,
            'status' => 'pending'
        ]);

        // Tampilkan hasil sukses ke layar pelanggan
        $this->bookingResult = [
            'code' => $uniqueCode,
            'name' => $this->customerName,
            'barber' => Barber::find($this->selectedBarber)->name,
            'date' => Carbon::parse($this->selectedDate)->translatedFormat('d F Y'),
            'slot' => $this->selectedSlot
        ];
    }

    // Ambil daftar nama barber yang aktif saat ini dari database
    public function with(): array
    {
        return [
            'barbers' => Barber::where('is_active', true)->get()
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

            <div class="max-w-md mx-auto text-left border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <div class="flex justify-between"><span>Nama Pelanggan:</span> <span class="font-semibold">{{ $bookingResult['name'] }}</span></div>
                <div class="flex justify-between"><span>Barber Terpilih:</span> <span class="font-semibold">{{ $bookingResult['barber'] }}</span></div>
                <div class="flex justify-between"><span>Tanggal Jadwal:</span> <span class="font-semibold">{{ $bookingResult['date'] }}</span></div>
                <div class="flex justify-between"><span>Jam Kedatangan:</span> <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $bookingResult['slot'] }} WIB</span></div>
            </div>

            <button onclick="window.location.reload()" class="mt-8 px-6 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-900 transition">
                Buat Pemesanan Baru
            </button>
        </div>
    @else
        <form wire:submit.prevent="submitBooking" class="space-y-6">
            @if (session()->has('error'))
                <div class="p-4 bg-red-100 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">1. Pilih Tanggal Kedatangan</label>
                <input type="date" wire:model.live="selectedDate" min="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500">
                @error('selectedDate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">2. Pilih Barber Terbaik Anda</label> 
                <select wire:model.live="selectedBarber" class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500">
                    <option value="">-- Silakan Pilih Barber --</option>
                    @foreach($barbers as $barber)
                        <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                    @endforeach
                </select>
                @error('selectedBarber') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            @if($selectedBarber && $selectedDate)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">3. Pilih Jam Slot yang Tersedia</label> 
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach($this->availableSlots as $slot)
                            @php $isBooked = in_array($slot, $bookedSlots); @endphp
                            <button type="button" 
                                wire:click="$set('selectedSlot', '{{ $slot }}')"
                                @if($isBooked) disabled @endif
                                class="py-2.5 text-xs font-semibold rounded-lg border text-center transition
                                    {{ $selectedSlot === $slot ? 'bg-blue-600 text-white border-blue-600 shadow' : '' }}
                                    {{ !$isBooked && $selectedSlot !== $slot ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:border-blue-500' : '' }}
                                    {{ $isBooked ? 'bg-gray-100 dark:bg-gray-900 text-gray-400 dark:text-gray-600 border-gray-200 line-through cursor-not-allowed' : '' }}
                                ">
                                {{ $slot }}
                                <div class="text-[9px] font-normal tracking-tight">
                                    {{ $isBooked ? 'Penuh' : 'Tersedia' }}
                                </div>
                            </button>
                        @endforeach
                    </div>
                    @error('selectedSlot') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            @endif

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