<?php


namespace App\Repositories\Catalog;

use App\Models\Price as Model;
use App\Repositories\CoreRepository;

class PricesRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getByFilterData($data)
    {
//        $data = [
//            'price'     => ['min' => 0, 'max' => 3200],
//            'vendorId'  => 28,
//            'modelId'   => 727,
//            'seasonId'  => 1,
//            'width'     => 1,
//            'height'    => 1,
//            'diameter'  => 1,
//            'isSpikes'  => 1,
//        ];

        $prices = $this->startConditions();

        if (array_key_exists('price', $data)) {
            $prices = $prices->where('price', '>=', $data['price']['min']);
            $prices = $prices->where('price', '<=', $data['price']['max']);
        }

        $prices = $prices->whereHasMorph(
                'priceable',
                ['vendor_code', 'saller_code'],
                function ($query) use ($data) {
                    $query
                        ->wherehas(
                            'tire',
                            function ($query) use ($data)
                            {
                                if (array_key_exists('modelId', $data)) $query->where('model_id', $data['modelId']);
                                if (array_key_exists('seasonId', $data)) $query->where('season_id', $data['seasonId']);
                                if (array_key_exists('width', $data)) $query->where('width', $data['width']);
                                if (array_key_exists('height', $data)) $query->where('height', $data['height']);
                                if (array_key_exists('diameter', $data)) $query->where('diameter', $data['diameter']);
                                if (array_key_exists('isSpikes', $data)) $query->where('is_spikes', $data['isSpikes']);

                                $query->whereHas(
                                    'modelPosition',
                                    function ($query) use ($data)
                                    {
                                        if (array_key_exists('vendorId', $data)) $query->where('vendor_id', $data['vendorId']);
                                    }
                                );
                            });
                }
            )
            ->with('priceable.tire.modelPosition.vendor', 'priceable.saller', 'storage')
            ->get();


        $prices = $prices->map(function ($price) {
            $data['id'] = $price->priceable->tire->id;

            $data['title'] = 'Шина ' .
                $price->priceable->tire->modelPosition->name . ' ' .
                $price->priceable->tire->width . '/' .
                $price->priceable->tire->height . ' R' .
                $price->priceable->tire->diameter;
                if(!empty($price->priceable->tire->index_speed)) $data['title'] .= ' ' . $price->priceable->tire->index_speed;

            $data['num'] = $price->priceable->tire->num;
            $data['price'] = $price->price;
            $data['storage'] = $price->storage->name;
            $data['storage_vol'] = $price->storage->volume;
            $data['count'] = $price->count;
            $data['vendor'] = $price->priceable->tire->modelPosition->vendor->name;
            $data['model'] = $price->priceable->tire->modelPosition->name;

            return $data;
        });

        return $prices;
    }

    public function getMinAndMaxPrice()
    {
        $models = $this->startConditions()->pluck('price');

        return ['min' => $models->min(), 'max' => $models->max()];
    }
}
