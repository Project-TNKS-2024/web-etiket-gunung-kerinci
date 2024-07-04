<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
   <meta name="author" content="AdminKit">
   <meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

   <link rel="preconnect" href="https://fonts.gstatic.com">
   <link rel="shortcut icon" href="{{ asset('adminkit/img/icons/icon-48x48.png') }}" />

   <link rel="canonical" href="https://demo-basic.adminkit.io/" />

   <title>AdminKit Demo - Bootstrap 5 Admin Template</title>

   <link href="{{ asset('adminkit/css/app.css') }}" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

   @yield('css')

</head>

<body>

   <div class="wrapper">
      @include('etiket.template.sidebar')
      <div class="main">
         @include('etiket.template.navbar')

         <main class="content">
            @yield('main')
         </main>

         @include('etiket.template.footer')

      </div>

   </div>



   @yield('modal')

   <script src="{{asset('adminkit/js/app.js')}}"></script>

   @yield('js')

</body>

</html>