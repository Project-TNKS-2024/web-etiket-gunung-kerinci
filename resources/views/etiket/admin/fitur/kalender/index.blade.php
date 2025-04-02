@extends('etiket.admin.template.index')

@section('css')
<style>
   .fc .fc-daygrid-event-harness {
      width: 15px;
      display: inline-block;
   }
</style>
@endsection

@section('main')
<div class="card">
   @php
   $currentDate = DateTime::createFromFormat('m-Y', $bulan);
   $monthName = $currentDate->format('F Y'); // Nama bulan dalam bahasa Inggris
   $prevMonth = $currentDate->modify('-1 month')->format('m-Y');
   $nextMonth = $currentDate->modify('+2 month')->format('m-Y'); // Balik ke bulan depan setelah -1
   @endphp

   <div class="card-header d-flex justify-content-between align-items-center">
      <h3><b>Kalender {{ __($monthName) }}</b></h3>
      <div>
         <a href="{{ route('admin.fitur.kalender', ['b' => $prevMonth]) }}" class="btn btn-sm btn-light me-2">
            <i class="fa-solid fa-chevron-left" style="color:#5d87ff"></i>
         </a>
         <a href="{{ route('admin.fitur.kalender', ['b' => $nextMonth]) }}" class="btn btn-sm btn-light">
            <i class="fa-solid fa-chevron-right" style="color:#5d87ff"></i>
         </a>
      </div>
   </div>

   <div class="card-body">
      <div class="row">
         <div class="col-12 col-md-8">
            <div id="calendar"></div>
         </div>
         <div class="col-12 col-md-4 d-flex flex-column">
            <h5>Tanggal: <span id="selected-date">-</span></h5>
            <ul class="list-group" id="event-list">
               <li class="list-group-item bg-body-secondary">Pilih tanggal untuk melihat event.</li>
            </ul>
            <!-- Tombol "Tambah Event" sejajar dengan kalender -->
            <div class="d-flex justify-content-between mt-auto">
               <button class="btn btn-primary flex-grow-1 me-2" onclick="addEvent()">Tambah Event</button>
               <button class="btn btn-secondary btn-sm" onclick="addJson()">
                  <i class="fa-solid fa-file-code"></i>
               </button>
            </div>

         </div>
      </div>
   </div>
</div>

<!-- Modal Create Event -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="eventModalLabel">Tambah Event</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form id="eventForm" method="post" action="{{ route('admin.fitur.kalender.storeEvent') }}">
               @csrf
               <div class="mb-3">
                  <label for="title" class="form-label">Nama Event:</label>
                  <input type="text" id="title" name="title" class="form-control" required>
               </div>
               <div class="mb-3">
                  <label for="eventStartDate" class="form-label">Tanggal Mulai:</label>
                  <input type="date" id="eventStartDate" name="eventStartDate" class="form-control" required>
               </div>
               <div class="mb-3">
                  <label for="eventEndDate" class="form-label">Tanggal Selesai:</label>
                  <input type="date" id="eventEndDate" name="eventEndDate" class="form-control" required>
               </div>
               <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" id="is_holiday" name="is_holiday" value="1">
                  <label class="form-check-label" for="is_holiday">Tandai sebagai hari libur</label>
               </div>
               <div class="d-flex justify-content-end">
                  <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary">Simpan</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Modal Create Event -->
<div class="modal fade" id="eventJSONModal" tabindex="-1" aria-labelledby="eventJSONModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="eventJSONModalLabel">Tambah Event (JSON)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <p>Masukkan data event dalam format JSON berikut:</p>
            <pre>{
   "title": "Nama Event",
   "start_date": "YYYY-MM-DD",
   "end_date": "YYYY-MM-DD",
   "is_holiday": true/false
}</pre>
            <form method="post" action="{{ route('admin.fitur.kalender.storejsonEvent') }}" enctype="multipart/form-data">
               @csrf
               <div class="mb-3">
                  <input id="jsonEventInput" name="jsonEventInput" class="form-control" type="file">
               </div>

               <div class="d-flex justify-content-end">
                  <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary">Simpan</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>


<div class="d-none">
   <form method="post" id="formDelete" action="{{ route('admin.fitur.kalender.destroyEvent') }}">
      @csrf
      <input type="hiden" name="id" value="">
   </form>
</div>
@endsection

@section('js')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
   // document.addEventListener('DOMContentLoaded', function() {
   const calendarEl = document.getElementById('calendar');
   const eventList = document.getElementById('event-list');
   const selectedDateSpan = document.getElementById('selected-date');

   const eventForm = document.getElementById('eventForm');
   const deleteForm = document.getElementById('formDelete');

   const dataEvents = JSON.parse('@json($events)');
   const dataBulan = JSON.parse('@json($bulan)'); // Format "m-Y"

   // Pecah bulan & tahun dari format "m-Y"
   const [month, year] = dataBulan.split('-');

   // Pastikan format tanggal awal sesuai FullCalendar
   const initialDate = `${year}-${month.padStart(2, '0')}-01`;

   // Format event agar sesuai dengan FullCalendar
   let eventsData = dataEvents.map(event => ({
      id: event.id,
      title: event.title,
      start: event.start_date,
      end: event.end_date,
      color: event.is_holiday ? '#ff4d4d' : '#3788d8',
   }));

   const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      themeSystem: 'bootstrap5',
      initialDate: initialDate,
      headerToolbar: false,
      events: eventsData,
      eventContent: function(info) {
         // Membuat event sebagai titik kecil
         const dot = document.createElement('div');
         dot.style.width = '10px';
         dot.style.height = '10px';
         dot.style.backgroundColor = info.event.extendedProps.color;
         dot.style.borderRadius = '50%';
         dot.style.margin = '0';

         const eventWrapper = document.createElement('div');
         eventWrapper.appendChild(dot);
         return {
            domNodes: [eventWrapper]
         };
      },
      dateClick: function(info) {
         selectedDateSpan.textContent = info.dateStr;
         // reset value eventForm
         eventForm.reset();
         displayEventList(info.dateStr);
      },
   });

   calendar.render();

   function addEvent() {
      eventForm.reset();
      console.log(selectedDateSpan.textContent);
      eventForm.eventStartDate.value = selectedDateSpan.textContent;
      $('#eventModal').modal('show');
   }

   function addJson() {
      $('#eventJSONModal').modal('show');
   }

   function deleteEvent(id) {
      // buka modal swetealert
      Swal.fire({
         title: "Are you sure?",
         text: "You won't be able to revert this!",
         icon: "warning",
         showCancelButton: true,
         confirmButtonColor: "#3085d6",
         cancelButtonColor: "#d33",
         confirmButtonText: "Yes, delete it!"
      }).then((result) => {
         if (result.isConfirmed) {
            // buka arahkan ke halaamn delete
            deleteForm.id.value = id;
            deleteForm.submit();
         }
      });
   }

   function editEvent(id) {
      let data = dataEvents.filter(event => event.id === id);
      eventForm.title.value = data[0].title;
      eventForm.eventStartDate.value = data[0].start_date;
      eventForm.eventEndDate.value = data[0].end_date;
      eventForm.is_holiday.checked = data[0].is_holiday;
      // open modal
      $('#eventModal').modal('show');
   }

   function displayEventList(date) {
      const eventsOnDate = eventsData.filter(event => {
         const eventStart = new Date(event.start).toISOString().split('T')[0]; // Format YYYY-MM-DD
         return eventStart === date;
      });

      eventList.innerHTML = "";
      if (eventsOnDate.length === 0) {
         eventList.innerHTML = '<li class="list-group-item">Tidak ada event</li>';
      } else {
         eventsOnDate.forEach(event => {
            let li = document.createElement('li');
            li.className = "list-group-item d-flex justify-content-between align-items-center bg-body-secondary mb-2";

            li.innerHTML = `
               <div style="color:${event.color};">
                  <strong >${event.title}</strong> <br>
                  <small>${event.start} - ${event.end}</small>
               </div>
               <div class="dropdown">
                  <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                     <i class="fa-solid fa-ellipsis-vertical"></i>
                  </button>
                  <ul class="dropdown-menu">
                     <li><a class="dropdown-item text-warning" href="#" onclick="editEvent(${event.id})"><i class="fa-solid fa-pen"></i> Edit</a></li>
                     <li><a class="dropdown-item text-danger" href="#" onclick="deleteEvent(${event.id})"><i class="fa-solid fa-trash"></i> Hapus</a></li>
                  </ul>
               </div>
            `;
            eventList.appendChild(li);
         });
      }
   }
   // });
</script>
@endsection