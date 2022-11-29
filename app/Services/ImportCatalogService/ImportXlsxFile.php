<?php


namespace App\Services\ImportCatalogService;


use App\Helpers\DbHelper;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportXlsxFile extends RootImportCatalog
{

    private $spreadsheet;
    private $sheet;

    public function __construct($path)
    {
        parent::__construct($path);
        $this->spreadsheet = IOFactory::load(Storage::disk('local')->path($path));
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->iStart = 2;
        $this->rowAll = $this->sheet->getHighestRow();
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

    protected function getRow($i, $targetKeys, $renameKeys = [], $notNulls = []) {
        $row = $this->selectedKeysHeader->map(function ($excelColName, $colName) use ($i, $targetKeys) {
            foreach ($targetKeys as $key) {
                if ($key == $colName) {
                    return $this->sheet->getCell($excelColName.$i)->getValue();
                }
            }
        });

        $row = $row->only($targetKeys);

        $row = $this->renameKeysAndCheckNotNull($row, $renameKeys, $notNulls);

        return $row;
    }

    protected function importSallersTable(... $targetKeys)
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

    protected function getRowForCode($i, $targetKeys)
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

    protected function deletePriceItemsByLabel()
    {
        return true;
    }
}
