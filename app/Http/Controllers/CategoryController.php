<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('JwtAuth', ['only' => ['update','store','destroy']]);
        $this->middleware('permission:add_category', ['only' => ['store']]);
        $this->middleware('permission:edit_category', ['only' => ['update']]);
        $this->middleware('permission:delete_category', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    //    $this->middleware('permission:edit_role_of_user');
       $categories =  Category::with('books')->latest()->get();
       return  new CategoryCollection($categories);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBookRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
       
        $category = Category::create([
            'name'=>$request->name, 
        ]);
        return new CategoryResource($category);
    }
    /**
     * Display the specified resource.
     
     * @param  \App\Models\Category  
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category =  Category::with('books')->where('id',$category->id)->get();
        return  new CategoryResource($category);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        ///
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        
        $category->name = $request->name;
        $category->update();
        return new CategoryResource($category); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {  
        $category = Category::find($category->id)->get();
        $category->delete();
        return  response()->json(['success'=>'Category deleted successufuly']);
    }
}
