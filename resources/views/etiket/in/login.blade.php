<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta name="description" content="">
   <meta name="author" content="">
   <title>{{ config('app.name', 'Laravel') }}</title>

   <link rel="stylesheet" href="{{asset('bootstrap-5.3.3-dist/css/bootstrap.min.css')}}">
   <link rel="stylesheet" href="{{asset('bootstrap-5.3.3-dist/font/bootstrap-icons.min.css')}}">
   <link rel="stylesheet" href="{{asset('componen/colorplate.css')}}">
   <style>
      .nav-logo img {
         width: 50px;
      }

      .nav-logo {
         font-weight: bold;
         text-align: center;
         color: black;
      }

      body {
         /* height: 100vh; */
         background-image: url("{{asset('img/bg/bg login.png')}}");
         background-size: cover;
      }

      a {
         text-decoration: none;
      }
   </style>
</head>

<body>
   <div style="width: 100%;
   margin-top:30px;
    position: fixed;">
      <a class="mx-auto nav-logo d-block mx-auto" href="{{route('homepage.beranda')}}">
         <img src="{{asset('img/logo/logo bulat.png')}}" alt="logo">
         Taman nasional Kerinci Seblat
      </a>
   </div>
   <div class="container" style="
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;">
      <div class="row justify-content-center w-100">
         <div class=" col-md-6">
            <div class="card border border-0 shadow-lg">
               <div class="card-body">
                  <h5 class="mt-2 text-center fw-bold">Masuk ke akun</h5>
                  <form method="POST" action="{{ route('etiket.in.actionlogin') }}">
                     @csrf
                     <div class="form-group mb-3">
                        <label for="email" class="form-label ">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                           {{ $message }}
                        </span>
                        @enderror
                     </div>

                     <div class="form-group mb-3">
                        <label for="password" class="form-label ">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                           {{ $message }}
                        </span>
                        @enderror
                     </div>

                     <div class="form-group mb-3">
                        <div class="d-flex justify-content-between">
                           <div>
                              <input type="checkbox" name="remember" id="remember" {{ old('remember')? 'checked' : '' }}>
                              <label for="remember">Ingat saya</label>
                           </div>
                           <div>
                              <a href="#">Lupa Password?</a>
                           </div>
                        </div>
                     </div>

                     <div class="form-group mb-3">
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                     </div>

                     <div class="form-group mb-3">
                        <a href="" class="btn btn-outline-secondary w-100">Login Dengan Akun Google</a>
                     </div>

                     <div class="form-group mb-3 text-center">
                        <p>Tidak Punya Akun? <a href="{{route('etiket.in.register')}}">Daftar Sekarang</a></p>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>

   <script src="{{asset('bootstrap-5.3.3-dist/js/bootstrap.min.js')}}"></script>
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
</body>

</html>