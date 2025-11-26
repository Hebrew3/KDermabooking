<?php

namespace App\View\Components\Partials;

use Illuminate\View\Component;
use Illuminate\View\View;

class Navigation extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.partials.navigation');
    }
}
