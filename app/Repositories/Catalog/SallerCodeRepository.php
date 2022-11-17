<?php


namespace App\Repositories\Catalog;

use App\Models\SallerCode as Model;
use App\Repositories\CoreRepository;

class SallerCodeRepository extends CoreRepository
{
    const RELATION_TYPE = 'saller_code';

    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getId($attributeName, $attribute)
    {
        $model = $this->startConditions()
            ->where($attributeName, $attribute)
            ->first();

        return $this->checkExist($model, 'id');
    }

}
