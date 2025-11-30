<?php

namespace App\View\Components;

use Illuminate\Support\Facades\App;
use Illuminate\View\Component;

class LanguageSwitcher extends Component
{
    public $currentLocale;

    public $supportedLocales;

    public function __construct()
    {
        $this->currentLocale = App::getLocale();
        $this->supportedLocales = config('app.supported_locales', ['en', 'fr', 'es']);

    }

    public function render()
    {

        return view('components.language-switcher');
    }
}
