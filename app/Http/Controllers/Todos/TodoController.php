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

    public function update(Request $request, $id)
    {
       
        $validator = Validator::make($request->all(), [
            'title'=> 'required',
            'content'=> 'required',
            'is_completed'=> 'boolean',
        ]);
      
        if($validator->fails()) 
        {
            return response()->json($validator->messages());
    }
    $todo = Todo::find( id: $id );
    if(! $todo ||auth()->user()->id != $todo->user_id){
        return response()->json(['message'=> 'Todo Not found'],200);
    }
   else{
     $todo->update([
            'title' => $request->title,
            'content'=> $request->content,
            'is_completed' => $request->is_completed
        ]);
        return response()->json('Todo Updated Successfully' ,200);
    }
}
  public function delete(Request $request, $id){
    $todo = Todo::find( $id );
    if(! $todo || auth()->user()->id != $todo->user_id){
        return response()->json(['message'=> 'Todo Not Found'],200);
    } 
    else{
        $todo->delete();
        return response()->json('Todo deleted successfully',200);
  }


}
}
