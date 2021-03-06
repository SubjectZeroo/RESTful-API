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

            'links' => [
                [
                    'rel' => 'self',
                    'href' =>  route('categories.show', $category->id),
                ],

                [
                    'rel' => 'category.buyers',
                    'href' =>  route('categories.buyers.index', $category->id),
                ],
                [
                    'rel' => 'category.products',
                    'href' =>  route('categories.products.index', $category->id),
                ],
                [
                    'rel' => 'category.sellers',
                    'href' =>  route('categories.sellers.index', $category->id),
                ]
            ]

        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'identifier' => 'id',
            'title' =>  'name',
            'description' => 'description',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deleteDate' => 'deleted_at',
        ];


        return isset($attributes[$index]) ? $attributes[$index] : null;

    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'identifier',
            'name' =>  'title',
            'description' => 'description',
            'created_at' => 'creationDate ',
            'updated_at' => 'lastChange',
            'deleted_at ' => 'deleteDate',
        ];


        return isset($attributes[$index]) ? $attributes[$index] : null;

    }
}
