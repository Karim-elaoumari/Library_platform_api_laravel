<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class BookController extends Controller
{
  

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('JwtAuth', ['only' => ['update','store','destroy','ownBooks']]);
        $this->middleware('permission:add_book', ['only' => ['store']]);
        $this->middleware('permission:show_own_books', ['only' =>  ['ownBooks']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    //    $this->middleware('permission:edit_role_of_user');
       $books =  Book::with('category','user','status')->latest()->get();
       return  new BookCollection($books);
    }
    public function ownBooks(){
        $user = JWTAuth::user();
        $books =  Book::with('category','user','status')->where('user_id',$user->id)->latest()->get();
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
        $user = JWTAuth::user();
        $image = $request->file('image');
        $image_name = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $image_name);
        $book = Book::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'download_link'=>$request->download_link,
            'content'=>$request->content,
            'image'=>$image_name,
            'category_id'=>$request->category_id,
            'status_id'=>$request->status_id,
            'location'=>$request->location,
            'user_id'=>$user->id,  
            
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
        $book =  Book::with('category','user','status')->findOrFail($book->id);

        return  new BookResource($book);
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
        $user = JWTAuth::user();
        $hisOwn =false;
        if($book->user_id==$user->id && $user->hasPermissionTo('edit_own_book')) $hisOwn = true;
        if(!$hisOwn && !$user->hasPermissionTo('edit_all_books')){
            return  response()->json(["error"=>'You Dont have permission to edit this book'], 404);
        }
        if ($request->hasFile('image')){
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
        $book->status_id = $request->status_id;
        $book->location = $request->location;
       
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
        $user = JWTAuth::user();
        $hisOwn =false;
        if($book->user_id==$user->id && $user->hasPermissionTo('delete_own_book')) $hisOwn = true;
        if(!$hisOwn && !$user->hasPermissionTo('delete_book')){
            return  response()->json(["error"=>'You Dont have permission to delete this book'], 404);
        }
        $book = Book::find($book->id)->where('user_id',$user->id);
        $book->delete();
        return  response()->json(['success'=>'book deleted successufuly']);
    }
}