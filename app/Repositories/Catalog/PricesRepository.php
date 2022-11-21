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

    public function getPaginationByFilterData($data, $paginate = 20)
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

        $paginator = $this->startConditions();

        if (array_key_exists('price.min', $data)) $paginator = $paginator->where('price', '>=', $data['price.min']);
        if (array_key_exists('price.max', $data)) $paginator = $paginator->where('price', '<=', $data['price.max']);

        $paginator = $paginator->whereHasMorph(
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
            ->paginate($paginate);


        $tires = collect($paginator->items())->map(function ($price) {
            $data['id'] = $price->priceable->tire->id;

            $data['title'] = 'Шина ' .
                $price->priceable->tire->modelPosition->name . ' ' .
                $price->priceable->tire->width . '/' .
                $price->priceable->tire->height . ' R' .
                $price->priceable->tire->diameter;
                if(!empty($price->priceable->tire->index_speed)) $data['title'] .= ' ' . $price->priceable->tire->index_speed;

            $data['num'] = $price->priceable->tire->num;
            $data['price'] = number_format(rtrim(rtrim($price->price, '0'), '.'), 0, '', ' ');
            $data['storage'] = $price->storage->name;
            $data['storage_vol'] = $price->storage->volume;
            $data['count'] = $price->count;
            $data['vendor'] = $price->priceable->tire->modelPosition->vendor->name;
            $data['model'] = $price->priceable->tire->modelPosition->name;
            $data['isSpikes'] = $price->priceable->tire->is_spikes;

            return $data;
        });

        $paginatePage['paginator']['title'] = '';

        if (array_key_exists('vendorId', $data)) {
            $paginatePage['paginator']['title'] .= 'Шины ' . (new VendorRepository)->getAttributeById('name', $data['vendorId']);
        }

        if (array_key_exists('width', $data)) {
            $paginatePage['paginator']['title'] .= ' ' . $data['width'];
        }

        if (array_key_exists('height', $data)) {
            $paginatePage['paginator']['title'] .= (array_key_exists('width', $data)) ? '/' . $data['height'] : ' ' . $data['height'];
        }

        $paginatePage['items'] = $tires->all();
        $paginatePage['paginator']['curent_page'] = $paginator->currentPage();
        $paginatePage['paginator']['last_page'] = $paginator->lastPage();
        $paginatePage['paginator']['total_items'] = $paginator->total();

        return $paginatePage;
    }

    public function getMinAndMaxPrice()
    {
        $models = $this->startConditions()->pluck('price');

        return [
            'min' => number_format(rtrim(rtrim($models->min(), '0'), '.'), 0, '', ' '),
            'max' => number_format(rtrim(rtrim($models->max(), '0'), '.'), 0, '', ' ')
        ];
    }
}
