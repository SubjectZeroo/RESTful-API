<?php

namespace App\Models;

use App\Scopes\BuyerScope;
use App\Transformers\BuyerTransfomer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends User
{
    use HasFactory;

    public $transformer = BuyerTransfomer::class;
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new BuyerScope);
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


}
