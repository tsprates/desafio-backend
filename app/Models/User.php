<?php

namespace App\Models;

use App\Exceptions\InsufficientException;
use App\Exceptions\InvalidTransferException;
use App\Jobs\RegisterPayment;
use App\Jobs\SendMail;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'document', // CPF ou CNPJ
        'logist',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'balance' => 'float',
        'logist' => 'boolean',
    ];

    /**
     * Has many transactions.
     *
     * @return Builder
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class)
            ->orWhere(function (Builder $query) {
                return $query->where('to_user_id', '=', $this->id);
            })
            ->orderBy('date', 'desc');
    }

    /**
     * Register the transaction.
     *
     * @param User $to
     * @param float $value The amount to be transfered
     * @return void
     * @throws InsufficientException
     * @throws InvalidArgumentException
     */
    public function transferTo(User $to, float $value)
    {
        if ($this->logist) {
            throw new InvalidTransferException();
        }

        if ($value < 0) {
            throw new InvalidArgumentException('Invalid given value.');
        }

        if ($this->balance < $value) {
            throw new InsufficientException();
        }

        (new RegisterPayment())->dispatch();

        (new SendMail())->dispatch();
        
        DB::beginTransaction();
        try {
            $this->addTransaction($to, $value);
    
            $this->balance -= $value;
            $this->save();

            $to->balance += $value;
            $to->save();
            
            DB::commit();
        } catch (Exception $e) {
            Log::error($e);
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Register a user's transaction.
     *
     * @param User $to
     * @param float $value
     * @return void
     */
    private function addTransaction(User $to, float $value)
    {
        $transaction = new Transaction();
        $transaction->value = $value;
        $transaction->user_id = $this->id;
        $transaction->to_user_id = $to->id;
        $transaction->save();
    }
}
