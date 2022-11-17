<?php


namespace App\Repositories\Catalog;

use App\Models\ModelPosition as Model;
use App\Repositories\CoreRepository;

class ModelPositionRepository extends CoreRepository
{
    const RELATION_TYPE = 'modelPosition';

    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getIdAndName()
    {
        $idAndName = $this->startConditions()->select('id', 'name')->get();

        $idAndName = $idAndName->map(function ($item) {
            return [ 'id' => $item->id, 'name' => $item->name];
            });

        return $idAndName;
    }

    public function getModelsIdAndPathForImportImageTable()
    {
        $models = $this->startConditions()
            ->with([
                'tires', 'tires.images'
            ])
            ->get();

        $data = collect();
        foreach ($models as $itemModel) {
            if ($itemModel->tires->count() > 0) {
                foreach ($itemModel->tires as $tire) {
                    if ($tire->images->count() > 0) {
                        $data->push([
                            'path' => $tire->images->first()->path,
                            'imageable_id' => $itemModel->id,
                            'imageable_type' => self::RELATION_TYPE
                        ]);
                    }
                }
            }
        };

        $data = $data->unique("imageable_id");

        return $data;
    }
}
