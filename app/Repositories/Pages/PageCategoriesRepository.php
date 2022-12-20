<?php


namespace App\Repositories\Pages;

use App\Models\PageCategory as Model;
use App\Repositories\CoreRepository;

class PageCategoriesRepository extends CoreRepository
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
            ->with('seo')
            ->get();

        $models->map(function ($item) {
            $item->title = !empty($item->seo->title) ? $item->seo->title : null;
            $item->description = !empty($item->seo->description) ? $item->seo->description : null;
            $item->label = !empty($item->seo->label) ? $item->seo->label : null;
            return $item;
        });

        $arr = $this->modelsAttributesToArray($models);

        return $arr;
    }
}
