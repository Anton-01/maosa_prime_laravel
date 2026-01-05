<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SimpleAlertMessage extends Component
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
    public function render(): View|Closure|string
    {
        $alertData = session('statusAdm');

        $alertType = $alertData['alert_type'];
        $mainMessage = $alertData['main_message'];

        return view('components.simple-alert-message', compact('mainMessage','alertType'));
    }
}
