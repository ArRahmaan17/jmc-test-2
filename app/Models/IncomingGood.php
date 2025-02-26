<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomingGood extends Model
{
    protected $fillable = ['operatorId', 'categoryId', 'subCategoryId', 'source', 'mail_number', 'attachment'];

    public function details(): HasMany
    {
        return $this->hasMany(IncomingGoodDetail::class, 'incomingId', 'id');
    }
}
