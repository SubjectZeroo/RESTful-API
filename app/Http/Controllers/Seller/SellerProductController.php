<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;

        return $this->showAll($products);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] =  $request->image->store('');
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return $this->showOne($product);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        // $rules = [
        //     'quantity' => 'required|integer|min:1',
        //     'status' => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,
        //     'image' => 'image',
        // ];

        // $this->validate($request, $rules);

        $this->validate(request(), [
            'quantity' => 'required|integer|min:1',
            'status' => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,
            'image' => 'image',
        ]);

        $this->checkSeller($seller, $product);

        $product->fill($request->only([
            'name',
            'description',
            'quantity',
        ]));


        if ($request->has('status')) {
            $product->status = $request->status;

            if ($product->isAvaible() && $product->categories()->count() == 0) {
                return $this->errorResponse('Un producto activo debe tener una categoria',409);
            }
        }

        if ($request->hasFile('image')) {
            Storage::delete($product->image);

            $product->image = $request->image->store('');
        }

        if ($product->isClean()) {
            return $this->errorResponse('Nesecita especificar un valor distinto para actualizar', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);
        $product->delete();
        Storage::delete($product->image);
        return $this->showOne($product);
    }


    protected function checkSeller(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {
            throw new HttpException("El seller especifico no es el actual seller de producto",422);
        }
    }
}
