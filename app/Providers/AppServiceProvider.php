<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TicketMessage;
use App\Models\User;
use App\Observers\InvoiceObserver;
use App\Observers\PaymentObserver;
use App\Observers\TicketMessageObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('testing')) {
            $this->loadMigrationsFrom([
                base_path('vendor/hasinhayder/tyro/database/migrations'),
                base_path('vendor/hasinhayder/tyro-login/database/migrations'),
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Invoice::observe(InvoiceObserver::class);
        Payment::observe(PaymentObserver::class);
        TicketMessage::observe(TicketMessageObserver::class);

        Gate::before(function (?User $user, string $ability) {
            if ($user === null) {
                return null;
            }

            if ($user->isSuperAdmin()) {
                return true;
            }

            return null;
        });
    }
}
