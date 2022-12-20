<?php


namespace App\Helpers;


use App\Models\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DbHelper
{
    private $model;
    /**
     *  массив вида
     *  [
     *      [
     *          'relationName' => relationName,
     *          'data' => []
     *      ],
     *      [
     *          'relationName' => relationName,
     *          'data' => []
     *      ],
     *  ]
     */

    private $relations = [];


    public function __construct($modelVsModelName = 'User')
    {
        $this->setModel($modelVsModelName);
    }

    public function setModel($modelVsModelName)
    {
        $model = $modelVsModelName;

        if (is_object($model) == false) {

            $modelClass = 'App\\Models\\' .$model;
            $model = new $modelClass();
        }

        $this->model =  $model;

        return $this;
    }

    public function addRelation(string $relationName, array $data = null)
    {
        array_push($this->relations, ['relationName' => $relationName, 'data' => $data]);
        return $this;
    }

    public function save(array $data)
    {
        $model =  $this->model->create($data);

        if(count($this->relations) > 0) $this->createRelations($model);

        return $model;
    }

    public function saveAll(array $dataAll)
    {
        $dataAll = collect($dataAll);
        $dataAll->each(function ($item) {
            $this->save($item);
        });

        return true;
    }

    public function update(array $data)
    {
        foreach($data as $key => $value) {
            $this->model->$key = $value;
        }

        $this->model->save();
        if(count($this->relations) > 0) $this->updateRelations($this->model);

        return $this->model;
    }

    public function delete($id): bool
    {
        if(count($this->relations) > 0) {
            foreach($this->relations as $relation) {
                $relationName = $relation['relationName'];
                $relationModel = $this->model
                    ->find($id)
                    ->$relationName;

                if(!empty($relationModel)) $relationModel->delete();
            }
        }
        $this->model->destroy($id);
        return true;
    }

    public function updateTable(array $data)
    {
        $tableName = $this->model->getTable();

        $dbUpdateRow = function ($tableName, $row) {
            $updateData = [];
            foreach ($row as $key => $value) {
                if ($key != 'id') $updateData[$key] = $value;
            }

            DB::table($tableName)
                ->where('id', $row['id'])
                ->update($updateData);
        };

        if (array_key_first($data) == 0) {
            foreach($data as $row) {
                $dbUpdateRow($tableName, $row);
            }
        }
        else {
            $dbUpdateRow($tableName, $data);
        }
        return true;
    }

    private function createRelations($model): bool
    {
        foreach ($this->relations as $relation) {
            $relationName = $relation['relationName'];
            $model->$relationName()->create($relation['data']);
        }

        return true;
    }

    private function updateRelations($model): bool
    {
        foreach ($this->relations as $relation) {
            $relationName = $relation['relationName'];
            $relationModel = $model->$relationName;

            foreach($relation['data'] as $key => $value) {
                $relationModel->$key = $value;
            }
            $relationModel->save();
        }

        return true;
    }
}
