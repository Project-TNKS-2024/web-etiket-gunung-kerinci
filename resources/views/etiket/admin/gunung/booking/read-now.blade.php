@extends('etiket.admin.template.index')

@section('css')

<style>
   table th {
      text-align: center !important;
      padding: 10px 26px 10px 10px !important;
      /* vertical-align: middle !important; */
   }

   #model-booking input {
      border: none;
      background: none;
   }

   #model-booking textarea {
      border: none;
      background: none;
   }

   #qrcode_kodebooking img {
      width: -webkit-fill-available !important;
   }

   .karcis-per-orang p {
      margin-bottom: 5px !important;
   }

   .qr_code_booking {
      padding: 15px;
      margin: 5px;
      border-radius: 10px;
      background-color: #ffffff;
   }

   .qr_code_booking img {
      width: -webkit-fill-available !important;
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
         <div class="card m-0 shadow-none">
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
      <div class="modal-content  gk-bg-primary100">
         <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Tiket</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body" id="model-booking">
            <div class="card">
               <div class="card-body">
                  <form id="inform-booking">
                     <div class="row">
                        <div class="col-md-4">
                           <div id="qrcode_kodebooking"></div>
                           <label for="ipt_kodebooking" class="w-100 text-center">Kode Booking</label>
                           <input type="text" name="ipt_kodebooking" id="ipt_kodebooking" class="form-control text-center fw-bold" readonly style="font-size: 18px;">
                        </div>
                        <div class="col-md-8">
                           <div class="row">

                              <div class="col-12">
                                 <span class="badge text-bg-primary" id="ipt_statusbooking" style="    margin-left: auto;display: block;width: min-content;"></span>
                              </div>
                              <div class="col-6">
                                 <label for="ipt_namaketua" class="fw-bold">Nama Ketua</label>
                                 <input type="text" name="ipt_namaketua" value="tes" id="ipt_namaketua" class="form-control" readonly>
                                 <label for="ipt_gerbangmasuk" class="fw-bold">Gerbang Masuk</label>
                                 <input type="text" name="ipt_gerbangmasuk" value="tes" id="ipt_gerbangmasuk" class="form-control" readonly>
                                 <label for="ipt_cekin" class="fw-bold">Cek In</label>
                                 <input type="text" name="ipt_cekin" value="tes" id="ipt_cekin" class="form-control" readonly>
                                 <label for="ipt_jumlahanggota" class="fw-bold">Jumlah Anggota</label>
                                 <input type="text" name="ipt_jumlahanggota" value="tes" id="ipt_jumlahanggota" class="form-control" readonly>
                              </div>
                              <div class="col-6">
                                 <label for="ipt_simaksi" class="fw-bold">Simaksi</label>
                                 <input type="text" name="ipt_simaksi" value="tes" id="ipt_simaksi" class="form-control" readonly>
                                 <label for="ipt_gerbangkeluar" class="fw-bold">Gerbang Keluar</label>
                                 <input type="text" name="ipt_gerbangkeluar" value="tes" id="ipt_gerbangkeluar" class="form-control" readonly>
                                 <label for="ipt_cekout" class="fw-bold">Cek Out</label>
                                 <input type="text" name="ipt_cekout" value="tes" id="ipt_cekout" class="form-control" readonly>
                                 <label for="ipt_kewarganegaraan" class="fw-bold">Jumlah Anggota</label>
                                 <div class="row">
                                    <div class="col-6">
                                       <input type="text" name="ipt_kewarganegaraanwni" value="tes" id="ipt_kewarganegaraanwni" class="form-control" readonly>
                                    </div>
                                    <div class="col-6">
                                       <input type="text" name="ipt_kewarganegaraanwna" value="tes" id="ipt_kewarganegaraanwna" class="form-control" readonly>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
            <div id="karcis_pengunjung">

            </div>
            <div class="card karcis-per-orang overflow-hidden bg-transparent d-non">
               <div class="row m-0">
                  <div class="col-8" style="background-color: rgba(106, 169, 152, 1); color: white;border-radius: 25px; padding: 20px;">
                     <p style="font-size: 18px; font-style: italic;">Nomor Seri : ${pendaki.id} /TNKS/NUS</p>
                     <p style="font-size: 18px;">Karcis Masuk Pengunjung</p>
                     <p style="font-size: 18px; font-weight: bold;">Pendakian Gunung Kerinci Melalui Pos 10</p>
                     <p style="font-size: 18px; font-weight: bold;">Nama Pendaki : ${pendaki.nama}</p>
                     <p style="font-size: 18px; font-weight: bold;">Berlaku untuk satu orang</p>
                     <p style="font-size: 24px; font-weight: bold;">Rp. ${pendaki.tagihan}</p>
                  </div>
                  <div class="col-4" style="background-color: rgba(255, 209, 51, 1);border-radius: 25px; padding: 20px;">
                     <div class="qr_code_booking"></div>
                  </div>
               </div>
            </div>

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
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
   // jalankan perintah clear console di console

   $(document).ready(function() {
      $('#myTable').DataTable({
         paging: false
      });
   });

   const informasiBooking = document.getElementById('inform-booking');

   function showDetailBooking(button) {
      // Ambil data booking dari atribut data-booking pada button
      const booking = button.getAttribute('data-booking');
      const bookingData = JSON.parse(booking);
      const iptqrcode = document.getElementById('qrcode_kodebooking');
      const iptstatusbooking = document.getElementById('ipt_statusbooking');

      // Isi struk
      informasiBooking.elements['ipt_kodebooking'].value = bookingData.unique_code;
      informasiBooking.elements['ipt_namaketua'].value = bookingData.pendakis[0].nama;
      informasiBooking.elements['ipt_gerbangmasuk'].value = bookingData.gate_masuk.nama;
      informasiBooking.elements['ipt_cekin'].value = bookingData.tanggal_masuk;
      informasiBooking.elements['ipt_jumlahanggota'].value = bookingData.pendakis.length + " Orang";
      if (bookingData.lampiran_simaksi) {
         informasiBooking.elements['ipt_simaksi'].value = 'Ada';
         informasiBooking.elements['ipt_simaksi'].style.color = 'green';
      } else {
         informasiBooking.elements['ipt_simaksi'].value = 'Tidak ada';
         informasiBooking.elements['ipt_simaksi'].style.color = 'red';
      }
      informasiBooking.elements['ipt_gerbangkeluar'].value = bookingData.gate_keluar.nama;
      informasiBooking.elements['ipt_cekout'].value = bookingData.tanggal_keluar;
      informasiBooking.elements['ipt_kewarganegaraanwni'].value = bookingData.total_pendaki_wni + " WNI";
      informasiBooking.elements['ipt_kewarganegaraanwna'].value = bookingData.total_pendaki_wna + " WNA";
      iptstatusbooking.innerText = bookingData.status_booking;


      // buat qr
      iptqrcode.innerText = "";
      new QRCode(iptqrcode, bookingData.unique_code);

      // ambil template karcis dan isi dengan karcis pengunjung

      const templateKarcis = document.getElementById('karcis_pengunjung');
      templateKarcis.innerHTML = ''; // Clear previous content
      bookingData.pendakis.forEach(pendaki => {
         const karcisHtml = BuatKarcisPengunjung(pendaki);
         templateKarcis.innerHTML += karcisHtml;
      });

      // buat qr untuk karcis pengunjung
      const qrcodependaki = document.querySelectorAll('.qr_code_booking');
      qrcodependaki.forEach(qr => {
         new QRCode(qr, bookingData.unique_code);
      });



      console.log(bookingData);
      console.log(informasiBooking);
   }

   function BuatKarcisPengunjung(pendaki) {
      let karcis = `
        <div class="card karcis-per-orang overflow-hidden bg-transparent">
               <div class="row m-0">
                  <div class="col-8" style="background-color: rgba(106, 169, 152, 1); color: white;border-radius: 25px; padding: 20px;">
                     <p style="font-size: 18px; font-style: italic;">Nomor Seri : ${pendaki.id} /TNKS/NUS</p>
                     <p style="font-size: 18px;">Karcis Masuk Pengunjung</p>
                     <p style="font-size: 18px; font-weight: bold;">Pendakian Gunung Kerinci Melalui Pos 10</p>
                     <p style="font-size: 18px; font-weight: bold;">Nama Pendaki : ${pendaki.nama}</p>
                     <p style="font-size: 18px; font-weight: bold;">Berlaku untuk satu orang</p>
                     <p style="font-size: 24px; font-weight: bold;">Rp. ${pendaki.tagihan}</p>
                  </div>
                  <div class="col-4" style="background-color: rgba(255, 209, 51, 1);border-radius: 25px; padding: 20px;">
                     <div class="qr_code_booking"></div>
                  </div>
               </div>
            </div>
    `;

      return karcis;
   }
</script>

@endsection