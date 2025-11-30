<?php

namespace App\Livewire\Tenants\Admin\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Sidebar extends Component
{
    public function logout()
    {
        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.tenants.admin.components.sidebar', [
            'user' => Auth::user(),
        ]);
    }
}
