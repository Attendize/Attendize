<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CategoryRequest as StoreRequest;
use App\Http\Requests\CategoryRequest as UpdateRequest;

/**
 * Class CategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CategoryCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Category');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/category');
        $this->crud->setEntityNameStrings('category', 'categories');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
//        $this->crud->setFromDb();
        $this->crud->addColumns([
            ['name'=>'id','type'=>'text','label'=>'Id'],
            ['name'=>'title_tm','type'=>'text','label'=>'Title tm'],
            ['name'=>'title_ru','type'=>'text','label'=>'Title ru'],
            ['name'=>'view_type','type'=>'text','label'=>'Type'],
            ['name'=>'parent_id','type'=>'text','label'=>'Parent'],
        ]);
        $this->crud->addFields([
            ['name'=>'title_tm','type'=>'text','label'=>'Title tm'],
            ['name'=>'title_ru','type'=>'text','label'=>'Title ru'],
            ['name'=>'view_type','type' =>'enum', 'label'=>'Type']
        ]);
        $this->crud->enableReorder('title_tm', 2);
        $this->crud->allowAccess('reorder');
        // add asterisk for fields that are required in CategoryRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here

        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
//        dd($redirect_location);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
