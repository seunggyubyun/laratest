@extends('layout.main')

@section('content')
  <form action="{{ URL::route('account-sign-in-post') }}" method="post">

    <div class="field">
      Email: <input type="text" name="email" {{ (Request::old('email')) ? 'value ='. e(Request::old('email')) . '':''}}>
      @if($errors->has('email'))
        {{ $errors->first('email')  }}
      @endif
    </div>
    <div class="field">
      password: <input type="password" name="password">
      @if($errors->has('password'))
        {{ $errors->first('password')  }}
      @endif
    </div>
    <div class="field">
      <input type="checkbox" name="remember" id="remember">
      <label for="remember">
        Remember me
      </label>
    </div>
        </menu>
    <input type="submit" value="Sign in">
    {{ Form::token()}}
  </form>
@stop
