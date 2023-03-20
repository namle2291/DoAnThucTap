<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    function index()
    {
        return view('admin.login');
    }
    function dashboard()
    {
        return view('admin.dashboard');
    }
    function store_login(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);

        $this->customValidate($data, [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email' => 'Email',
            'password' => 'Password'
        ]);
        if (Auth::attempt($data)) {
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }
        return back();
    }
    function register(){
        return view('admin.register');
    }
    function store_register(Request $request){
        $data = $request->all();
        unset($data['_token']);

        $rules = [
            'email'=>'required|email',
            'name'=>'required',
            'password'=>'required|min:6',
            'confirm_password'=>'required|same:password|min:6'
        ];
        $messages = [
            'email'=>'Email',
            'name'=>'Name',
            'password'=>'Password',
            'confirm_password'=>'Confirm Password'
        ];
        $this->customValidate($data,$rules,$messages);

        $data['password'] = Hash::make($request->password);
        unset($data['confirm_password']);

        $user = User::create($data);
        $user->save();
        return redirect()->route('admin.login');
        
    }
    function logout(Request $request){
        Auth::logout();
 
        $request->session()->regenerateToken();
     
        return redirect()->route('admin.login');
    }
}