<?php


namespace App\Services\ImportCatalogService\Sallers;

use App\Services\ImportCatalogService\ImportXml;


class ImportXmlShinservice extends ImportXml
{
    public function __construct($path)
    {
        parent::__construct($path);
    }

    function getItemsFromXml($xml) {
        $items = collect();
        $storages = collect();
        foreach ($xml as $itemXml) {

            if ($itemXml->getName() == 'shops') {

                foreach ($itemXml as $shop) {
                    $attributes = collect($shop->attributes());
                    $storages->push([
                        'id' => $attributes->get('id')->__toString(),
                        'title' => $attributes->get('title')->__toString(),
                    ]);
                }
            }

            if ($itemXml->getName() == 'tires') {
                foreach ($itemXml as $item) {
                    foreach ($item->shops as $shop) {
                        $shop = collect($shop->shop->attributes());
                        $storageName = $storages->firstWhere('id', $shop->get('id')->__toString())['title'];
                        $item = collect($item->attributes());

                        $items->push([
                            'title' => $item->get('title')->__toString(),
                            'num' => $item->get('id')->__toString(),
                            'code' => $item->get('sku')->__toString(),
                            'count' => $item->get('stock')->__toString(),
                            'price' => $item->get('retail_price')->__toString(),
                            'storage' => $storageName,
                            'label' => $this->label
                        ]);
                    }
                }
            }
        }

        return $items;
    }
}
