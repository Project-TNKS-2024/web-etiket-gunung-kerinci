@extends('etiket.admin.template.index')

@section('css')
<style>
   .fc .fc-daygrid-event-harness {
      width: 15px;
      display: inline-block;
   }

   .fc-day a {
      color: black;
   }

   .fc-day.cal-weekend {
      background-color: rgba(255, 0, 0, 0.1);
   }

   .fc-day.cal-weekend a {
      color: red;
   }

   .fc-day:has(.holiday-dot) {
      background-color: rgba(255, 0, 0, 0.1);
   }

   .fc-day:has(.holiday-dot) a {
      color: red;
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
      <h3><b>Kalender</b></h3>
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
         <div class="col-12 col-lg-6 col-md-8">
            <!-- <div class="row">
               <div class="col-12 col-lg-6">
                  <div id="calendar1"></div>
               </div>
               <div class="col-12 col-lg-6">
                  <div id="calendar2"></div>
               </div>
            </div> -->
            <div id="calendar1"></div>
            <br>
            <div id="calendar2"></div>
         </div>
         <div class="col-12 col-lg-6 col-md-4 d-flex flex-column">
            <h5>Tanggal: <span id="selected-date">-</span></h5>
            <ul class="list-group mb-2" id="event-list">
               <li class="list-group-item bg-body-secondary">Pilih tanggal untuk melihat event.</li>
            </ul>
            <!-- Tombol "Tambah Event" sejajar dengan kalender -->
            <div class="d-flex justify-content-between mt-auto ">
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
   const calendarEl1 = document.getElementById('calendar1');
   const calendarEl2 = document.getElementById('calendar2');
   const eventList = document.getElementById('event-list');
   const selectedDateSpan = document.getElementById('selected-date');

   const eventForm = document.getElementById('eventForm');
   const deleteForm = document.getElementById('formDelete');

   const dataEvents = JSON.parse('@json($events)');
   const dataBulan = JSON.parse('@json($bulan)');

   // Pecah bulan & tahun dari format "m-Y"
   let [month, year] = dataBulan.split('-');
   month = parseInt(month, 10);
   year = parseInt(year, 10);
   const initialDate1 = `${year}-${String(month).padStart(2, '0')}-01`;

   // Hitung bulan selanjutnya
   let nextMonth = month + 1;
   let nextYear = year;
   if (nextMonth > 12) {
      nextMonth = 1;
      nextYear += 1;
   }
   const initialDate2 = `${nextYear}-${String(nextMonth).padStart(2, '0')}-01`;


   // Format event agar sesuai dengan FullCalendar
   let eventsData = dataEvents.map(event => ({
      id: event.id,
      title: event.title,
      start: event.start_date,
      end: event.end_date,
      is_holiday: event.is_holiday,
      color: event.is_holiday ? '#ff4d4d' : '#3788d8',
   }));


   function createCalendar(calendarEl, initialDate) {
      return new FullCalendar.Calendar(calendarEl, {
         initialView: 'dayGridMonth',
         aspectRatio: 1.5,
         height: 'auto',
         locale: 'id',
         themeSystem: 'bootstrap5',
         initialDate: initialDate,
         headerToolbar: {
            left: 'title',
            center: false,
            right: false
         },
         events: eventsData,
         eventContent: function(info) {
            const dot = document.createElement('div');
            dot.style.width = '10px';
            dot.style.height = '10px';
            if (info.event.extendedProps.is_holiday) {
               dot.classList.add('holiday-dot');
            }
            dot.style.backgroundColor = info.event.extendedProps.color;
            return {
               domNodes: [dot]
            };
         },
         dayCellDidMount: function(info) {
            const day = info.date.getDay();

            if (day === 0 || day === 6) {
               info.el.classList.add('cal-weekend');
            }
         },
         dateClick: function(info) {
            selectedDateSpan.textContent = info.dateStr;
            eventForm.reset();
            displayEventList(info.dateStr);
         },
      });
   }

   // Render kedua kalender
   const calendar1 = createCalendar(calendarEl1, initialDate1);
   const calendar2 = createCalendar(calendarEl2, initialDate2);

   calendar1.render();
   calendar2.render();

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