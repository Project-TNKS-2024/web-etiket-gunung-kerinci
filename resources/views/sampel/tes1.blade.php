<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta name="description" content="">
   <meta name="author" content="">
   <title>{{ config('app.name', 'Laravel') }}</title>

   <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">
   <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/font/bootstrap-icons.min.css') }}">
   <link rel="stylesheet" href="{{ asset('componen/colorplate.css') }}">
   <style>
      .nav-logo img {
         width: 50px;
      }

      .nav-logo {
         font-weight: bold;
         margin-top: 10px;
         text-align: center;
      }

      html,
      body {
         height: 100%;
         width: 100%;
         margin: 0;
         padding: 0;
         display: flex;
         justify-content: center;
         align-items: center;
         background-color: #f8f9fa;
      }

      .card {
         max-width: 100%;
         padding: 20px;
      }
   </style>
</head>

<body>
   <div class="container">
      <div class="row justify-content-center">
         <div class="col-12 col-md-6 d-none d-md-block text-center">
            <img src="path_to_your_image.jpg" alt="Taman Nasional Kerinci Seblat" class="img-fluid">
         </div>
         <div class="col-12 col-md-6">
            <div class="card border border-0 shadow-lg">
               <div class="card-body">
                  <h2 class="text-center">Taman Nasional Kerinci Seblat</h2>
                  <h5 class="text-center">Selamat Datang!</h5>
                  <h5 class="text-center fw-bold">Pendaftaran Akun Pendaki</h5>
                  <form method="POST" action="{{ route('etiket.in.actionregister') }}">
                     @csrf
                     <div class="form-group mb-3">
                        <label for="fullname" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('fullname') is-invalid @enderror" id="fullname" name="fullname" value="{{ old('fullname') }}" required>
                        @error('fullname')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                     </div>
                     <div class="form-group mb-3">
                        <label for="phone" class="form-label">Nomor Handphone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                     </div>
                     <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                     </div>
                     <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                     </div>
                     <div class="form-group mb-3">
                        <label for="password_confirmation" class="form-label">Ulangi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                     </div>
                     <div class="form-group mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <div class="form-check">
                           <input class="form-check-input" type="radio" name="gender" id="male" value="Laki-Laki" required>
                           <label class="form-check-label" for="male">
                              Laki-Laki
                           </label>
                        </div>
                        <div class="form-check">
                           <input class="form-check-input" type="radio" name="gender" id="female" value="Perempuan" required>
                           <label class="form-check-label" for="female">
                              Perempuan
                           </label>
                        </div>
                     </div>
                     <div class="form-group mb-3">
                        <button type="submit" class="btn btn-primary w-100">Buat Akun</button>
                     </div>
                     <div class="form-group mb-3">
                        <a href="#" class="btn btn-outline-secondary w-100">Daftar Dengan Akun Google</a>
                     </div>
                     <div class="form-group mb-3 text-center">
                        <p>Sudah Punya Akun? <a href="{{ route('etiket.in.login') }}">Log in</a></p>
                     </div>
                  </form>
                  <footer class="text-center mt-4">
                     <p>© Taman Nasional Kerinci Seblat 2024</p>
                  </footer>
               </div>
            </div>
         </div>
      </div>
   </div>

   <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.min.js') }}"></script>
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>