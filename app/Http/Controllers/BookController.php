<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{
  

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store','update','destroy']);
        $this->middleware('IsAuthor')->only(['store']);
        $this->middleware('isAdminAuthor')->only(['update','destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $books =  Book::with('category','user')->latest()->get();
       return  new BookCollection($books);
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
    public function store(StoreBookRequest $request)
    {
        $image = $request->file('image');
        $image_name = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $image_name);
        $book = Book::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'downloand_link'=>$request->downloand_link,
            'content'=>$request->content,
            'image'=>$image_name,
            'category_id'=>$request->category_id,
            'user_id'=>1,  
            // Auth::user()->id
        ]);
        return new BookResource($book);
    }
    /**
     * Display the specified resource.
     
     * @param  \App\Models\Book  
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        $book =  Book::with('category','user')->where('id',$book->id)->get();
        return  new BookCollection($book);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        ///
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBookRequest  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        // if($book->user_id!=1 && Auth::user()->role->name!="admin"){
        //     return  response()->json(["error"=>'You Dont have permission to make action on it'], 404);
        // }
        if ($request->hasFile('image')) {
            // delete old image
            $oldImage = public_path('images/').$book->image;
            if (file_exists($oldImage)) {
                unlink($oldImage);
            }
            // upload new image
            $image = $request->file('image');
            $imageName = time().'-'.$image->getClientOriginalName();
            $image->move(public_path('images/'), $imageName);
            $book->image = $imageName;
        }
        $book->title = $request->title;
        $book->description = $request->description;
        $book->download_link = $request->download_link;
        $book->content = $request->content;
        $book->category_id = $request->category_id;
        $book->update();
        return new BookResource($book); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {  
        // if($book->user_id!=1 && Auth::user()->role->name!="admin"){
        //     return  response()->json(["error"=>'You Dont have permission to make action on this book'], 404);
        // }
        $book = Book::find($book->id)->where('user_id',1);
        $book->delete();
        return  response()->json(['success'=>'book deleted successufuly']);
    }
}