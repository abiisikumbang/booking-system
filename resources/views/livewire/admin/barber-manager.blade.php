<?php

use Livewire\Volt\Component;
use App\Models\Barber;

new class extends Component {
    // Properti Manipulasi Data
    public $barberId = null;
    public $name = '';
    public $is_active = true;

    // Properti Status UI Modul
    public $isEditing = false;

    // Aturan Validasi Input Nama Barber
    protected $rules = [
        'name' => 'required|string|min:3|max:50',
    ];

    // Mengaktifkan pesan error kustom bahasa Indonesia
    protected $messages = [
        'name.required' => 'Nama barber wajib diisi.',
        'name.min' => 'Nama barber minimal terdiri dari 3 karakter.'
    ];

    // Fungsi: Reset Form Input
    public function resetForm()
    {
        $this->reset(['barberId', 'name', 'is_active', 'isEditing']);
    }

    // Fungsi: Simpan Barber Baru ATAU Update Barber Lama (Upsert)
    public function saveBarber()
    {
        $this->validate();

        if ($this->isEditing) {
            $barber = Barber::find((int) $this->barberId);
            if ($barber) {
                $barber->update([
                    'name' => $this->name,
                    // Konversi kembali nilai select string menjadi boolean saat disimpan ke database
                    'is_active' => (bool) $this->is_active
                ]);
                session()->flash('message', "Data barber '{$this->name}' berhasil diperbarui!");
            }
        } else {
            Barber::create([
                'name' => $this->name,
                'is_active' => (bool) $this->is_active
            ]);
            session()->flash('message', "Barber baru '{$this->name}' berhasil ditambahkan ke toko!");
        }

        $this->resetForm();
    }

    // Fungsi: Tarik Data ke Form saat Admin klik Edit
    public function editBarber($id)
    {
        // Paksa id menjadi integer untuk memastikan pencarian Eloquent akurat
        $barber = Barber::find((int) $id);

        if ($barber) {
            $this->barberId = $barber->id;
            $this->name = $barber->name;

            // Konversi boolean database menjadi string "1" atau "0" agar klop dengan komponen <select> HTML
            $this->is_active = $barber->is_active ? "1" : "0";

            $this->isEditing = true;
        }
    }


    // Fungsi: Ubah Status Aktif/Libur Secara Instan Melalui Switch Toggle di Tabel
    public function toggleStatus($id)
    {
        $barber = Barber::find($id);
        if ($barber) {
            $barber->update(['is_active' => !$barber->is_active]);
            session()->flash('message', "Status kehadiran barber '{$barber->name}' berhasil diubah.");
        }
    }

    // Fungsi: Hapus Data Barber dari Toko
    public function deleteBarber($id)
    {
        $barber = Barber::find($id);
        if ($barber) {
            $barber->delete();
            session()->flash('message', "Data barber berhasil dihapus dari sistem.");
        }
    }

    // Ambil data barber terbaru secara dinamis untuk dirender ke tabel
    public function with(): array
    {
        return [
            'barbers' => Barber::orderBy('name', 'asc')->get()
        ];
    }
}; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6">

    <div class="bg-gray-800/40 backdrop-blur-sm rounded-xl border border-gray-700/40 p-6 h-fit shadow-xl">
        <h3 class="text-lg font-bold text-white mb-1">
            {{ $isEditing ? '✏️ Edit Data Barber' : '➕ Tambah Barber Baru' }}
        </h3>
        <p class="text-xs text-gray-400 mb-6">Kelola profil pemangkas rambut Mr. Klimis.</p>

        <form wire:submit.prevent="saveBarber" class="space-y-5">
            <div>
                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Nama Lengkap Barber</label>
                <input type="text" wire:model="name" placeholder="Contoh: Ricky Barber" class="w-full rounded-lg border-gray-700 bg-gray-900 text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Status Operasional</label>
                <select wire:model="is_active" class="w-full rounded-lg border-gray-700 bg-gray-900 text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="1">Aktif / Masuk Kerja</option>
                    <option value="0">Tidak Aktif / Libur</option>
                </select>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-md">
                    {{ $isEditing ? 'Simpan Perubahan' : 'Daftarkan Barber' }}
                </button>
                @if($isEditing)
                    <button type="button" wire:click="resetForm" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg text-xs font-bold transition">
                        Batal
                    </button>
                @endif
            </div>
        </form>
    </div>

    <div class="lg:grid lg:col-span-2 bg-gray-800/40 backdrop-blur-sm rounded-xl border border-gray-700/40 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-700/40">
            <h3 class="text-lg font-bold text-white">Daftar Tim Pemangkas</h3>
            <p class="text-xs text-gray-400 mt-1">Daftar seluruh barber yang terdaftar di database sistem saat ini.</p>
        </div>

        @if (session()->has('message'))
            <div class="m-6 p-3 bg-green-500/10 border border-green-500/20 text-green-400 text-xs font-medium rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700/50">
                <thead class="bg-gray-900/40">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Inisial</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Barber</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($barbers as $b)
                        <tr class="hover:bg-gray-800/20 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-9 h-9 bg-gray-700 text-gray-200 rounded-full flex items-center justify-center font-bold text-xs uppercase border border-gray-600/30">
                                    {{ strtoupper(substr($b->name, 0, 2)) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-200">
                                {{ $b->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button" wire:click="toggleStatus({{ $b->id }})" class="inline-flex items-center">
                                    @if($b->is_active)
                                        <span class="px-2.5 py-1 text-[11px] font-semibold bg-green-500/10 text-green-400 border border-green-500/20 rounded-full">● Aktif</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[11px] font-semibold bg-red-500/10 text-red-400 border border-red-500/20 rounded-full">✕ Libur</span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium space-x-2">
                                <button type="button" wire:click="editBarber({{ $b->id }})" class="px-2.5 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded transition">
                                    Ubah
                                </button>
                                <button type="button"
                                        wire:click="deleteBarber({{ $b->id }})"
                                        wire:confirm="Hapus data barber ini? Menghapus barber akan otomatis membatalkan seluruh jadwal booking milik barber yang bersangkutan!"
                                        class="px-2.5 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 rounded transition">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                Belum ada data nama barber terdaftar di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
