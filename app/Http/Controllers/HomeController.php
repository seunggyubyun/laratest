<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\User;
use Mail;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
      // Mail::send('emails.auth.test', array('name' => 'Alex'), function($message){
      //   $message->to('seunggyu0128@gmail.com', 'Seunggyu')->subject('Test email');
      // });
      return view('home');
    }
}
