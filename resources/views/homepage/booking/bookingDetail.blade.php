@extends('homepage.template.index')


@section('css')
<style>
   .table tbody tr td {
      padding: 3px 5px;
   }

   .centered {
      display: flex;
      justify-content: center;
   }
</style>
@endsection


@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Detail Booking',
])

<div class="container my-5">
   @include('homepage.booking.booking-nav', ['step' => 2])

   <div class="card border-0 shadow">
      <div class="card-body px-4 px-md-5 pb-4">
         <div class="mt-3">
            <h1 class="fs-5 fw-bold">Booking {{$booking->gateMasuk->destinasi->nama}}</h1>
            <div class="row">
               <div class="col-12 col-md-6">
                  <table class="table table-borderless ">
                     <tr>
                        <td>Nama Ketua</td>
                        <td> : </td>
                        <td>{{$formulirPendakis[0]->first_name . ' ' . $formulirPendakis[0]->last_name}}</td>
                     </tr>
                     <tr>
                        <td>Gerbang Masuk</td>
                        <td> : </td>
                        <td>{{$booking->gateMasuk->nama}}</td>
                     </tr>
                     <tr>
                        <td>Gerbang Keluar</td>
                        <td> : </td>
                        <td>{{$booking->gateKeluar->nama}}</td>
                     </tr>
                     <tr>
                        <td>Check In</td>
                        <td> : </td>
                        <td>{{ \Carbon\Carbon::parse($booking->tanggal_masuk)->isoFormat('D MMMM Y') }}</td>
                     </tr>
                     <tr>
                        <td>Check Out</td>
                        <td> : </td>
                        <td>{{ \Carbon\Carbon::parse($booking->tanggal_masuk)->isoFormat('D MMMM Y') }}</td>
                     </tr>
                     <tr>
                        <td>Jumlah Pendaki</td>
                        <td> : </td>
                        <td>{{$booking->total_pendaki_wni}} WNI dan {{$booking->total_pendaki_wna}} WNA</td>
                     </tr>
                  </table>
               </div>
            </div>
         </div>
         <hr>
         @foreach ($formulirPendakis as $key => $pendaki)
         <div class=" mt-4">
            <h1 class="fs-5 fw-bold">
               @if ($key === 0)
               Biodata Ketua
               @else
               Biodata Pendaki {{ $key }}
               @endif
            </h1>
            <div class="row">
               <div class="col-12 col-lg-6">
                  <table class="table table-borderless">
                     <tr>
                        <td>Nama</td>
                        <td> : </td>
                        <td>{{$pendaki->first_name . ' ' . $pendaki->last_name}}</td>
                     </tr>
                     <tr>
                        <td>Kewarganegaraan</td>
                        <td> : </td>
                        <td>
                           @if($pendaki->kategori_pendaki == 'wni')
                           Warga Negara Indonesia (WNI)
                           @else
                           Warga Negara Asing (WNA)
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <td>No KTP/Pasport</td>
                        <td> : </td>
                        <td>{{$pendaki->biodata->nik}}</td>
                     </tr>
                     <tr>
                        <td>No Telepon</td>
                        <td> : </td>
                        <td>{{$pendaki->biodata->no_hp}}</td>
                     </tr>
                     <tr>
                        <td>No Telepon Darurat</td>
                        <td> : </td>
                        <td>{{$pendaki->biodata->no_hp_darurat}}</td>
                     </tr>
                     <tr>
                        <td>Tanggal Lahir</td>
                        <td> : </td>
                        <td>{{ \Carbon\Carbon::parse($pendaki->tanggal_lahir)->isoFormat('D MMMM Y') }}</td>
                     </tr>
                     <tr>
                        <td>Usia</td>
                        <td> : </td>
                        <td>{{$pendaki->usia}}</td>
                     </tr>
                  </table>
               </div>
               <div class="col-12 col-lg-6">
                  @if ($pendaki->lampiran_surat_izin_ortu)
                  <div class="border rounded p-2" style="max-height: 300px; overflow: hidden;">
                     @php
                     $extension = pathinfo($pendaki->lampiran_surat_izin_ortu, PATHINFO_EXTENSION);
                     @endphp

                     @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                     <img src="{{asset($pendaki->lampiran_surat_izin_ortu)}}" alt="Lampiran Identitas" class="img-fluid" style="max-height: 280px; width: auto; display: block; margin: 0 auto;">
                     @else
                     <embed src="{{asset($pendaki->lampiran_surat_izin_ortu)}}" type="application/pdf" width="100%" height="280px">
                     @endif
                  </div>
                  @endif

               </div>
            </div>
         </div>
         @endforeach
         <hr>
         <div class="mt-3">
            <h1 class="fs-5 fw-bold">Barang Bawaan Wajib</h1>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" name="barangWajib[perlengkapan_gunung_standar]" value="1" checked readonly>
               <label class="form-check-label" for="perle_gunung">
                  Perlengkapan Standar Pendaki Gunung
               </label>
            </div>

            <div class="form-check">
               <input class="form-check-input" type="checkbox" name="barangWajib[trash_bag]" value="1" id="trash_bag" checked readonly>
               <label class="form-check-label" for="trash_bag">
                  Trash Bag
               </label>
            </div>

            <div class="form-check">
               <input class="form-check-input" type="checkbox" name="barangWajib[p3k_standart]" value="1" id="p3k_standart" checked readonly>
               <label class="form-check-label" for="p3k_standart">
                  P3K Standart
               </label>
            </div>

            <div class="form-check">
               <input class="form-check-input" type="checkbox" name="barangWajib[survival_kit_standart]" value="1" id="survival_kit_standart" checked readonly>
               <label class="form-check-label" for="survival_kit_standart">
                  Survival Kit Standart
               </label>
            </div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-12 col-md-4">
         <a type="submit" class="btn btn-primary w-100 fw-bold mt-3" href="{{route('homepage.booking.cancel', ['id' => $booking->id])}}">Kembali</a>
      </div>
      <div class="col-12 col-md-4"></div>
      <div class="col-12 col-md-4 text-end">
         <a type="submit" class="btn btn-primary w-100 fw-bold mt-3" href="{{route('homepage.booking.payment', ['id' => $booking->id])}}">Selanjutnya</a>
      </div>
   </div>
</div>
@endsection
@section('js')



@endsection