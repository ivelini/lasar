<?php


namespace App\Repositories\Pages;

use App\Models\Page as Model;
use App\Repositories\CoreRepository;

class PagesRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getAllForIndex(...$attributes)
    {
        $models = $this->startConditions()
            ->with('seo', 'category')
            ->get();

        $models->map(function ($item) {
            return $this->getRelations($item);
        });

        $arr = $this->modelsAttributesToArray($models);

        return $arr;
    }

    public function getEdit($id)
    {
        $model = $this->startConditions()
            ->where('id', $id)
            ->with('seo', 'category')
            ->first();

        $model = $this->getRelations($model)->getAttributes();

        return $model;
    }

    private function getRelations($item)
    {
        $item->categoryName = !empty($item->category->name) ? $item->category->name : null;
        $item->categoryId = !empty($item->category->id) ? $item->category->id : null;
        $item->title = !empty($item->seo->title) ? $item->seo->title : null;
        $item->description = !empty($item->seo->description) ? $item->seo->description : null;
        $item->label = !empty($item->seo->label) ? $item->seo->label : null;
        return $item;
    }
}
