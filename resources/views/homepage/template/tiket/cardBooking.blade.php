<style>
   /* Set fixed dimensions for the card */
   .fixed-card {
      max-width: 786px;
      min-width: 786px;
      width: 100%;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      background-color: #fff;
   }

   /* Style the table */
   .table {
      font-size: 0.9rem;
   }

   .qrcode_kodebooking img {
      width: -webkit-fill-available !important;
   }


   .table tbody tr td {
      padding: 3px 5px;
   }

   .table {
      --bs-table-bg: transparent;
   }

   hr {
      border: none;
      border-top: 2px dashed #555;
      /* Warna dan tipe garis */
      margin: 20px 0;
      /* Jarak atas dan bawah */
      width: 100%;
      /* Lebar penuh */
   }

   /* Responsiveness */
   @media (max-width: 786px) {
      .card-body {
         overflow-x: auto;
         white-space: nowrap;
      }

      .table {
         display: inline-block;
         width: auto;
      }
   }


   /* Ensure print layout */
   @media print {
      .fixed-card {
         width: 100%;
         box-shadow: none;
         border: none;
      }

      body {
         margin: 0;
         padding: 0;
         background: none;
      }

      table {
         width: 100%;
      }
   }
</style>

<div class="card fixed-card p-3">
   <div class="card-body">
      <div class="row">
         <div class="col-8">
            <table class="table table-borderless align-middle">
               <tr class="fw-bold">
                  <td>Nama Ketua</td>
                  <td>Destinasi</td>
               </tr>
               <tr>
                  <td>{{ $booking->pendakis[0]->biodata->first_name .' '. $booking->pendakis[0]->biodata->last_name }}</td>
                  <td>
                     {{$booking->destinasi->nama}}
                  </td>
               </tr>
               <tr class="fw-bold">
                  <td>Gerbang Masuk</td>
                  <td>Gerbang Keluar</td>
               </tr>
               <tr>
                  <td>{{ $booking->gateMasuk->nama }}</td>
                  <td>{{ $booking->gateKeluar->nama }}</td>
               </tr>
               <tr class="fw-bold">
                  <td>Tanggal Masuk</td>
                  <td>Tanggal Keluar</td>
               </tr>
               <tr>
                  <td>{{ $booking->tanggal_masuk }}</td>
                  <td>{{ $booking->tanggal_keluar }}</td>
               </tr>
               <tr class="fw-bold">
                  <td>Jumlah Anggota</td>
                  <td>Kewarganegaraan</td>
               </tr>
               <tr>
                  <td>{{ $booking->total_pendaki_wni + $booking->total_pendaki_wna }} orang</td>
                  <td>
                     <div class="row">
                        <div class="col">
                           <p>{{ $booking->total_pendaki_wni }} WNI</p>
                        </div>
                        <div class="col">
                           <p>{{ $booking->total_pendaki_wna }} WNA</p>
                        </div>
                     </div>
                  </td>
               </tr>
            </table>
         </div>
         <div class="col-4 text-center card-qrboooking">
            <div class="qrcode_kodebooking" data-qr='{{$booking->unique_code}}'></div>
            <label for="ipt_kodebooking" class="w-100">Kode Booking</label>
            <input type="text" name="ipt_kodebooking" value="{{ $booking->unique_code }}" id="ipt_kodebooking"
               class="form-control text-center fw-bold border-0" readonly>
         </div>
      </div>
   </div>
</div>

@foreach($booking->pendakis as $pendaki)
<hr>
<div class="card fixed-card overflow-hidden ">
   <div class="row g-0"> <!-- Kolom Kiri -->
      <div class="col-9 p-3" style="background-color: rgba(106, 169, 152, 1); color: white;">
         <h5 class="fw-bold mb-3">Karcis Masuk Pengunjung</h5>
         <table class="table table-borderless">
            <tr>
               <td>No Booking</td>
               <td> : </td>
               <td>{{ $booking->unique_code }}</td>
            </tr>
            <tr>
               <td>Pendakian</td>
               <td> : </td>
               <td>Pendakian {{$booking->gateMasuk->destinasi->nama}} Jalur {{$booking->gateMasuk->nama}}</td>
            </tr>
            <tr>
               <td>Nama</td>
               <td> : </td>
               <td>{{ $pendaki->biodata->first_name . ' '.$pendaki->biodata->last_name }}</td>
            </tr>
            <tr>
               <td>Tanggal Masuk</td>
               <td> : </td>
               <td>{{ $booking->tanggal_masuk }}</td>
            </tr>
            <tr>
               <td>Tanggal Keluar</td>
               <td> : </td>
               <td>{{ $booking->tanggal_keluar }}</td>
            </tr>
            <tr>
               <td>Harga</td>
               <td> : </td>
               <td>Rp. {{ number_format($pendaki->tagihan, 0, ',', '.') }}</td>
            </tr>
         </table>

         <!-- catatan kaki -->
         <p class="mb-0">Karcis ini hanya berlaku untuk satu orang</p>
      </div> <!-- Kolom Kanan -->
      <div class="col-3 p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(255, 209, 51, 1);">
         <div class="text-center bg-white p-3 rounded">
            <div class="qrcode_kodebooking" data-qr="{{$booking->unique_code}}" style="width: 150px;"></div>
            <p class="text-center fw-bold mb-0 mt-2">Scan Disini</p>
         </div>
      </div>
   </div>
</div>

@endforeach