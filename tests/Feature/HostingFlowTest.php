<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\MvpRole;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class HostingFlowTest extends TestCase
{
    public function test_client_cannot_query_another_clients_invoice(): void
    {
        $reseller = User::factory()->create(['mvp_role' => MvpRole::Reseller]);
        $clientA = User::factory()->create([
            'mvp_role' => MvpRole::Client,
            'parent_id' => $reseller->id,
        ]);
        $clientB = User::factory()->create([
            'mvp_role' => MvpRole::Client,
            'parent_id' => $reseller->id,
        ]);

        $product = Product::factory()->create();
        Service::factory()->create([
            'product_id' => $product->id,
            'client_user_id' => $clientA->id,
            'reseller_user_id' => $reseller->id,
        ]);

        $invoice = Invoice::factory()->create([
            'client_user_id' => $clientA->id,
            'reseller_user_id' => $reseller->id,
            'status' => InvoiceStatus::Sent,
        ]);

        $this->actingAs($clientB);
        $this->assertFalse(Invoice::query()->whereKey($invoice->id)->exists());

        $this->actingAs($clientA);
        $this->assertTrue(Invoice::query()->whereKey($invoice->id)->exists());
    }

    public function test_internal_ticket_message_is_hidden_from_client_via_policy(): void
    {
        $client = User::factory()->create(['mvp_role' => MvpRole::Client]);
        $staff = User::factory()->create(['mvp_role' => MvpRole::Reseller]);

        $ticket = Ticket::factory()->create([
            'client_user_id' => $client->id,
            'opened_by_user_id' => $client->id,
        ]);

        $internal = TicketMessage::factory()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $staff->id,
            'is_internal' => true,
        ]);

        $this->assertFalse(Gate::forUser($client)->allows('view', $internal));
    }

    public function test_payment_created_sends_mail_to_client(): void
    {
        Notification::fake();

        $client = User::factory()->create(['mvp_role' => MvpRole::Client]);
        $invoice = Invoice::factory()->create([
            'client_user_id' => $client->id,
            'status' => InvoiceStatus::Sent,
        ]);

        Payment::factory()->for($invoice)->create([
            'amount' => 10.50,
        ]);

        Notification::assertSentTo($client, PaymentReceivedNotification::class);
    }
}
