<?php


namespace App\Services;


use App\Helpers\DbHelper;
use App\Repositories\Catalog\SallerCodeRepository;
use App\Repositories\Catalog\SallersRepository;
use App\Repositories\Catalog\TiresRepository;
use App\Repositories\Catalog\VendorCodeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Repositories\Catalog\VendorRepository;
use App\Repositories\Catalog\ModelPositionRepository;
use App\Repositories\Catalog\SeasonRepository;
use App\Repositories\Catalog\UpdateCatalogRepository;
use App\Repositories\Catalog\StorageRepository;

/*
 * Серис импорта каталога в базу данных
 */

class ImportDataCatalogService
{
    private \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet;
    private \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet;
    private int $rowAll = 0;
    private VendorRepository $vendorRepository;
    private ModelPositionRepository $modelPositionRepository;
    private SeasonRepository $seasonRepository;
    private $selectedKeysHeader;
    private TiresRepository $tiresRepository;
    private StorageRepository $storageRepository;
    private SallersRepository $sallersRepository;


    public function __construct($path)
    {
        $this->spreadsheet = IOFactory::load(Storage::disk('local')->path($path));
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->rowAll = $this->sheet->getHighestRow();
        $this->vendorRepository = new VendorRepository();
        $this->modelPositionRepository = new ModelPositionRepository();
        $this->seasonRepository = new SeasonRepository();
        $this->tiresRepository = new TiresRepository();
        $this->storageRepository = new StorageRepository();
        $this->sallersRepository = new SallersRepository();

    }

    public function setSelectedKeysHeader($selectedKeysHeader)
    {
        $this->selectedKeysHeader = $selectedKeysHeader;
    }

    public function getKyesHeaderFromDataFile(): array
    {
        $cells = $this->sheet->getCellCollection();
        $keysFromSheet = [];

        $col = 'A';
        $colMax = $cells->getHighestColumn();
        while ($col != $colMax) {
            if(!empty($cells->get($col . '1'))) {
                $keysFromSheet[mb_strtolower($cells->get($col . '1')->getValue())] = $col;
            }
            else {
                $keysFromSheet[$col] = $col;
            }
            $col++;
        }

        return $keysFromSheet;
    }


    public function clearCatalogDb($tables) {
        foreach($tables as $table) {
            DB::table($table)->delete();

            if($table == 'sallers') {
                DB::table($table)->insert(['name' => 'Global']);
            }
        }
    }

    public function import() {
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
        $dbHelperUpdateCatalog = new DbHelper('UpdateCatalog');
        $dbHelperUpdateCatalog->update($updateCatalogModel, ['is_catalog_updated' => true]);

        return true;
    }

    public function importPrice()
    {
        $this->clearCatalogDb(['storages','prices']);
        $this->importStoragesTable('storage');
        $this->importPricesTable('num', 'code', 'storage', 'price', 'count');

        $updateCatalogRepository = new UpdateCatalogRepository();
        $updateCatalogModel = $updateCatalogRepository->getLastModel('tires_price');
        $dbHelperUpdateCatalog = new DbHelper('UpdateCatalog');
        $dbHelperUpdateCatalog->update($updateCatalogModel, ['is_catalog_updated' => true]);

        return true;

    }

    private function getRow($i, $targetKeys, $renameKeys = [], $notNulls = []) {
            $row = $this->selectedKeysHeader->map(function ($excelColName, $colName) use ($i, $targetKeys) {
                foreach ($targetKeys as $key) {
                    if ($key == $colName) {
                        return $this->sheet->getCell($excelColName.$i)->getValue();
                    }
                }
            });

            $row = $row->only($targetKeys);

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

    private function getRowForCode($i, $targetKeys)
    {
        $row = $this->selectedKeysHeader->map(function ($excelColName, $colName) use ($i, $targetKeys) {
            foreach ($targetKeys as $key) {
                if ($key == $colName) {
                    if (gettype($excelColName) == 'array') {
                        $arr = [];
                        foreach ($excelColName as $elColName) {
                            $arr[$elColName] = $this->sheet->getCell($elColName.$i)->getValue();
                        }
                        return $arr;
                    } else {
                        return $this->sheet->getCell($excelColName.$i)->getValue();
                    }
                }
            }
        });

        $row = $row->only($targetKeys);

        return $row;
    }

    private function importVendorsTable(...$targetKeys): bool
    {
        $cellValues = collect();
        for ($i = 2; $i <= $this->rowAll; $i++) {

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
        for ($i = 2; $i <= $this->rowAll; $i++) {

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
        for ($i = 2; $i <= $this->rowAll; $i++) {

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
        for ($i = 2; $i <= $this->rowAll; $i++) {

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
        for ($i = 2; $i <= $this->rowAll; $i++) {

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

    private function importStoragesTable(...$targetKeys)
    {
        $cellValues = collect();
        for ($i = 2; $i <= $this->rowAll; $i++) {

            $row = $this->getRow($i, $targetKeys, ['storage' => 'name'], ['storage']);

            if (!empty($row)) {

                $cellValues->push($row->all());
            }
        }

        $dataAll = $cellValues->unique('name')->values()->all();

        $dbHelperVendorTable = new DbHelper('Storage');
        $dbHelperVendorTable->saveAll($dataAll);

        return true;
    }

    private function importSallersTable(... $targetKeys)
    {
        $cellValues = collect();

        foreach ($targetKeys as $targetKey) {
            foreach($this->selectedKeysHeader[$targetKey] as $selectKey) {
                $cellValues->push([
                    'name' => $this->sheet->getCell($selectKey. '1')->getValue(),
                    'key_excel_col' => $selectKey
                ]);
            }
        }

        $dataAll = $cellValues->all();

        $dbHelperVendorTable = new DbHelper('Saller');
        $dbHelperVendorTable->saveAll($dataAll);

        return true;
    }

    private function importCodesTables(... $targetKeys)
    {
        $cellValues = collect();
        $targetKeyName = '';

        for ($i = 2; $i <= $this->rowAll; $i++) {

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

        $dataAll = $cellValues->all();

        $dbHelperVendorTable= match ($targetKeyName) {
            'vendor_code' => new DbHelper('VendorCode'),
            'saller_code' => new DbHelper('SallerCode'),
        };

        $dbHelperVendorTable->saveAll($dataAll);

        return true;
    }

    private function importPricesTable(... $targetKeys)
    {
        $vendorCodeRepository = new VendorCodeRepository();
        $sallerCodeRepository = new SallerCodeRepository();
        $cellValues = collect();

        for ($i = 2; $i <= $this->rowAll; $i++) {

            $row = $this->getRow($i, $targetKeys, ['storage' => 'storage_id'], ['storage', 'code']);

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

                    $cellValues->push($data);
                }
            }
        }

        $dataAll = $cellValues->all();

        $dbHelperVendorTable = new DbHelper('Price');
        $dbHelperVendorTable->saveAll($dataAll);

        return true;
    }
}
