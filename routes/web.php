<?php

Route::get('/', array(
  'as' => 'home',
  'uses' => 'HomeController@home'
));

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

  });

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
});

?>
