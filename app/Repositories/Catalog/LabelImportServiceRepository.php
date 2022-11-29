<?php


namespace App\Repositories\Catalog;

use App\Models\LabelImportCatalogService as Model;
use App\Repositories\CoreRepository;

class LabelImportServiceRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getAll()
    {
        $models = $this->getAllModelsByAttributes('id', 'title', 'name');
        $items = $this->modelsAttributesToArray($models);

        return $items;
    }

}
