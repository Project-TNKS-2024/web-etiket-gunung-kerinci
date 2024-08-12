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

            @include('etiket.admin.template.sidebar.list', [
            "sidebar" => [
            [
            "name" => "Dashboard",
            "type" => "single",
            "link" => route('admin.dashboard'),
            "icon" => [
            "name" => asset('assets/icon/tnks/darhboard_alt-dark.svg'),
            "type" => "image"
            ],
            ],

            [
            "name" => "Master Data",
            "type" => "multiple",
            "list" => [
            [
            "name" => "Kelola Tiket",
            "type" => "single",
            "link" => route('admin.tiket.daftar'),
            "icon" => [
            "name" => asset('assets/icon/tnks/ticket-dark.svg'),
            "type" => "image"
            ],
            ],
            [
            "name" => "Kelola Destinasi",
            "type" => "single",
            "link" => route('admin.destinasi.daftar'),
            "icon" => [
            "name" => asset('assets/icon/tnks/map-dark.svg'),
            "type" => "image"
            ],
            ],
            [
            "name" => "Kelola Gate",
            "type" => "single",
            "link" => route('admin.gate.daftar'),
            "icon" => [
            "name" => asset('assets/icon/tnks/pointers-dark.svg'),
            "type" => "image"
            ],
            ],
            ],

            ],
            [
            "name" => "Gunung Kerinci",
            "type" => "multiple",
            "list" => [
            [
            "name" => "Data Pengguna",
            "type" => "single",
            "link" => "#",
            "icon" => [
            "name" => asset('assets/icon/tnks/user_alt-dark.svg'),
            "type" => "image"
            ],
            ],
            [
            "name" => "Data Booking",
            "type" => "single",
            "link" => route('admin.booking.now.read'),
            "icon" => [
            "name" => asset('assets/icon/tnks/ticket_alt-dark.svg'),
            "type" => "image"
            ],
            ],
            [
            "name" => "Data Pendaki",
            "type" => "single",
            "link" => "#",
            "icon" => [
            "name" => asset('assets/icon/tnks/group-dark.svg'),
            "type" => "image"
            ],
            ],
            [
            "name" => "Climber Tracking",
            "type" => "single",
            "link" => "#",
            "icon" => [
            "name" => asset('assets/icon/tnks/compass-dark.svg'),
            "type" => "image"
            ],
            ],
            [
            "name" => "Monitoring Gunung",
            "type" => "single",
            "link" => "#",
            "icon" => [
            "name" => asset('assets/icon/tnks/navigate-dark.svg'),
            "type" => "image"
            ],
            ],
            ]
            ],
            ],
            ])

            <hr>
            <li class="sidebar-item">
               <a class="sidebar-link" href="{{route('admin.fitur.scanTiket')}}" aria-expanded="false">
                  <span>
                     <img src="{{asset('assets/icon/tnks/group_scan-dark.svg')}}"></img>
                  </span>
                  <span class="hide-menu">Scan Qr</span>
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