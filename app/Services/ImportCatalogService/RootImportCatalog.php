<?php


namespace App\Services\ImportCatalogService;
/*
 * Реализация шаблона проектироания "Шаблонный метод"
 * Этапы от получения файла до выгрузи остатков
 */

use App\Helpers\DbHelper;
use App\Repositories\Catalog\ModelPositionRepository;
use App\Repositories\Catalog\SallerCodeRepository;
use App\Repositories\Catalog\SallersRepository;
use App\Repositories\Catalog\SeasonRepository;
use App\Repositories\Catalog\StorageRepository;
use App\Repositories\Catalog\TiresRepository;
use App\Repositories\Catalog\UpdateCatalogRepository;
use App\Repositories\Catalog\VendorCodeRepository;
use App\Repositories\Catalog\VendorRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

abstract class RootImportCatalog
{
    //Сопостовалние ключей Колонка:Обозначение в файле
    protected $selectedKeysHeader;
    //Путь до файла. Задается в доченем классе в конструкторе
    protected $path;
    //Количество строк в файле
    protected int $rowAll = 0;
    protected int $iStart = 0;

    protected $apiUrlSallerId;
    //Метка в таблице prices с названием класса чтоб знать, какие данные удалить из таблицы при обновлении
    protected $label;
    protected $skladName = null;

    private $vendorRepository;
    private $modelPositionRepository;
    private $seasonRepository;
    private $tiresRepository;
    private $sallersRepository;
    private $storageRepository;

    public function __construct($path)
    {
        $this->path = $path;
        $this->vendorRepository = new VendorRepository();
        $this->modelPositionRepository = new ModelPositionRepository();
        $this->seasonRepository = new SeasonRepository();
        $this->tiresRepository = new TiresRepository();
        $this->sallersRepository = new SallersRepository();
        $this->storageRepository = new StorageRepository();
    }

    /*
     * Метод получения строки из файла вида collect([targetKey => volume])
     * i - номер строки
     * $targetKeys - целевые ключи вида [key1, key2]
     * $renameKeys - переименовать ключи после взятия [valueId -> name]
     * $notNulls - если данный ключей нет, то строку не брать
     */
    abstract protected function getRow($i, $targetKeys, $renameKeys = [], $notNulls = []);

    abstract protected function importSallersTable(... $targetKeys);

    abstract protected function getRowForCode($i, $targetKeys);

    //Удалить все позиции из таблицы price с label = имя класса
    abstract protected function deletePriceItemsByLabel();

    /*
     * Удаление указанных таблиц перед полной выгрузкой
     *
     * @param array $tables
     */
    protected function clearCatalogDb($tables) {
        foreach($tables as $table) {
            DB::table($table)->delete();

            if($table == 'sallers') {
                DB::table($table)->insert(['name' => 'Global']);
            }
        }
    }

    /*
     * Получить сопостовалние ключей Колонка:Обозначение в файле
     */
    public function setSelectedKeysHeader($selectedKeysHeader)
    {
        $this->selectedKeysHeader = $selectedKeysHeader;
    }

    function import() {
        $this->clearCatalogDb(['vendors','seasons','images','tires', 'sallers', 'vendor_codes', 'saller_codes']);
        $this->importVendorsTable('vendor');
        $this->importModelPositionsTable('vendor', 'model');
        $this->importSeasonsTable('season');
        $this->importTiresTable('model', 'season', 'num', 'width', 'height', 'diameter', 'is_spikes', 'index_speed', 'index_load');
//        $this->importImagesTable('num', 'path');
        $this->importSallersTable('vendor_code', 'saller_code');
        $this->importCodesTables('num', 'vendor_code');
        $this->importCodesTables('num', 'saller_code');
        $this->importCodesTables('num', 'vendor_code_global');

        $updateCatalogRepository = new UpdateCatalogRepository();
        $updateCatalogModel = $updateCatalogRepository->getLastModel('tires');

        $dbHelper = new DbHelper();
        $dbHelper->setModel($updateCatalogModel)
            ->update(['is_catalog_updated' => true]);

        return true;
    }

    public function importPrice()
    {
        $this->importStoragesTable('storage');
        $this->importPricesTable('title', 'num', 'code', 'storage', 'price', 'count', 'label');

        return true;
    }

    private function importVendorsTable(...$targetKeys): bool
    {
        $cellValues = collect();
        for ($i = $this->iStart; $i <= $this->rowAll; $i++) {

            $row = $this->getRow($i, $targetKeys, ['vendor' => 'name'], ['vendor']);

            if (!empty($row)) {

                $cellValues->push($row->all());
            }
        }

        $dataAll = $cellValues->unique('name')->values()->all();

        $dbHelperVendorTable = new DbHelper('Vendor');
        $dbHelperVendorTable->saveAll($dataAll);

        return true;
    }

    private function importModelPositionsTable(...$targetKeys): bool
    {
        $vendorsIdAndName = $this->vendorRepository->getIdAndName();

        $cellValues = collect();
        for ($i = $this->iStart; $i <= $this->rowAll; $i++) {

            $row = $this->getRow($i, $targetKeys, ['model' => 'name', 'vendor' => 'vendor_id'], ['model']);

            if (!empty($row)) {

                $vendorId = $vendorsIdAndName->where('name', $row['vendor_id'])->first();
                $row['vendor_id'] = $vendorId['id'];

                $cellValues->push($row->all());
            }
        }

        $dataAll = $cellValues->unique('name')->values()->all();

        $dbHelperModelPositionTable = new DbHelper('ModelPosition');

        $dbHelperModelPositionTable->saveAll($dataAll);

        return true;
    }

    private function importSeasonsTable(...$targetKeys): bool
    {
        $cellValues = collect();
        for ($i = $this->iStart; $i <= $this->rowAll; $i++) {

            $row = $this->getRow($i, $targetKeys, ['season' => 'name'], ['season']);

            if (!empty($row)) {
                $cellValues->push($row->all());
            }
        }

        $dataAll = $cellValues->unique('name')->values()->all();

        $dbHelperSeasonTable = new DbHelper('Season');

        $dbHelperSeasonTable->saveAll($dataAll);

        return true;
    }

    private function importTiresTable(...$targetKeys): bool
    {
        $modelPositionIdAndName = $this->modelPositionRepository->getIdAndName();
        $seasonIdAndName = $this->seasonRepository->getIdAndName();

        $cellValues = collect();
        for ($i = $this->iStart; $i <= $this->rowAll; $i++) {

            $row = $this->getRow($i, $targetKeys,
                ['model' => 'model_id', 'season' => 'season_id'],
                ['model','season','num','width','height','diameter']);

            if (!empty($row)) {
                $modelPositionIdName = $modelPositionIdAndName->where('name', $row['model_id'])->first();
                $seasonIdName = $seasonIdAndName->where('name', $row['season_id'])->first();

                $row['model_id'] = $modelPositionIdName['id'];
                $row['season_id'] = $seasonIdName['id'];
                $row['is_spikes'] = (empty($row['is_spikes'])) ? 0 : 1;

                $cellValues->push($row->all());
            }
        }

        $dataAll = $cellValues->all();
        $dbHelperTiresTable = new DbHelper('Tire');
        $dbHelperTiresTable->saveAll($dataAll);

        return true;
    }

    private function importImagesTable(...$targetKeys)
    {
        $cellValues = collect();
        for ($i = $this->iStart; $i <= $this->rowAll; $i++) {

            $row = $this->getRow($i, $targetKeys, [], ['num', 'path']);

            if (!empty($row)) {

                $tireId = $this->tiresRepository->getIdFromNum($row['num']);
                if(!empty($tireId)) {

                    $row['imageable_id'] = $tireId;
                    $row['imageable_type'] = $this->tiresRepository::RELATION_TYPE;

                    $row = $row->filter(function ($item, $key) {
                        return $key != 'num';
                    });

                    $cellValues->push($row->all());
                }
            }
        }

        $dataAll = $cellValues->all();
        $dbHelperTiresTable = new DbHelper('Image');
        $dbHelperTiresTable->saveAll($dataAll);

        $modelsDataAll = $this->modelPositionRepository->getModelsIdAndPathForImportImageTable();
        $modelsDataAll = $modelsDataAll->all();
        $dbHelperTiresTable->saveAll($modelsDataAll);
    }

    private function importCodesTables(... $targetKeys)
    {
        $cellValues = collect();
        $targetKeyName = '';

        for ($i = $this->iStart; $i <= $this->rowAll; $i++) {

            if (in_array('vendor_code_global', $targetKeys)) {
                $row = $this->getRow($i, $targetKeys, ['vendor_code_global' => 'code'], ['vendor_code_global']);
                if (!empty($row)) {
                    $row = $row->all();
                    $tireId = $this->tiresRepository->getIdFromNum($row['num']);
                    if(!empty($tireId)) {
                        $data['saller_id'] = $this->sallersRepository->getIdExccelColumnName(00);
                        $data['tire_id'] = $tireId;
                        $data['code'] = $row['code'];
                        $cellValues->push($data);
                    }
                    $targetKeyName = 'vendor_code';
                }
            } else {
                $row =$this->getRowForCode($i, $targetKeys);
                $tireId = $this->tiresRepository->getIdFromNum($row['num']);
                if(!empty($tireId)) {
                    foreach ($row as $key => $excelKeyColValue) {
                        $targetKeyName = $key;
                        if (gettype($excelKeyColValue) == 'array') {
                            foreach ($excelKeyColValue as $excelKey => $value) {
                                if (!empty($value)) {
                                    $data['saller_id'] = $this->sallersRepository->getIdExccelColumnName($excelKey);
                                    $data['tire_id'] = $tireId;
                                    $data['code'] = $value;
                                    $cellValues->push($data);
                                }
                            }
                        }
                    }
                }
            }
        }

        if(!empty($targetKeyName)) {

            $dataAll = $cellValues->all();

            $dbHelperVendorTable= match ($targetKeyName) {
                'vendor_code' => new DbHelper('VendorCode'),
                'saller_code' => new DbHelper('SallerCode'),
            };

            $dbHelperVendorTable->saveAll($dataAll);

            return true;
        } else {
            return false;
        }
    }

    private function importStoragesTable(...$targetKeys)
    {
        $cellValues = collect();
        for ($i = $this->iStart; $i <= $this->rowAll; $i++) {

            $row = $this->getRow($i, $targetKeys, ['storage' => 'name'], ['storage']);

            if (!empty($row)) {

                $cellValues->push($row->all());
            }
        }

        $dataAll = $cellValues->unique('name')->values();

        $dbHelperVendorTable = new DbHelper('Storage');

        $dataAll->each(function ($item) use($dbHelperVendorTable) {
            $itemModel = $this->storageRepository->findModelByAttributes($item);
            $item['api_url_saller_id'] = $this->apiUrlSallerId;
            if(empty($itemModel)) $dbHelperVendorTable->save($item);
        });


        return true;
    }

    private function importPricesTable(... $targetKeys)
    {
        $this->deletePriceItemsByLabel();
        $vendorCodeRepository = new VendorCodeRepository();
        $sallerCodeRepository = new SallerCodeRepository();
        $cellValues = collect();
        $tiresNotFound = collect();

        for ($i = $this->iStart; $i <= $this->rowAll; $i++) {

            $row = $this->getRow($i, $targetKeys, ['storage' => 'storage_id'], ['num', 'storage', 'price']);

            if (!empty($row)) {

                $storageId = $this->storageRepository->getAttributeFromAttributeValue('name', $row['storage_id'], 'id');

                $vendorCodeId = $vendorCodeRepository->getId('code', $row->get('code'));

                $codeId = null;
                if(!empty($vendorCodeId)) {
                    $codeId = $vendorCodeId;
                    $codeRelationType = $vendorCodeRepository::RELATION_TYPE;
                } else {
                    $codeId = $sallerCodeRepository->getId('code', $row->get('num'));
                    $codeRelationType = $sallerCodeRepository::RELATION_TYPE;
                }

                if(!empty($storageId) && !empty($codeId)) {
                    $data['priceable_id'] = $codeId;
                    $data['priceable_type'] = $codeRelationType;
                    $data['storage_id'] = $storageId;
                    $data['count'] = $row->get('count');
                    $data['price'] = $row->get('price');
                    $data['label'] = $row->get('label');

                    $cellValues->push($data);
                } else {
                    if ($tiresNotFound->count() == 0) $tiresNotFound->push($targetKeys);

                    $values = [];

                    $row->each(function ($item) use (&$values) {
                        $values[] =  $item;
                    });

                    $tiresNotFound->push($values);
                }
            }
        }

        $fileCsv = fopen(Storage::disk('local')->path('tmp\\' . $this->label . '' . $this->skladName . '.csv'), 'wb');

        foreach ($tiresNotFound as $row) {
            fputcsv($fileCsv, $row);
        }

        fclose($fileCsv);

        $dataAll = $cellValues->all();

        $dbHelperVendorTable = new DbHelper('Price');
        $dbHelperVendorTable->saveAll($dataAll);

        return true;
    }

    protected function renameKeysAndCheckNotNull($row, $renameKeys = [], $notNulls = [])
    {
        $isRowNotContainNullKey = true;
        if (count($notNulls) > 0) {
            foreach ($notNulls as $notNullKey) {
                if (empty($row->get($notNullKey))) {
                    $isRowNotContainNullKey = false;
                }
            }
        }

        if ($isRowNotContainNullKey) {
            if(count($renameKeys) > 0) {
                $row = $row->mapWithKeys(function ($rowValue, $rowKey) use ($renameKeys) {
                    foreach ($renameKeys as $key => $value) {
                        if ($rowKey == $key) {
                            return [ $value => $rowValue ];
                        }
                    }
                    return [ $rowKey => $rowValue ];
                });
            }

            return $row;
        } else {
            return null;
        }
    }
}
