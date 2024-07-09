<!doctype html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta name="description" content="">
   <meta name="author" content="">
   <title>{{ config('app.name', 'Laravel') }}</title>
   <link rel="shortcut icon" type="image/png" href="{{asset('assets/img/logo/logo bulat.png')}}" />
   <link rel="stylesheet" href="{{asset('modernize/css/styles.css')}}" />
   <style>
      .logo-img {
         text-decoration: none;
         color: black;
         font-weight: bold;
         font-size: 20px;
         font-family: sans-serif;
      }

      .logo-img img {
         width: 40px;
         margin-right: 5px;
      }
   </style>
   @yield('css')
</head>

<body>
   <!--  Body Wrapper -->
   <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
      <!-- ========================================= -->
      @include('etiket.admin.template.sidebar')
      <!--  Main wrapper -->
      <div class="body-wrapper">
         <!--  Header Start -->
         <!-- ================================================ -->
         @include('etiket.admin.template.navbar')
         <!--  Header End -->
         <div class="container-fluid" style="background-color: #f3f3f3;">
            <!--  Row 1 -->
            @yield('main')


            <!-- footer  -->
            <!-- ============================================== -->
            @include('etiket.admin.template.footer')
         </div>
      </div>
   </div>
   <script src="{{asset('modernize/libs/jquery/dist/jquery.min.js')}}"></script>
   <script src="{{asset('modernize/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>

   <script src="{{asset('modernize/js/sidebarmenu.js')}}"></script>
   <script src="{{asset('modernize/js/app.min.js')}}"></script>

   <script src="{{asset('modernize/libs/apexcharts/dist/apexcharts.min.js')}}"></script>
   <script src="{{asset('modernize/libs/simplebar/dist/simplebar.js')}}"></script>
   <script src="{{asset('modernize/js/dashboard.js')}}"></script>

   @yield('js')
</body>

</html>