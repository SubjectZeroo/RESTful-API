<?php

namespace App\Transformers;

use App\Models\Buyer;
use League\Fractal\TransformerAbstract;

class BuyerTransfomer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Buyer $buyer)
    {
        return [
            'identifier' => (int)$buyer->id,
            'name' => (string)$buyer->name,
            'email' => (string)$buyer->email,
            'isVerified' => (int)$buyer->verified,
            'creationDate' => $buyer->created_at,
            'lastChange' => $buyer->update_at,
            'deleteDate' => isset($buyer->deleted_at) ? (string)$buyer->deleted_at : null,
        ];
    }


    public static function originalAttribute($index)
    {
        $attributes = [
            'identifier' => 'id',
            'name' =>  'name',
            'email' => 'email',
            'isVerified' => 'verified',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deleteDate' => 'deleted_at',
        ];


        return isset($attributes[$index]) ? $attributes[$index] : null;

    }


    public static function transformedAttribute($index)
    {
        $attributes = [
            'id ' => 'identifier',
            'name' =>  'name',
            'email' => 'email',
            'verified ' => 'isVerified' ,
            'created_at ' => 'creationDate',
            'updated_at ' => 'lastChange',
            'deleted_at' => 'deleteDate',
        ];


        return isset($attributes[$index]) ? $attributes[$index] : null;

    }
}
