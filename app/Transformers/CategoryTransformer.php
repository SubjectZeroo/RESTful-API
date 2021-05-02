<?php

namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
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
    public function transform(Category $category)
    {
        return [
            'identifier' => (int)$category->id,
            'title' => (int)$category->name,
            'description' => (int)$category->description,
            'creationDate' => $category->created_at,
            'lastChange' => $category->update_at,
            'deleteDate' => isset($category->deleted_at) ? (string) $category->deleted_at : null,
        ];
    }
}
