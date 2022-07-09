<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * No timestamps.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'value',
        'payer',
        'payee',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'to_user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime:d/m/Y à\s H:i:s',
        'value' => 'double',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['payer', 'payee'];

    /**
     * Get the payer.
     *
     * @return string
     */
    public function getPayerAttribute()
    {
        return $this->user_id;
    }

    /**
    * Set the payer.
    *
    * @param  int|string  $value
    * @return void
    */
    public function setPayerAttribute($value)
    {
        $this->attributes['user_id'] = $value;
    }

    /**
     * Get the payee.
     *
     * @return string
     */
    public function getPayeeAttribute()
    {
        return $this->to_user_id;
    }

    /**
    * Set the payer.
    *
    * @param  int|string  $value
    * @return void
    */
    public function setPayeeAttribute($value)
    {
        $this->attributes['to_user_id'] = $value;
    }
}
