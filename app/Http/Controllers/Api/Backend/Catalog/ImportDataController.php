<?php

namespace App\Http\Controllers\Api\Backend\Catalog;

use App\Helpers\DbHelper;
use App\Helpers\UploadFileHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ImportCatalogPriceJob;
use App\Repositories\Catalog\PricesRepository;
use App\Repositories\Catalog\SettingsCatalogepository;
use App\Repositories\Catalog\StorageRepository;
use App\Repositories\Catalog\UpdateCatalogRepository;
use App\Services\ImportCatalogService\ImportXlsxFile;
use Illuminate\Http\Request;
use App\Jobs\ImportCatalogJob;


class ImportDataController extends Controller
{
    public function uploadFile(Request $request): array
    {
        $dbHelper = new DbHelper();

        $modelUpdateCatalog = $dbHelper
            ->setModel('UpdateCatalog')
            ->save(['type' => $request->input('type')]);

        $uploadFileHelper = new UploadFileHelper();

        $path = $uploadFileHelper
            ->setRelationModel($modelUpdateCatalog)
            ->setRequest('fileData', $request)
            ->setPath('tmp')
            ->upload();

        $importXlsx = new ImportXlsxFile($path);
        $keysHeader = $importXlsx->getKyesHeaderFromDataFile();

        return ['is_upload_data_file' => true, 'keysHeader' => $keysHeader];
    }

    public function importTire(Request $request): array
    {
        $settings = SettingsCatalogepository::getModelSettingsCatalog('import', 'tires');

        if(!empty($settings)) {
            $selectedKeysHeader = collect(json_decode($settings->settings));
        }
        else {
            $selectedKeysHeader = collect(json_decode($request->query('selectedKeys')));
        }

        $this->createJob('tires', $selectedKeysHeader);

        return ['is_job_update_created' => true];
    }

    public function importPrice(Request $request): array
    {
        $selectedKeysHeader =collect(json_decode($request->input('selectedKeys')));
        $this->createJob('tires_price', $selectedKeysHeader);

        return ['is_job_update_created' => true];
    }

    public function getUploadStatus(Request $request): array
    {
        $type = $request->query('type');
        $updateCatalogRepository = new UpdateCatalogRepository();
        $uploadStatus =  $updateCatalogRepository->getUploadStatus($type);

        return ['upload_status' => $uploadStatus];
    }

    private function createJob($typeUpdateCatalog, $selectedKeysHeader, )
    {
        $updateCatalogRepository = new UpdateCatalogRepository();
        $dbHelper = new DbHelper();

        $path = $updateCatalogRepository->getPathLastModel($typeUpdateCatalog);

        $job = match ($typeUpdateCatalog) {
            'tires' => new ImportCatalogJob($path, $selectedKeysHeader),
            'tires_price' => new ImportCatalogPriceJob($path, $selectedKeysHeader),
        };

        $this->dispatch($job);

        $updateCatalogModel = $updateCatalogRepository->getLastModel($typeUpdateCatalog);
        $dbHelper->setModel($updateCatalogModel)
            ->update(['is_job_created' => true]);

        return true;
    }

}
