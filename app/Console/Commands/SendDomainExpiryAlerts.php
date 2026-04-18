<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Notifications\DomainExpiringNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendDomainExpiryAlerts extends Command
{
    protected $signature = 'hosting:send-domain-expiry-alerts';

    protected $description = 'Notify clients when domains approach expiry';

    public function handle(): int
    {
        $daysList = config('hosting.domain_expiry_alert_days_before', []);
        $sent = 0;
        $today = now()->toDateString();

        foreach ($daysList as $days) {
            $target = now()->addDays((int) $days)->toDateString();

            Domain::withoutGlobalScopes()
                ->with('service.client')
                ->whereDate('expires_on', $target)
                ->cursor()
                ->each(function (Domain $domain) use (&$sent, $days, $today): void {
                    $cacheKey = "hosting:domain-expiry:{$domain->id}:{$days}:{$today}";
                    if (Cache::has($cacheKey)) {
                        return;
                    }

                    $client = $domain->service?->client;
                    if ($client === null) {
                        return;
                    }

                    $client->notify(new DomainExpiringNotification($domain, (int) $days));
                    Cache::put($cacheKey, true, now()->addDay());
                    $sent++;
                });
        }

        $this->info("Queued {$sent} domain expiry alert(s).");

        return self::SUCCESS;
    }
}
