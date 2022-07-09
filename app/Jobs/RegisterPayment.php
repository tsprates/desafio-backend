<?php

namespace App\Jobs;

use App\Exceptions\PaymentServiceInavailableException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class RegisterPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->registerTransferInGatewayPayment();
    }


    /**
     * Register transfer in payment gateway.
     *
     * @return void
     * @throws PaymentServiceInavailableException
     */
    private function registerTransferInGatewayPayment()
    {
        $url = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
        $response = Http::get($url);

        if ($response['message'] !== 'Autorizado') {
            throw new PaymentServiceInavailableException();
        }
    }
}
