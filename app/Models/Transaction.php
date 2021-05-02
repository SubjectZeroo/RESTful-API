<?php

namespace App\Models;

use App\Transformers\TransactionTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;
    public $transformer = TransactionTransformer::class;
    protected $dates = ['delated_at'];
    // protected $filable = [
    //     'quantity',
    //     'buyer_id',
    //     'product_id',
    // ];


    protected  $guarded = [];
    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
