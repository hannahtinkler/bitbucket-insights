
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>{{ config('app.name') }}</title>

    <!-- Custom styles for this template -->
    <link href="/css/app.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  </head>

  <body>

    <div class="container mb-5 pb-5" id="app">
      <div class="navbar">
        <h1 class="navbar__heading">
          <a href="/">Bitbucket Insights</a>
        </h1>

        <ul class="nav my-3 d-flex justify-content-end">
          <li class="nav-item">
            <a class="nav-link {{ Request::is('/reviews') ? 'active' : '' }}" href="{{ route('reviews') }}">Open PRs</a>
          </li>
          <li class="nav-item {{ Request::is('/merges') }}">
            <a class="nav-link" href="{{ route('merges') }}">Merged PRs</a>
          </li>
        </ul>
      </div>

      @yield('content')

      <!-- <footer class="footer">
        <p>&copy; Company 2017</p>
      </footer> -->

    </div>
    <script src="/js/app.js"></script>
  </body>
</html>
