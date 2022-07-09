<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/users', [UserController::class, 'index'])->name('user.index');

Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show');

Route::post('/users', [UserController::class, 'create'])->name('user.create');

Route::put('/users/{user}', [UserController::class, 'update'])->name('user.update');

Route::delete('/users/{user}', [UserController::class, 'delete'])->name('user.delete');

Route::get('/transactions/{user}', [TransactionController::class, 'getTransactions'])->name('user.transactions');

Route::post('/transactions', [TransactionController::class, 'transfer'])->name('transaction.transfer');
