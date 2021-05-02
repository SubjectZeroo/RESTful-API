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
}
