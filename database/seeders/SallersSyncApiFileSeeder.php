<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SallersSyncApiFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('label_import_catalog_service')
            ->insert([
                ['title' => 'Бринекс', 'name' => 'ImportXmlBrinex'],
                ['title' => 'СВР Авто', 'name' => 'ImportXmlSvrauto'],
                ['title' => 'Точка Маркет', 'name' => 'ImportXmlTochkamarket'],
                ['title' => 'Четыре точки', 'name' => 'ImportXmlForTochki'],
                ['title' => 'Шининвест', 'name' => 'ImportXmlShininvest'],
                ['title' => 'Шинсервис', 'name' => 'ImportXmlShinservice']
            ]);
    }
}
