<nav class="navbar navbar-expand-sm gk-bg-primary700 index-navbar py-0">
   <div class="w-100 index-nav-ats gk-bg-neutrals-base-white py-2">
      <div class="container d-flex justify-content-between align-items-center">
         <a href="{{route('homepage.beranda')}}">
            <img src="{{asset('img/logo/logo bulat.png')}}" alt="">
            Taman Nasional Kelinci Seblat
         </a>
         <div>
            <select class="form-select form-select-sm" aria-label="Small select example">
               <option value="id" selected>Pilih Bahasa : Indonesia</option>
               <option value="en">Select Language: English</option>
            </select>
         </div>
      </div>
   </div>
   <div class="container">
      <div></div>
      <button class="navbar-toggler index-nav-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
         <ul class="navbar-nav me-auto mb-2 mb-sm-0">
            <li class="nav-item">
               <a class="nav-link text-white pe-2" aria-current="page" href="#">Beranda</a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white pe-2" aria-current="page" href="#">SOP Pendakian</a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white pe-2" aria-current="page" href="#">Panduan Booking</a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white pe-2" aria-current="page" href="#">Booking Online</a>
            </li>
         </ul>
         <ul class="navbar-nav mb-2 mb-sm-0">
            <li class="nav-item">
               <a class="nav-link text-white pe-2" aria-current="page" href="{{route('etiket.in.register')}}">Register</a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white" aria-current="page" href="{{route('etiket.in.login')}}">Login</a>
            </li>
         </ul>

      </div>
   </div>
</nav>