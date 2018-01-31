<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\User;
use Mail;
use Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class AccountController extends Controller
{

  public function getCreate() {
    return view('account.create');
  }

  public function postCreate(){
    $validator = Validator::make(Request::all(),
      array(
        'email'           => 'required|max:50|email|unique:users',
        'username'        => 'required|max:20|min:3|unique:users',
        'password'        => 'required|min:6',
        'password_again'  => 'required|same:password'
      )
    );

    if($validator->fails()) {
      return Redirect::route('account-create')
              ->withErrors($validator)
              ->withInput();
    } else {
        //Create account
        $email = Request::get('email');
        $username = Request::get('username');
        $password = Request::get('password');

        //Activation code
        $code = str_random(60);

        $user = User::create(array(
          'email' => $email,
          'username' => $username,
          'password' => Hash::make($password),
          'password_temp' => 0,
          'code' => $code,
          'active' => 0
        ));

        if($user){
          //send email
          Mail::send('emails.auth.activate', array('link' => URL::route('account-activate',$code), 'username' => $username), function($message) use ($user) {
            $message ->to($user->email, $user->username)->subject('Activate your account');
          });

          return Redirect::route('home')
                  //send along with Redirect a variable and global is a global message area on our template
                  //and is run on main.blade.php
                  ->with('global', 'Your account has activated and sent an email');
        }
    }
  }

  public function getActivate($code) {
    $user = User::where('code', '=', $code)->where('active', '=', 0);

    if($user->count()){
      $user = $user->first();

      // Update user to active state
      $user->active   = 1;
      $user->code     = '';

      if($user->save()){
        return Redirect::route('home')
              ->with('global', 'Your account has been activated.');
      }
    }

    return Redirect::route('home')
            ->with('global', 'We could not activate your account. Try again later.');
  }
}
