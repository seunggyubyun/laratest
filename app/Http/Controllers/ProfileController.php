<?php

namespace App\Http\Controllers;

use App\User;
use App;

class ProfileController extends Controller {

  public function user($username) {
    $user = User::where('username', '=', $username);

    if($user->count()) {
      $user = $user->first();
      return view('profile.user')
            ->with('user', $user);
    }

    return App::abort(404);
  }
}
 ?>
