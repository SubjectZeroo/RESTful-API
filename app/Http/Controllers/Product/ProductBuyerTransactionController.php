<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {

        $rules = [
            'quantity' => 'required|integer|min:1'
        ];

        $this->validate($request, $rules);

        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('El comprando tiene que ser diferente ded vendedor', 409);
        }

        if (!$buyer->isVerified()) {
            return $this->errorResponse('El comprador tiene que esta verificado', 409);
        }

        if (!$product->seller->isVerified()) {
            return $this->errorResponse('El vendedor tiene que esta verificado', 409);
        }

        if (!$product->isAvaible()) {
            return $this->errorResponse('El producto no esta disponible', 409);
        }

        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('No hay unidades suficiente de este producto para esta transaccion', 409);
        }

    return DB::transaction( function() use($request, $product, $buyer) {

        $product->quantity -= $request->quantity;
        $product->save();

        $transaction = Transaction::create([
            'quantity' => $request->quantity,
            'buyer_id' => $buyer->id,
            'product_id' => $product->id,
        ]);

        return $this->showOne($transaction, 201);
    });

    }

}
