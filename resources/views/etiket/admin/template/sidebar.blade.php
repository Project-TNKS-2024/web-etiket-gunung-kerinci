<aside class="left-sidebar">
   <!-- Sidebar scroll-->
   <div class="">
      <div class="brand-logo d-flex align-items-center justify-content-between">
         <a href="{{route('homepage.beranda')}}" class="text-nowrap logo-img">
            <img src="{{asset('assets/icon/tnks.png')}}" width="180" alt="" /><span>TNKS Admin</span>
         </a>
         <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
         </div>
      </div>
      <!-- Sidebar navigation-->
      <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
         <ul id="sidebarnav">
            @can('admin.dashboard')
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{route('admin.dashboard')}}" aria-expanded="false">
                  <span>
                     <i class="fa-solid fa-gauge-high"></i>
                  </span>
                  <span class="hide-menu">Dashboard</span>
               </a>
            </li>
            @endcan

            <li class="nav-small-cap">
               <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
               <span class="hide-menu">Destinasi</span>
            </li>
            @foreach ($DataDestinasi as $itemDestinasi)
            @canDestinasi($itemDestinasi->id)
            <li class="sidebar-item">
               <a class="sidebar-link has-arrow" data-bs-toggle="collapse" href="#collapseDestinasi{{$itemDestinasi->id}}" role="button" aria-expanded="false" aria-controls="collapseDestinasi{{$itemDestinasi->id}}">
                  <span>
                     <i class="fa-solid fa-mountain-sun"></i>
                  </span>
                  <span class="hide-menu">{{$itemDestinasi->nama}}</span>
               </a>
               <div class="collapse lis-collapse-destinasi" id="collapseDestinasi{{$itemDestinasi->id}}">
                  <ul class="list-unstyled">
                     @can('admin.destinasi.detail')
                     <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.destinasi.detail', ['id' => $itemDestinasi->id]) }}" aria-expanded="false">
                           <span>
                              <i class="fa-solid fa-map"></i>
                           </span>
                           <span class="hide-menu">Kelola Destinasi</span>
                        </a>
                     </li>
                     @endcan
                     @can('admin.destinasi.tiket')
                     <li class="sidebar-item">
                        <a class="sidebar-link" href="{{route('admin.destinasi.tiket', ['id' => $itemDestinasi->id])}}" aria-expanded="false">
                           <span>
                              <i class="fa-solid fa-ticket"></i>
                           </span>
                           <span class="hide-menu">Kelola Tiket</span>
                        </a>
                     </li>
                     @endcan
                     @can('admin.destinasi.booking')
                     <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.destinasi.booking',  ['id' => $itemDestinasi->id] )}}" aria-expanded="false">
                           <span>
                              <i class="fa-solid fa-tachograph-digital"></i>
                           </span>
                           <span class="hide-menu">Data Booking</span>
                        </a>
                     </li>
                     @endcan

                     <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false">
                           <span>
                              <i class="fa-solid fa-file-lines"></i>
                           </span>
                           <span class="hide-menu">Data Pendaki</span>
                        </a>
                     </li>

                  </ul>
               </div>
            </li>
            @endCanDestinasi
            @endforeach

            <li class="nav-small-cap">
               <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
               <span class="hide-menu">Master Data</span>
            </li>
            @can('admin.master.destinasi')
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{ route('admin.master.destinasi') }}" aria-expanded="false">
                  <span>
                     <i class="fa-solid fa-file-lines"></i>
                  </span>
                  <span class="hide-menu">Data Destinasi</span>
               </a>
            </li>
            @endcan
            @can('admin.master.pengunjung')
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{ route('admin.master.pengunjung')}}" aria-expanded="false">
                  <span>
                     <i class="fa-solid fa-file-lines"></i>
                  </span>
                  <span class="hide-menu">Account Pengunjung</span>
               </a>
            </li>
            @endcan
            @can('admins.akun.index')
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{route('admins.akun.index')}}" aria-expanded="false">
                  <span>
                     <i class="fa-solid fa-file-lines"></i>
                  </span>
                  <span class="hide-menu">Account Admin</span>
               </a>
            </li>
            @endcan
            @can('roles.index')
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{route('roles.index')}}" aria-expanded="false">
                  <span>
                     <i class="fa-solid fa-file-lines"></i>
                  </span>
                  <span class="hide-menu">Role Permision</span>
               </a>
            </li>
            @endcan

            <li class="nav-small-cap">
               <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
               <span class="hide-menu">Traking</span>
            </li>
            @can('admin.tracking')
            <li class="sidebar-item">
               <a class="sidebar-link" href="#" aria-expanded="false">
                  <span>
                     <i class="fa-solid fa-chart-line"></i>
                  </span>
                  <span class="hide-menu">Climber Tracking</span>
               </a>
            </li>
            @endcan

            <li class="nav-small-cap">
               <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
               <span class="hide-menu">Fitur</span>
            </li>
            @can('admin.fitur.scanTiket')
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{route('admin.fitur.scanTiket')}}" aria-expanded="false">
                  <span>
                     <i class="fa-solid fa-expand"></i>
                  </span>
                  <span class="hide-menu">Scan Tiket</span>
               </a>
            </li>
            @endcan
            <hr>
            @can('admin.setting')
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{route('admin.setting')}}" aria-expanded="false">
                  <span>
                     <i class="fa-solid fa-gear"></i>
                  </span>
                  <span class="hide-menu">Setting</span>
               </a>
            </li>
            @endcan
         </ul>

      </nav>
      <!-- End Sidebar navigation -->
   </div>


   <!-- End Sidebar scroll-->
</aside>