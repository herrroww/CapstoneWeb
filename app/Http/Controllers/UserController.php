<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

        $users = User::all();
        return view('usuarios.show', ['users' =>$users]);
    }


    public function show($id){

        $users = User::all();

        return view('usuarios.show', ['users' => User::findOrFail($id)]);

    }

    public function edit($id){
        return view('usuarios.edit', ['users' => User::findOrFail($id)]);
    }

    public function update(Request $request, $id){
        $user = User::findOrFail($id);
        
        $user->name = $request->get('name');
        
        $user->email = $request->get('email');

        $user->update();

        return redirect('showuser');
    }
}
