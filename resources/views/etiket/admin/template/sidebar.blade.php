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
                     "icon" => "ti ti-layout-dashboard",
                  ],
                  [
                     "name" => "Master Data",
                     "type" => "multiple",
                     "list" => [
                        [
                           "name" => "Kelola Tiket",
                           "type" => "single",
                           "link" => route('admin.tiket.daftar'),
                           "icon" => "ti ti-article",
                        ],
                        [
                           "name" => "Kelola Destinasi",
                           "type" => "single",
                           "link" => "#",
                           "icon" => "ti ti-article",
                        ],
                        [
                           "name" => "Kelola Gate",
                           "type" => "single",
                           "link" => "#",
                           "icon" => "ti ti-article",
                        ],
                     ]
                  ],
                  [
                     "name" => "Gunung Kerinci",
                     "type" => "multiple",
                     "list" => [
                        [
                           "name" => "Data Booking",
                           "type" => "single",
                           "link" => "#",
                           "icon" => "ti ti-login",
                        ],
                        [
                           "name" => "Data Pendaki",
                           "type" => "single",
                           "link" => "#",
                           "icon" => "ti ti-login",
                        ],
                        [
                           "name" => "Climber Tracking",
                           "type" => "single",
                           "link" => "#",
                           "icon" => "ti ti-login",
                        ],
                        [
                           "name" => "Monitoring Gunung",
                           "type" => "single",
                           "link" => "#",
                           "icon" => "ti ti-login",
                        ],
                     ]
                  ]
               ],
            ])

            <hr>
            <li class="sidebar-item">
               <a class="sidebar-link" href="./authentication-login.html" aria-expanded="false">
                  <span>
                     <i class="ti ti-login"></i>
                  </span>
                  <span class="hide-menu">Data Pengguna</span>
               </a>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="./icon-tabler.html" aria-expanded="false">
                  <span>
                     <i class="ti ti-mood-happy"></i>
                  </span>
                  <span class="hide-menu">Scan Qr</span>
               </a>
            </li>
            <li class="sidebar-item">
               <a class="sidebar-link" href="./sample-page.html" aria-expanded="false">
                  <span>
                     <i class="ti ti-aperture"></i>
                  </span>
                  <span class="hide-menu">Cetak Laporan</span>
               </a>
            </li>
         </ul>

      </nav>
      <!-- End Sidebar navigation -->
   </div>
   <!-- End Sidebar scroll-->
</aside>
