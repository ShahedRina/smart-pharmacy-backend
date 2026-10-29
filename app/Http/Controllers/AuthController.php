<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm(){
        return view('auth.login');
    }

    
    public function login(Request $request){
$credentials = $request ->only('emily','password') ;
if(Auth::attempt($credentials )){
    return redirect() ->intended('home');
}
return back() ->withErrors(['email' => 'the provided credentials do not match our0',]);
}

public function showsingupform(){
    return view('auth.register');
}
public function register(Request $request){
    $request ->validate([
        'name'=> 'requied|string|email|max:255',
        'email'=> 'requied|string|email|max:255|unique:user',
        'password'=> 'requied|string|min:8|confirmed',
    ]);

    $password_hash = Hash::make($request ->password);
      User::create ([

        'name'=> $request -> name,
        'email'=> $request -> email,
        'password'=> Hash::make ($request -> password),

    ]);
    
    return redirect() ->route('login');
}
public function logout(){
Auth::logout();
return redirect() -> route('login');
}

}