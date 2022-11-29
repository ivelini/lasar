<?php


namespace App\Repositories\Catalog;

use App\Models\ApiUrlSaller as Model;
use App\Repositories\CoreRepository;

class ApiUrlSallersRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getAllForTableUpdatePrice()
    {
        $models = $this->startConditions()
            ->with(['saller', 'storage', 'label'])
            ->get();

        $items = $models->map(function ($itemUrl) {
            return [
                'urlId' => $itemUrl->id,
                'sallerName' => $itemUrl->saller->name,
                'url' => $itemUrl->url,
                'storage' => $itemUrl->storage != null ? $itemUrl->storage->name : null,
                'labelId' => $itemUrl->label->id,
                'labelTitle' => $itemUrl->label->title,

            ];
        });

        return $items;
    }
}
