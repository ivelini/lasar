<?php


namespace App\Repositories\Catalog;

use App\Models\VendorCode as Model;
use App\Repositories\CoreRepository;

class VendorCodeRepository extends CoreRepository
{
    const RELATION_TYPE = 'vendor_code';

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
