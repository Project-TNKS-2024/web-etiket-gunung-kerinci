@extends('etiket.admin.template.index')

@section('css')

<style>
   table th {
      text-align: center !important;
      padding: 10px 26px 10px 10px !important;
      /* vertical-align: middle !important; */
   }

   #inform-booking input {
      border: none;
   }

   #inform-booking textarea {
      border: none;
   }
</style>
<link rel="stylesheet" href="/DataTables/datatables.css" />

@endsection

@section('main')

<div style="min-height: 80vh;">
   <!-- title -->
   <h3 class="font-bold mb-3 gk-text-base-black">Kelola Booking</h3>
   <p>Booking 2 Bulan Terakhir</p>

   <!-- tombol tambah -->

   <div style="overflow: visible;">
      <div class="col-12 p-0 shadow rounded bg-white" style="overflow:auto;">
         <div class="card m-0">
            <div class="card-body">
               <table id="myTable" class="display table table-hover table-bordered">
                  <thead class="table-primary text-nowrap">
                     <tr>
                        <th rowspan="2">ID</th>
                        <th rowspan="2">User Email</th>
                        <th rowspan="2">Tiket Destinasi</th>
                        <th rowspan="1" colspan="2">Tanggal</th>
                        <th rowspan="1" colspan="2">Pendaki</th>
                        <th rowspan="1" colspan="2">Gate</th>
                        <th rowspan="2" class="text-wrap">Status Booking</th>
                        <th rowspan="2"> Pendaki</th>
                        <th rowspan="2">Detail</th>
                     </tr>
                     <tr>
                        <!-- tanggal -->
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <!-- pendaki -->
                        <th>WNI</th>
                        <th>WNA</th>
                        <!-- gate -->
                        <th>Masuk</th>
                        <th>Keluar</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($bookings as $booking)
                     <tr>
                        <td>{{ $booking->id }}</td>
                        <td class="text-nowrap">{{ $booking->user->email }}</td>
                        <td>{{ $booking->gktiket->destinasi->nama }}</td>
                        <td class="text-nowrap">{{ $booking->tanggal_masuk }}</td>
                        <td class="text-nowrap">{{ $booking->tanggal_keluar }}</td>
                        <td>{{ $booking->total_pendaki_wni }}</td>
                        <td>{{ $booking->total_pendaki_wna }}</td>
                        <td class="text-nowrap">{{ $booking->gateMasuk->nama }}</td>
                        <td class="text-nowrap">{{ $booking->gateKeluar->nama }}</td>
                        <td>
                           <span class="badge text-bg-primary">{{ $booking->status_booking }}</span>

                        </td>
                        <td class="text-nowrap">
                           @foreach ($booking->pendakis as $pendaki)
                           <li>{{$pendaki->nama}}</li>
                           @endforeach
                        </td>
                        <td>
                           <button type="button" style="width: 30px; height:30px" class="btn btn-info rounded-pill p-1" data-bs-toggle="modal" data-booking="{{$booking}}" data-bs-target="#modalDetailBoooking" onclick="showDetailBooking(this)">
                              <i class="fa-solid fa-info" style="color: #ffffff;"></i>
                              <!-- <img src="{{ asset('assets/icon/tnks-detail.svg') }}" alt=""> -->
                           </button>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="modalDetailBoooking" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Tiket</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form id="inform-booking">
               <input type="text" name="" value="qwkjf qkwef iqe wfv we jfv hiwe jfv we dvjn weif " readonly>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-warning">Edit</button>
         </div>
      </div>
   </div>
</div>

@endsection

@section('js')

<script src="/DataTables/datatables.js"></script>
<script>
   $(document).ready(function() {
      $('#myTable').DataTable({
         paging: false
      });
   });

   function showDetailBooking(button) {
      // Ambil data booking dari atribut data-booking pada button
      const booking = button.getAttribute('data-booking');
      const bookingData = JSON.parse(booking);

      // form infrormasi booking
      const informasiBooking = document.getElementById('inform-booking');

      console.log(bookingData);
      console.log(informasiBooking);
   }
</script>

@endsection