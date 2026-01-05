<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AlertMessage extends Component
{
    public function __construct(){
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $alertData = session('status');

        if (!$alertData && session()->has('errors')) {
            $errors = session('errors');
            if ($errors->has('email')) {
                $alertData = [
                    'main_message' => 'Error de Autenticación',
                    'description' => $errors->first('email'),
                    'alert_type' => 'danger'
                ];
            } else if ($errors->any()) {
                $alertData = [
                    'main_message' => '¡Atención!',
                    'description' => 'Se han encontrado errores. Por favor, verifica los campos.',
                    'alert_type' => 'warning'
                ];
            }
        }

        $alertType = $alertData['alert_type'];
        $mainMessage = $alertData['main_message'];
        $description = $alertData['description'];

        $iconClass = 'fa-circle-info';
        if ($alertType === 'danger') {
            $iconClass = 'fa-circle-xmark';
        } elseif ($alertType === 'warning') {
            $iconClass = 'fa-triangle-exclamation';
        } elseif ($alertType === 'success') {
            $iconClass = 'fa-circle-check';
        }

        return view('components.alert-message', compact('mainMessage', 'description', 'alertType', 'iconClass'));
    }
}
