<?php

namespace App\Livewire\Tenants\Admin\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

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
