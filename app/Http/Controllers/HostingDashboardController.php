<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\MvpRole;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HostingDashboardController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $role = $user->mvp_role;

        $servicesCount = Service::query()->count();
        $openInvoicesCount = Invoice::query()->whereIn('status', [
            InvoiceStatus::Sent,
            InvoiceStatus::PartiallyPaid,
            InvoiceStatus::Overdue,
        ])->count();
        $domainsExpiringCount = Domain::query()
            ->whereNotNull('expires_on')
            ->whereBetween('expires_on', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();
        $revenueMtd = (float) Payment::query()
            ->where('paid_at', '>=', now()->startOfMonth())
            ->sum('amount');

        $view = match ($role) {
            MvpRole::SuperAdmin => 'hosting.dashboards.super-admin',
            MvpRole::Reseller => 'hosting.dashboards.reseller',
            MvpRole::SubReseller => 'hosting.dashboards.sub-reseller',
            MvpRole::Client => 'hosting.dashboards.client',
        };

        return view($view, compact(
            'servicesCount',
            'openInvoicesCount',
            'domainsExpiringCount',
            'revenueMtd'
        ));
    }
}
