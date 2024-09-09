<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta name="description" content="">
   <meta name="author" content="">
   <title>{{ config('app.name', 'Laravel') }}</title>
   <link rel="stylesheet" href="{{ asset('assets/img/logo/logo bulat.png') }}" />

   <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">
   <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/font/bootstrap-icons.min.css') }}">
   <link rel="stylesheet" href="{{ asset('fontawesome-free-6.5.2-web/css/all.css') }}">
   <link rel="stylesheet" href="{{ asset('componen/colorplate.css') }}">
   <link rel="stylesheet" href="{{ asset('componen/tailwind-classes.css') }}">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet">
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <style>
      * {
         font-family: Verdana, Geneva, Tahoma, sans-serif;
      }

      .bg-linear-gradient-primary {
         background: linear-gradient(263deg, #0169BF 12.63%, #63B8FF 80.63%);
         color: #fff;
         border: none;
         padding: 10px 20px;
         text-align: center;
         text-decoration: none;
         display: inline-block;
      }

      .bg-linear-gradient-danger {
         background: linear-gradient(263deg, #da260e 12.63%, #FF6363 80.63%);
         color: #fff;
         border: none;
         padding: 10px 20px;
         text-align: center;
         text-decoration: none;
         display: inline-block;
      }

      a,
      .btn-email {
         border-radius: 5px;
         padding: 3px 2px;
         color: white;
         text-decoration: none;
         /* To remove underline if needed */
      }

      .btn-email:active,
      .btn-email:focus,
      .btn-email:hover {
         color: white;
         text-decoration: none;
         /* To remove underline if needed */
      }
   </style>

</head>

<body>
   <div style="">

      <div>
         Silahkan klik tombol berikut <a href="{{ route('resetpassword', [$token]) }}"
            class="btn-email bg-linear-gradient-primary">Reset Password</a>
      </div>

      <div>atau klik tautan berikut untuk mereset password:</div>
      <div>
         <a
            href="{{ route('resetpassword', [$token]) }}">http://localhost:8000/reset-password/{{ $token }}</a>
      </div>
   </div>
</body>

</html>