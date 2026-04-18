<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client — Hosting</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 p-8">
    <h1 class="text-2xl font-semibold mb-6">Client dashboard</h1>
    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 max-w-5xl">
        <div class="rounded-lg border border-slate-800 bg-slate-900/60 p-4">
            <dt class="text-sm text-slate-400">My services</dt>
            <dd class="text-3xl font-bold">{{ $servicesCount }}</dd>
        </div>
        <div class="rounded-lg border border-slate-800 bg-slate-900/60 p-4">
            <dt class="text-sm text-slate-400">Open invoices</dt>
            <dd class="text-3xl font-bold">{{ $openInvoicesCount }}</dd>
        </div>
        <div class="rounded-lg border border-slate-800 bg-slate-900/60 p-4">
            <dt class="text-sm text-slate-400">Domains expiring (30d)</dt>
            <dd class="text-3xl font-bold">{{ $domainsExpiringCount }}</dd>
        </div>
        <div class="rounded-lg border border-slate-800 bg-slate-900/60 p-4">
            <dt class="text-sm text-slate-400">Payments (MTD)</dt>
            <dd class="text-3xl font-bold">{{ number_format($revenueMtd, 2) }}</dd>
        </div>
    </dl>
    <p class="mt-8 text-slate-500 text-sm"><a class="text-sky-400 hover:underline" href="/dashboard">Tyro Dashboard</a></p>
</body>
</html>
