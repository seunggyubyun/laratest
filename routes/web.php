<?php

Route::get('/', array(
  'as' => 'home',
  'uses' => 'HomeController@home'
));


Route::get('/user/{username}', array(
  'as' => 'profile-user',
  'uses' => 'ProfileController@user'
));
/*
Authenticated group
*/
Route::group(array('before' => 'auth'), function() {

  /*
  \ Authenticated group
  */
  Route::group(array('before'=> 'csrf'), function() {

    /*
    Change Password (POST)
    */
    Route::post('/account/change-password', array(
      'as' => 'account-change-password-post',
      'uses' => 'AccountController@postChangePassword'
    ));
  });


  /*
  Sign out (GET)
  */
  Route::get('/account/sign-out', array(
    'as' => 'account-sign-out',
    'uses' => 'AccountController@getSignOut'
  ));


  /*
  Change Password (GET)
  */
  Route::get('/account/change-password', array(
    'as' => 'account-change-password',
    'uses' => 'AccountController@getChangePassword'
  ));
});

/*
| Unauthenticated group
nesting routes within group
*/
Route::group(array('before' => 'guest'), function() {

  /*
  crosssite request forgery protection group
  */
  Route::group(array('before' => 'csrf'), function(){
    /*
    Create account (POST)
    */
    Route::post('/account/create', array(
      'as' =>'account-create-post',
      'uses' => 'AccountController@postCreate'
    ));

    /*
    Sign in (POST)
    */
    Route::post('/account/signin', array(
      'as' => 'account-sign-in-post',
      'uses' => 'AccountController@postSignIn'
    ));

    /*
    Forgot password (POST)
    */
    Route::post('/account/forgot', array(
      'as' => 'account-forgot-password-post',
      'uses' => 'AccountController@postForgotPassword'
    ));

  });

  Route::get('/account/recover/{code}', array(
    'as' => 'account-recover',
    'uses' => 'AccountController@getRecover'
  ));

  /*
  Sign in (GET)
  */
  Route::get('/account/signin', array(
    'as' => 'account-sign-in',
    'uses' => 'AccountController@getSignIn'
  ));

  /*
  Create account (GET)
  */
  Route::get('/account/create', array(
    'as' =>'account-create',
    'uses' => 'AccountController@getCreate'
  ));

  Route::get('/account/activate/{code}', array(
    'as' => 'account-activate',
    'uses' => 'AccountController@getActivate'
  ));

  Route::get('/account/forgot', array(
    'as' => 'account-forgot-password',
    'uses' => 'AccountController@getForgotPassword'
  ));

});

?>
