<?php

namespace App\Http\Controllers\Api\Backend\Catalog;

use App\Helpers\DbHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\SallersSyncApiFileResource;
use App\Jobs\ImportCatalogPriceJob;
use App\Repositories\Catalog\ApiUrlSallersRepository;
use App\Repositories\Catalog\LabelImportServiceRepository;
use App\Repositories\Catalog\SallersRepository;
use App\Repositories\Catalog\SallersSyncApiFileRepository;
use App\Repositories\Catalog\SettingsCatalogepository;
use App\Repositories\Catalog\UpdateCatalogRepository;
use App\Services\ImportCatalogService\ImportXlsxFile;
use App\Services\ImportCatalogService\Sallers\ImportXmlForTochki;
use App\Services\ImportCatalogService\Sallers\ImportXmlShininvest;
use App\Services\ImportCatalogService\Sallers\ImportXmlShinservice;
use App\Services\ImportCatalogService\Sallers\ImportXmlSvrauto;
use App\Services\ImportCatalogService\Sallers\ImportXmlTochkamarket;
use App\Services\ImportCatalogService\Sallers\ImportXmlBrinex;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ImportCatalogJob;


class ImportDataController extends Controller
{
    public function uploadFile(Request $request): array
    {
        $dbHelperUpdateCatalog = new DbHelper('UpdateCatalog');

        $fileNAme = 'UserName_' . rand(1000, 10000) . '.xlsx';
        $path = Storage::putFileAs('tmp', $request->file('fileData'), $fileNAme);

        $importXlsx = new ImportXlsxFile($path);
        $keysHeader = $importXlsx->getKyesHeaderFromDataFile();

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

    public function test(Request $request)
    {
        $apiUrlSallersRepository = new ApiUrlSallersRepository();
        $urls = $apiUrlSallersRepository->getAllForTableUpdatePrice();
        dd($urls);
//        $selectedKeysHeader =collect(json_decode($request->input('filter')));

//        (new ImportXmlBrinex('tmp\xml\brinex.xml'))->importPrice();
//        (new ImportXmlShinservice('tmp\xml\shinservice-b2b-27.xml'))->importPrice();
//        (new ImportXmlForTochki('tmp\xml\4tochki.xml'))->importPrice();
//        (new ImportXmlShininvest('tmp\xml\shininvest.xml'))->importPrice();
//        (new ImportXmlSvrauto('tmp\xml\svrauto.xml'))->importPrice();
//        (new ImportXmlTochkamarket('tmp\xml\tochkamarket-chel.xml', 'Челябинск'))->importPrice();
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
