<?php

namespace App\Http\Controllers\Api\Backend\Pages;

use App\Helpers\DbHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Pages\PageCategoriesRepository;
use App\Repositories\Pages\PagesRepository;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = (new PagesRepository())->getAllForIndex('id', 'name');
        return ['pages' => $pages];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dbHelper = new DbHelper();
        $model = $dbHelper->setModel('Page')
            ->addRelation('seo', [
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'label' => $request->input('label'),
            ])
            ->save([
                'name' => $request->input('name'),
                'category_id' => $request->input('categoryId'),
                'content' => $request->input('content'),
            ]);
        return ['pageId' => $model->id];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = (new PagesRepository())->getEdit($id);
        $categories = (new PageCategoriesRepository)->getAllForIndex('id', 'name');

        return [ 'page' => $page, 'categories' => $categories];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dbHelper = new DbHelper();
        $model = (new PagesRepository())->getModel($id);

        $model = $dbHelper->setModel($model)
            ->addRelation('seo', [
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'label' => $request->input('label'),
            ])
            ->update([
                'name' => $request->input('name'),
                'category_id' => $request->input('categoryId'),
                'content' => $request->input('content'),
            ]);

        return ['id' => $id, 'model' => $model];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dbHelper = new DbHelper();
        $result = $dbHelper
            ->setModel('Page')
            ->addRelation('seo')
            ->delete($id);

        return ['delete' => $result];
    }
}
