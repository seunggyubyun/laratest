@extends('layout.main')

@section('content')
  <form action="{{ URL::route('account-forgot-password-post') }}" method="post">
    <div class="field">
      Email: <input type="text" name="email" {{ (Request::old('email')) ? 'value=' .e(Request::old('email')). '':''}}>
      @if($errors->has('email'))
          {{ $errors->first('email') }}
      @endif

    <input type="submit" value="Recover">
    {{ Form::token() }}
  </form>
@stop
