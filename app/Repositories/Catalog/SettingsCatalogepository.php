<?php


namespace App\Repositories\Catalog;

use App\Models\SettingsCatalog as Model;
use App\Repositories\CoreRepository;

class SettingsCatalogepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass(): string
    {
        return Model::class;
    }

    public static function getModel($action, $type)
    {
        $model = (new SettingsCatalogepository)->startConditions()
            ->where('action', $action)
            ->where('type', $type)
            ->first();

        return $model;

    }
}
