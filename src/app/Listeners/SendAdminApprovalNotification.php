<?php

namespace App\Listeners;

use App\Mail\AccountPendingApproval;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendAdminApprovalNotification implements  ShouldQueue
{
    public function handle(Registered $event): void
    {
        Mail::to($event->user->email)->send(new AccountPendingApproval($event->user));
    }
}
