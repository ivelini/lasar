<?php


namespace App\Services\ImportCatalogService\Sallers;

use App\Services\ImportCatalogService\ImportXml;


class ImportXmlBrinex extends ImportXml
{
    public function __construct($path, $skladName, $apiUrlSallerId)
    {
        $this->skladName = $skladName;
        $this->apiUrlSallerId = $apiUrlSallerId;
        parent::__construct($path);
    }

    function getItemsFromXml($xml) {
        $items = collect();
        foreach ($xml as $itemXml) {
            if ($itemXml->getName() == 'shina') {
                foreach ($itemXml as $item) {
                    $item = collect($item);

                    $items->push([
                        'title' => $item->get('name')->__toString(),
                        'num' => $item->get('product_id')->__toString(),
                        'code' => $item->get('vendor_code')->__toString(),
                        'count' => $item->get('stockQuantity')->__toString(),
                        'price' => $item->get('price')->__toString(),
                        'storage' => !empty($this->skladName) ? $this->skladName : $item->get('stockName')->__toString(),
                        'label' => $this->label
                    ]);
                }
            }
        }

        return $items;
    }
}
