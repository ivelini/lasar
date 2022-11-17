<?php

namespace App\Http\Controllers\Api\Backend\Catalog;

use App\Helpers\DbHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ImportCatalogPriceJob;
use App\Repositories\Catalog\SettingsCatalogepository;
use App\Repositories\Catalog\UpdateCatalogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ImportDataCatalogService;
use App\Jobs\ImportCatalogJob;


class ImportDataController extends Controller
{
    public function uploadFile(Request $request): array
    {
        $dbHelperUpdateCatalog = new DbHelper('UpdateCatalog');

        $fileNAme = 'UserName_' . rand(1000, 10000) . '.xlsx';
        $path = Storage::putFileAs('tmp', $request->file('fileData'), $fileNAme);

        $importDataService = new ImportDataCatalogService($path);
        $keysHeader = $importDataService->getKyesHeaderFromDataFile();

        $dbHelperUpdateCatalog->save(['type' => $request->input('type'), 'upload_file_path' => $path]);

        return ['is_upload_data_file' => true, 'keysHeader' => $keysHeader];
    }

    public function importTire(Request $request): array
    {
        $settings = SettingsCatalogepository::getModel('import', 'tires');

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

    public function test(Request $request): array
    {
//        $settings = SettingsCatalogepository::getModel('update', 'tires_price');
//
//        if(!empty($settings)) {
//            $selectedKeysHeader = collect(json_decode($settings->settings));
//        }
//        else {
//            $selectedKeysHeader = collect(json_decode($request->query('selectedKeys')));
//        }

        $importDataService = new ImportDataCatalogService('tmp\UserName_4871.xlsx');
        $keysHeader = $importDataService->getKyesHeaderFromDataFile();
//        $importDataService->setSelectedKeysHeader($selectedKeysHeader);
//        $importDataService->importPrice();

        dd($keysHeader);
    }

    public function getUploadStatus(Request $request): array
    {
        $type = $request->input('type');
        $updateCatalogRepository = new UpdateCatalogRepository();
        $uploadStatus =  $updateCatalogRepository->getUploadStatus($type);

        return ['upload_status' => $uploadStatus];
    }

    private function createJob($typeUpdateCatalog, $selectedKeysHeader, )
    {
        $updateCatalogRepository = new UpdateCatalogRepository();
        $dbHelperUpdateCatalog = new DbHelper('UpdateCatalog');

        $path = $updateCatalogRepository->getPathLastModel($typeUpdateCatalog);

        $job = match ($typeUpdateCatalog) {
            'tires' => new ImportCatalogJob($path, $selectedKeysHeader),
            'tires_price' => new ImportCatalogPriceJob($path, $selectedKeysHeader),
        };

        $this->dispatch($job);

        $updateCatalogModel = $updateCatalogRepository->getLastModel($typeUpdateCatalog);
        $dbHelperUpdateCatalog->update($updateCatalogModel, ['is_job_created' => true]);

        return true;
    }

}
