<!DOCTYPE html>
<html lang="en">

<head>
   <title>Log Viewer</title>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.css') }}" rel="stylesheet">
   <link href="{{ asset('fontawesome-free-6.5.2-web/css/all.css') }}" rel="stylesheet">
   <link href="{{ asset('DataTables/datatables.css') }}" rel="stylesheet">
   <style>
      #bg-body {
         background: linear-gradient(65deg, #e9eef3 50%, #333 50%);
         height: 100vh;
         width: 100vw;
         position: fixed;
         z-index: -1;
      }

      /* Awalnya tinggi hanya satu baris */
      .log-message {
         max-height: 1.5em;
         overflow: hidden;
         transition: max-height 0.3s ease-in-out;
         cursor: pointer;
      }

      /* Saat dibuka, tinggi akan menyesuaikan konten */
      .log-message.expanded {
         max-height: none;
      }

      .log-details {
         display: none;
         white-space: pre-wrap;
         background-color: #f8f9fa;
         padding: 10px;
         border-radius: 5px;
         font-size: 14px;
      }

      .bg-trans-white {
         background-color: #ffffff99;
      }

      #DataTables_Table_0_wrapper>div:first-child {
         display: none;
      }

      /* Mengatur background tabel menjadi transparan */
      .dataTable {
         background-color: #ffffff99 !important;
      }

      /* Header dan sel data juga dibuat transparan */
      .dataTable thead th,
      .dataTable tbody td {
         background-color: #ffffff99 !important;
         border-color: rgba(255, 255, 255, 0.5);
         /* Agar border tetap terlihat */
      }

      /* Untuk mengubah warna hover */
      .dataTable tbody tr:hover {
         background-color: rgba(255, 255, 255, 0.5) !important;
      }
   </style>
</head>

<body>
   <div id="bg-body"></div>
   <div class="d-flex">
      <div class="w-25 border-right p-4" style="max-width: 380px;">
         <h1 class="h5 fw-bold text-dark mb-3">Log Viewer</h1>
         <a href="{{route('admin.dashboard')}}" class="text-secondary mt-1">
            <i class="fas fa-arrow-left"></i> Back to Admin
         </a>

         {{-- Daftar File Log --}}
         <ul class="list-group mt-3">
            @foreach ($logFiles as $file)
            <li class="list-group-item d-flex justify-content-between align-items-center rounded shadow-sm border border-light bg-white mb-1">
               <a href="{{ route('admin.fitur.log', ['file' => basename($file)]) }}"
                  class="text-decoration-none fw-semibold text-dark">
                  {{ basename($file) }}
               </a>
               <span class="text-secondary small">{{ round(filesize($file) / 1024 / 1024, 2) }} MB</span>
            </li>
            @endforeach
         </ul>

      </div>

      <div class="w-auto p-4">
         {{-- Statistik Log --}}
         <div class="d-flex align-items-center justify-content-between mb-4 ">
            <div class="d-flex align-items-center gap-2">
               <span class="badge bg-primary p-2">Debug: {{ $logCounts['debug'] ?? 0 }}</span>
               <span class="badge bg-info p-2">Info: {{ $logCounts['info'] ?? 0 }}</span>
               <span class="badge bg-warning p-2 text-dark">Warning: {{ $logCounts['warning'] ?? 0 }}</span>
               <span class="badge bg-danger p-2">Error: {{ $logCounts['error'] ?? 0 }}</span>
            </div>
            <input type="search" onkeyup="searchLogs(this)" class="form-control flex-grow-1 ms-4 bg-trans-white" placeholder="Search...">
         </div>

         {{-- Tabel Log --}}
         <div class="table-responsive">
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th scope="col" style="width: 1%;">Waktu</th>
                     <th scope="col" style="width: 1%;">Channel</th>
                     <th scope="col" style="width: 1%;">Level</th>
                     <th scope="col">Deskripsi</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($logEntries as $index => $log)
                  <tr>
                     <td class="text-nowrap">{{ $log['time'] }}</td>
                     <td class="text-nowrap">{{ $log['channel'] }}</td>
                     <td class="text-nowrap">
                        <span class="badge bg-{{ $getBadgeClass($log['level']) }}">
                           {{ strtoupper($log['level']) }}
                        </span>
                     </td>
                     <td class="text-wrap" style="white-space: normal; word-break: break-word; min-width: 400px;">
                        <div class="log-message" id="log-message-{{ $index }}" onclick="toggleDetails({{ $index }})">
                           {{ $log['message'] }} <i class="fas fa-angle-down"></i>
                        </div>
                     </td>
                  </tr>
                  @endforeach
               </tbody>

            </table>
         </div>
      </div>
   </div>
   <script src="{{asset('modernize/libs/jquery/dist/jquery.min.js')}}"></script>
   <script src="{{asset('DataTables/datatables.js')}}"></script>

   <script>
      document.addEventListener("DOMContentLoaded", function() {
         let table = new DataTable("table", {
            paging: true, // Aktifkan paginasi
            searching: true, // Aktifkan pencarian
            ordering: true, // Aktifkan pengurutan kolom
            info: true, // Tampilkan jumlah data
            pageLength: 20, // Tampilkan 20 data per halaman
            language: {
               search: "Cari:",
               lengthMenu: "Tampilkan _MENU_ data per halaman",
               info: "Menampilkan _START_ - _END_ dari _TOTAL_ log",
               paginate: {
                  first: "Awal",
                  last: "Akhir",
                  next: "Selanjutnya",
                  previous: "Sebelumnya"
               }
            },

         });

         // Fungsi pencarian custom
         window.searchLogs = function(input) {
            let searchTerm = input.value.toLowerCase(); // Ambil teks pencarian dan ubah ke lowercase
            table.search(searchTerm).draw(); // Jalankan filter pencarian DataTables
         };
      });
   </script>
   <script>
      function toggleDetails(index) {
         let message = document.getElementById('log-message-' + index);
         let details = document.getElementById('details-' + index);

         if (message.classList.contains('expanded')) {
            message.classList.remove('expanded');
         } else {
            message.classList.add('expanded');
         }
      }
   </script>
</body>

</html>