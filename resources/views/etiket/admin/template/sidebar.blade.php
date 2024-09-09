<aside class="left-sidebar">
   <!-- Sidebar scroll-->
   <div class="">
      <div class="brand-logo d-flex align-items-center justify-content-between">
         <a href="{{route('homepage.beranda')}}" class="text-nowrap logo-img">
            <img src="{{asset('assets/img/logo/logo bulat.png')}}" width="180" alt="" /><span>TNKS Admin</span>
         </a>
         <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
         </div>
      </div>
      <!-- Sidebar navigation-->
      <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
         <ul id="sidebarnav">
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{route('admin.dashboard')}}" aria-expanded="false">
                  <span>
                     <img src="{{asset('assets/icon/tnks/darhboard_alt-dark.svg')}}"></img>
                  </span>
                  <span class="hide-menu">Dashboard</span>
               </a>
            </li>

            <li class="nav-small-cap">
               <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
               <span class="hide-menu">Destinasi</span>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" data-bs-toggle="collapse" href="#collapseGunungKerinci" role="button" aria-expanded="false" aria-controls="collapseGunungKerinci">
                  <span>
                     <img src="{{ asset('assets/icon/tnks/mountain-sun-solid.svg') }}"></img>
                  </span>
                  <span class="hide-menu">Gunung Kerinci</span>
               </a>
               <div class="collapse lis-collapse-destinasi" id="collapseGunungKerinci">
                  <ul class="list-unstyled">
                     <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.destinasi.detail', ['id' => 1]) }}" aria-expanded="false">
                           <span>
                              <img src="{{ asset('assets/icon/tnks/map-dark.svg') }}"></img>
                           </span>
                           <span class="hide-menu">Kelola Destinasi</span>
                        </a>
                     </li>
                     <li class="sidebar-item">
                        <a class="sidebar-link" href="{{route('admin.tiket.daftar')}}" aria-expanded="false">
                           <span>
                              <img src="{{ asset('assets/icon/tnks/ticket-dark.svg') }}"></img>
                           </span>
                           <span class="hide-menu">Kelola Tiket</span>
                        </a>
                     </li>
                     <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.gate.daftar') }}" aria-expanded="false">
                           <span>
                              <img src="{{ asset('assets/icon/tnks/pointers-dark.svg') }}"></img>
                           </span>
                           <span class="hide-menu">Kelola Gate</span>
                        </a>
                     </li>
                     <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.booking.now.read') }}" aria-expanded="false">
                           <span>
                              <img src="{{ asset('assets/icon/tnks/ticket_alt-dark.svg') }}"></img>
                           </span>
                           <span class="hide-menu">Data Booking</span>
                        </a>
                     </li>
                     <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false">
                           <span>
                              <img src="{{ asset('assets/icon/tnks/group-dark.svg') }}"></img>
                           </span>
                           <span class="hide-menu">Data Pendaki</span>
                        </a>
                     </li>
                     <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false">
                           <span>
                              <img src="{{ asset('assets/icon/tnks/navigate-dark.svg') }}"></img>
                           </span>
                           <span class="hide-menu">Monitoring Gunung</span>
                        </a>
                     </li>
                     <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false">
                           <span>
                              <img src="{{asset('assets/icon/tnks/file_dock-dark.svg')}}"></img>
                           </span>
                           <span class="hide-menu">Cetak Laporan</span>
                        </a>
                     </li>
                  </ul>
               </div>
            </li>

            <!-- <li class="sidebar-item">
               <a class="sidebar-link" href="{{ route('admin.destinasi.daftar') }}" aria-expanded="false">
                  <span>
                     <img src="{{ asset('assets/icon/tnks/map-dark.svg') }}"></img>
                  </span>
                  <span class="hide-menu">Kelola Destinasi</span>
               </a>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{route('admin.tiket.daftar')}}" aria-expanded="false">
                  <span>
                     <img src="{{ asset('assets/icon/tnks/ticket-dark.svg') }}"></img>
                  </span>
                  <span class="hide-menu">Kelola Tiket</span>
               </a>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{ route('admin.gate.daftar') }}" aria-expanded="false">
                  <span>
                     <img src="{{ asset('assets/icon/tnks/pointers-dark.svg') }}"></img>
                  </span>
                  <span class="hide-menu">Kelola Gate</span>
               </a>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{ route('admin.booking.now.read') }}" aria-expanded="false">
                  <span>
                     <img src="{{ asset('assets/icon/tnks/ticket_alt-dark.svg') }}"></img>
                  </span>
                  <span class="hide-menu">Data Booking</span>
               </a>
            </li> -->

            <li class="nav-small-cap">
               <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
               <span class="hide-menu">Master Data</span>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{ route('admin.destinasi.daftar') }}" aria-expanded="false">
                  <span>
                     <img src="{{ asset('assets/icon/tnks/map-dark.svg') }}"></img>
                  </span>
                  <span class="hide-menu">Data Destinasi</span>
               </a>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="#" aria-expanded="false">
                  <span>
                     <img src="{{ asset('assets/icon/tnks/user_alt-dark.svg') }}"></img>
                  </span>
                  <span class="hide-menu">Account Pengunjung</span>
               </a>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="#" aria-expanded="false">
                  <span>
                     <img src="{{ asset('assets/icon/tnks/user_alt-dark.svg') }}"></img>
                  </span>
                  <span class="hide-menu">Account Admin</span>
               </a>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="#" aria-expanded="false">
                  <span>
                     <img src="{{ asset('assets/icon/tnks/user_alt-dark.svg') }}"></img>
                  </span>
                  <span class="hide-menu">Role Permision</span>
               </a>
            </li>

            <li class="nav-small-cap">
               <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
               <span class="hide-menu">Traking</span>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="#" aria-expanded="false">
                  <span>
                     <img src="{{ asset('assets/icon/tnks/compass-dark.svg') }}"></img>
                  </span>
                  <span class="hide-menu">Climber Tracking</span>
               </a>
            </li>

            <li class="nav-small-cap">
               <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
               <span class="hide-menu">Fitur</span>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{route('admin.fitur.scanTiket')}}" aria-expanded="false">
                  <span>
                     <img src="{{asset('assets/icon/tnks/group_scan-dark.svg')}}"></img>
                  </span>
                  <span class="hide-menu">Scan Tiket</span>
               </a>
            </li>
            <hr>
            <li class="sidebar-item">
               <a class="sidebar-link" href="#" aria-expanded="false">
                  <span>
                     <img src="{{asset('assets/icon/tnks/setting_line-dark.svg')}}"></img>
                  </span>
                  <span class="hide-menu">Setting</span>
               </a>
            </li>
         </ul>

      </nav>
      <!-- End Sidebar navigation -->
   </div>
   <!-- End Sidebar scroll-->
</aside>