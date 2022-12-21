<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */
    protected $table = 'withdrawal';
     //protected $guard = 'admin';
     protected $guarded = [];
    protected $fillable = [

        'user_id',
        'amount',
        'trx',

    ];
}