<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProductCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProductCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings('product', 'products');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('article');
        CRUD::column('category_id');
        CRUD::column('image_url');
        CRUD::column('created_at');
        CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProductRequest::class);

        CRUD::field('name');
        CRUD::field('article');
        CRUD::addField([
            'label'=>'Category',
            'type'=>'select',
            'name'=>'category_id',
            'attribute'=>'name',
            'model'=>Category::class,
            'options'=>(function(Builder $builder) {
                return $builder->get();
            })
        ]);
        CRUD::addField([
            'name' => 'image_url',
            'type' => 'upload',
            'upload' => true,
        ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $product = Product::create([
            'name' => $this->crud->getRequest()->request->get('name'),
            'article' => $this->crud->getRequest()->request->get('article'),
            'category_id' => $this->crud->getRequest()->request->get('category_id')
        ]);
        $file = $this->crud->getRequest()->file();

        if($file) {
            $product->image_url =  env('APP_URL').'/storage/'.$file['image_url']->store('uploads', 'public');
            $product->save();
        }

        return redirect('admin/product');
    }

    public function update()
    {
        $product = Product::find($this->crud->getRequest()->request->get('id'));
        $product->name = $this->crud->getRequest()->request->get('name');
        $product->article = $this->crud->getRequest()->request->get('article');
        $product->category_id = $this->crud->getRequest()->request->get('category_id');
        $file = $this->crud->getRequest()->file();

        if($file) {
            $product->image_url = env('APP_URL').'/storage/'.$file['image_url']->store('uploads', 'public');
        }
        $product->save();
        return redirect('admin/product');
    }
}
