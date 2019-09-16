<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    CRUD::resource('category', 'CategoryCrudController');
    CRUD::resource('country', 'CountryCrudController');
    CRUD::resource('event', 'EventCrudController');
    CRUD::resource('slider', 'SliderCrudController');
    CRUD::resource('tag', 'TagCrudController');
    CRUD::resource('subscriber', 'SubscriberCrudController');
    CRUD::resource('event_request', 'EventRequestCrudController');
}); // this should be the absolute last line of this file