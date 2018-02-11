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
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{

  public function getSignIn(){
    return view('account.signin');
  }

  public function postSignIn(){
    $validator = Validator::make(Request::all(),
      array(
        'email'     => 'required|email',
        'password'  => 'required'
      )
    );

    if($validator->fails()){
      return Redirect::route('account-sign-in')
            ->withErrors($validator)
            ->withInput();
    }
    else{
      //Attempt user signin

      $remember = (Request::has('remember')) ? true : false;
      $auth = Auth::attempt(array(
        'email'     => Request::get('email'),
        'password'  => Request::get('password'),
        'active'    => 1
      ), $remember);

      if($auth) {
        return Redirect::intended('/');
      } else{
        return Redirect::route('account-sign-in')
              ->with('global', 'Email/password is wrong or account not activated.');
      }
    }

    return Redirect::route('account-sign-in')
          ->with('global', 'There was a problem signing you in.');
  }

  public function getSignOut() {
    Auth::logout();
    return Redirect::route('home');
  }

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

  public function getChangePassword() {
    return view('account.password');
  }

  public function postChangePassword() {
    $validator = Validator::make(Request::all(),
        array(
          'old_password' =>   'required',
          'password' =>       'required|min:6',
          'password_again' => 'required|same:password'
        )
      );

      if($validator->fails()) {
        return Redirect::route('account-change-password')
              ->withErrors($validator);
      } else{

        $user = User::find(Auth::user()->id);

        $old_password = Request::get('old_password');
        $password = Request::get('password');

        if(Hash::check($old_password, $user->getAuthPassword())) {
          //password user provided matches
          $user->password = Hash::make($password);

          if($user->save()) {
            return Redirect::route('home')
                  ->with('global', 'Your password has been changed.');
          }
          else{
            return Redirect::route('account-change-password')
                  ->with('global', 'Your old password was incorrect');
          }
        }
      }

      return Redirect::route('account-change-password')
            ->with('global', 'Your password could not be changed.');
  }

  public function getForgotPassword() {
    return view('account.forgot');
  }

  public function postForgotPassword() {
    $validator = Validator::make(Request::all(),
      array(
        'email' => 'required|email'
      )
    );

    if($validator->fails()) {
      return Redirect::route('account-forgot-password')
            ->withErrors($validator)
            ->withInput();
    } else{
      //change password

      $user = User::where('email', '=', Request::get('email'));

      if($user->count()) {
        $user = $user->first();

        //Generate a new code and password
        $code                 = str_random(60);
        $password             = str_random(10);

        $user->code           = $code;
        $user->password_temp  = Hash::make($password);

        if($user->save()) {
          Mail::send('emails.auth.forgot', array('link' => URL::route('account-recover', $code), 'username' => $user->username, 'password' => $password), function($message) use ($user) {
            $message->to($user->email, $user->username)->subject('Your new password');
          });

          return Redirect::route('home')
                ->with('global', 'We have sent you a new password');
        }
      }
    }

    return Redirect::route('account-forgot-password')
          ->with('global', 'Could not request new password.');
  }

  public function getRecover($code) {
    $user = User::where('code', '=', $code)->where('password_temp', '!=', '');

    if($user->count()) {
      $user = $user->first();

      $user->password = $user->password_temp;
      $user->password_temp = '';
      $user->code = '';

      if($user->save()) {
        return Redirect::route('home')
              ->with('global', 'Your account has been recovered and you can sign in with your new password');
      }
    }

    return Redirect::route('home')
          ->with('global', 'Could not recover your account');

  }

}
