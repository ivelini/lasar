<?php


namespace App\Services\ImportCatalogService\Sallers;

use App\Services\ImportCatalogService\ImportXml;


class ImportXmlForTochki extends ImportXml
{
    public function __construct($path)
    {
        parent::__construct($path);
    }

    function getItemsFromXml($xml) {
        $items = collect();
        foreach ($xml as $item) {
            if ($item->getName() == 'tires') {

                $item = collect($item)->map(function ($volume) {
                    return $volume->__toString();
                });

                $storages = [];
                $keys = $item->keys();

                foreach ($keys as $key) {
                    if (stripos($key, 'rest_') !== false) $storages[] = mb_substr($key, 5);
                }

                if ($item->count() > 4) {
                    foreach ($storages as $storage) {
                        $itemOneStorage = $item->filter(function ($value, $key) use ($storage) {
                            if ($key == 'cae' || str_contains($key, $storage)) return true;
                        });

                        $items->push([
                            'title' => null,
                            'num' => $itemOneStorage->get('cae'),
                            'code' => null,
                            'count' => mb_substr($itemOneStorage->get('rest_' . $storage), -2),
                            'price' => $itemOneStorage->get('price_' . $storage . '_rozn'),
                            'storage' => 'rest_' . $storage,
                            'label' => $this->label
                        ]);
                    }
                }
                else {
                    $items->push([
                        'title' => null,
                        'num' => $item->get('cae'),
                        'code' => null,
                        'count' => mb_substr($item->get('rest_' . $storages[0]), -2),
                        'price' => $item->get('price_' . $storages[0] . '_rozn'),
                        'storage' => 'rest_' . $storages[0],
                        'label' => $this->label
                    ]);
                }
            }
        }

        return $items;
    }
}
