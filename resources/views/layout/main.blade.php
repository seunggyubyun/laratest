<!DOCTYPE html>
//Session allows the webpage to disappear when it's refreshed only appears once
<html>
  <head>
    <title>Authentication system</title>
  </head>
  <body>

    @if(Session::has('global'))
      <p>{{ Session::get('global') }}</p>
    @endif
    @include('layout.navigation')
    @yield('content')
  </body>
  </html>
