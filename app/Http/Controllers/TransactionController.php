<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Displays user's transactions.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User         $user
     * @return \Illuminate\Http\Response
     */
    public function getTransactions(Request $request, User $user)
    {
        return $user->transactions()->get();
    }

    /**
     * Transfer toUser the user the given value.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User         $user
     * @return \Illuminate\Http\Response
     */
    public function transfer(Request $request)
    {
        $value = $request->input('value');
        $payee = User::findOrFail($request->input('payee'));
        $payer = User::findOrFail($request->input('payer'));

        try {
            $payer->transferTo($payee, $value);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'errors' => $e->getMessage(),
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'value' => $value,
                'payee' => $payee->id,
                'payer' => $payer->id,
            ],
        ], 200);
    }
}
