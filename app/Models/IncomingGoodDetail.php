<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingGoodDetail extends Model
{
    protected $fillable = ['amount', 'name', 'price', 'unit', 'total', 'expired_at', 'incomingId', 'status'];
}
