<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_all_users_transactations()
    {
        $user = User::factory()->create();

        Transaction::factory(['user_id' => $user->id])
            ->count(3)
            ->create();

        $this->get('/api/transactions/' . $user->id)
            ->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_transfer_transaction()
    {
        Http::fake([
            'run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6' => Http::response([
                'message' => 'Autorizado'
            ], 200),
            'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04' => Http::response([
                'message' => 'Enviado'
            ], 200),
        ]);

        $payee = User::factory()->create();
        $payer = User::factory(['logist' => false])->create();

        $this->post('/api/transactions', [
                'payee' => $payee->id,
                'payer' => $payer->id,
                'value' => $this->makeRandomValue($payer->balance),
            ])
            ->assertStatus(200)
            ->assertJsonPath('status', 'success');
    }

    public function test_transfer_not_authorized_transaction()
    {
        Http::fake([
            'run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6' => Http::response([
                'message' => 'Não Autorizado'
            ], 200),
            'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04' => Http::response([
                'message' => 'Enviado'
            ], 200),
        ]);

        $payee = User::factory()->create();
        $payer = User::factory(['logist' => false])->create();

        $this->post('/api/transactions', [
                'payee' => $payee->id,
                'payer' => $payer->id,
                'value' => $this->makeRandomValue($payer->balance),
            ])
            ->assertStatus(403)
            ->assertExactJson([
                'status' => 'failed', 'errors' => 'Payment service inavailable.',
            ]);
    }

    public function test_transfer_not_sent_the_notification_transaction()
    {
        Http::fake([
            'run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6' => Http::response([
                'message' => 'Autorizado'
            ], 200),
            'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04' => Http::response([
                'message' => 'Não Enviado'
            ], 200),
        ]);

        $payee = User::factory()->create();
        $payer = User::factory(['logist' => false])->create();

        $this->post('/api/transactions', [
                'payee' => $payee->id,
                'payer' => $payer->id,
                'value' => $this->makeRandomValue($payer->balance),
            ])
            ->assertStatus(403)
            ->assertExactJson([
                'status' => 'failed', 'errors' => 'There was a problem sending the notification.',
            ]);
    }

    public function test_transfer_using_invalid_value()
    {
        $payee = User::factory()->create();
        $payer = User::factory(['logist' => false])->create();

        $this->post('/api/transactions', [
                'payee' => $payee->id,
                'payer' => $payer->id,
                'value' => $payer->balance + 99999,
            ])
            ->assertStatus(403)
            ->assertExactJson([
                'status' => 'failed', 'errors' => 'Insufficient balance.',
            ]);
    }

    public function test_transfer_beetween_logists()
    {
        $payee = User::factory(['logist' => true])->create();
        $payer = User::factory(['logist' => true])->create();

        $this->post('/api/transactions', [
                'payee' => $payee->id,
                'payer' => $payer->id,
                'value' => $this->makeRandomValue($payer->balance),
            ])
            ->assertStatus(403)
            ->assertExactJson([
                'status' => 'failed', 'errors' => 'Logists cannot send the value.',
            ]);
    }

    /**
     * Makes a random value according to the available balance.
     *
     * @param float $balance
     * @return float
     */
    private function makeRandomValue($balance)
    {
        return rand(0, $balance) / 10.0;
    }
}
