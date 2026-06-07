<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component
{
    /**
     * Mendengarkan sinyal event dari JavaScript layout utama untuk memicu logout asli.
     */
    #[On('trigger-breeze-logout')]
    public function executeBreezeLogout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="hidden">
    </div>
