<x-app-layout>
    <div class="py-6 max-w-4xl mx-auto">

        <div class="mb-8 px-4 sm:px-0">
            <h2 class="text-2xl font-bold tracking-tight text-white">Pengaturan Akun Kasir</h2>
            <p class="text-xs text-gray-400 mt-1">Perbarui informasi profil, alamat email, keamanan kata sandi, atau hapus akun Anda secara aman.</p>
        </div>

        <div class="space-y-6 sm:px-0">
            <div class="p-6 bg-gray-800/40 backdrop-blur-sm shadow-xl rounded-xl border border-gray-700/40">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-6 bg-gray-800/40 backdrop-blur-sm shadow-xl rounded-xl border border-gray-700/40">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-6 bg-gray-800/40 backdrop-blur-sm shadow-xl rounded-xl border border-gray-700/40">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
