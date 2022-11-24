<?php

namespace App\Jobs;

use App\Helpers\DbHelper;
use App\Repositories\Catalog\UpdateCatalogRepository;
use App\Services\ImportCatalogService\ImportXlsxFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportCatalogPriceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;
    protected $selectedKeysHeader;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path, $selectedKeysHeader)
    {
        $this->path =$path;
        $this->selectedKeysHeader = $selectedKeysHeader;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $importXlsx = new ImportXlsxFile($this->path);
        $importXlsx->setSelectedKeysHeader($this->selectedKeysHeader);

        try {
            $importXlsx->importPrice();
        }catch (\Exception $e) {
            $updateCatalogRepository = new UpdateCatalogRepository();
            $updateCatalogModel = $updateCatalogRepository->getLastModel('tires_price');
            $dbHelperUpdateCatalog = new DbHelper('UpdateCatalog');
            $dbHelperUpdateCatalog->update($updateCatalogModel, ['error' => $e]);
        }
    }
}
