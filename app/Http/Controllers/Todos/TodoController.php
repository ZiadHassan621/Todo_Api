<?php

namespace App\Http\Controllers\Todos;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $todos = $user->todos;
        

        if($todos->isEmpty()){
          return response()->json(['message' => 'No data avaliable'],200);
        }
    
        return response()->json($todos);
    }
    public function store(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'is_completed' => 'boolean',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages());
        }
       $todos = Todo::create([
        'title'=> $request->title,
        'content'=> $request->content,
        'is_completed' => $request->is_completed,
        'user_id' => auth()->id(),
       ]);
        return response()->json($todos);
    }
}
