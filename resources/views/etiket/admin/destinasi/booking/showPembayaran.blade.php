@extends('etiket.admin.template.index')

@section('css')

<style>
   .table-des tbody tr td {
      padding: 3px 5px;
   }
</style>

@endsection

@section('main')

<div class="card">
   <div class="card-header">
      <div class="d-flex align-items-center justify-content-between">
         <h5><b>Pembayaran</b></h5>
         @if ($booking->pembayaran->isEmpty())
         <span class="badge text-bg-info">Belum ada Pembayaran</span>
         @else

         @if ($booking->pembayaran->last()->status == 'success')
         <span class="badge text-bg-success">Success</span>
         @elseif ($booking->pembayaran->last()->status == 'failed')
         <span class="badge text-bg-danger">Failed</span>
         @else
         <span class="badge text-bg-warning">Pending</span>
         @endif

         @endif

      </div>
   </div>
   <div class="card-body">
      <div class="row">
         <div class="col-md-6">
            <div class="card shadow mb-2">
               <div class="card-body">
                  <h1 class="fs-5 fw-bold text-center">Tagihan</h1>
                  <h1 class="fs-4 fw-bold">Tagihan Ke:</h1>
                  <p>Email: {{ $booking->user->email }}</p>

                  <h1 class="fs-4 fw-bold">Deskripsi Pendakian</h1>
                  <table class="table table-borderless table-des">
                     <tbody>
                        <tr>
                           <td>Nama Ketua</td>
                           <td>:</td>
                           <td>{{$booking->pendakis[0]->first_name}} {{$booking->pendakis[0]->last_name}}</td>
                        </tr>
                        <tr>
                           <td>Gate Masuk</td>
                           <td>:</td>
                           <td>{{$booking->gateMasuk->nama }}</td>
                        </tr>
                        <tr>
                           <td>Gate Keluar</td>
                           <td>:</td>
                           <td>{{$booking->gateKeluar->nama }}</td>
                        </tr>
                        <tr>
                           <td>Tanggal Pendakian</td>
                           <td>:</td>
                           <td>{{$booking->tanggal_masuk }}</td>
                        </tr>
                        <tr>
                           <td>Tanggal Keluar</td>
                           <td>:</td>
                           <td>{{$booking->tanggal_keluar }}</td>
                        </tr>
                        <tr>
                           <td>Total Pendaki</td>
                           <td>:</td>
                           <td>{{$booking->total_pendaki_wni }} WNI dan {{$booking->total_pendaki_wna }} WNA</td>
                        </tr>
                     </tbody>
                  </table>

                  <h1 class="fs-4 fw-bold">List Tagihan Tiket</h1>
                  <table class="table table-des bg-transparent">
                     <tbody>
                        <tr class="fw-semibold">
                           <td>Nama Pendaki</td>
                           <td class="text-end">Tagihan</td>
                        </tr>
                        @foreach ($booking->pendakis as $pendaki)
                        <tr>
                           <td>{{$pendaki->firt_name}} {{$pendaki->last_name}}</td>
                           <td class="text-end">Rp. {{$pendaki->tagihan}}</td>
                        </tr>
                        @endforeach
                        <tr>
                           <td class="fw-semibold text-end">Total:</td>
                           <td class="text-end">Rp. {{$booking->total_pembayaran}}</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
            <a href="{{route('admin.destinasi.booking.struk.show', ['id' => $booking->id])}}" class="btn btn-primary">Cek Struk Pembelian</a>
         </div>

         <div class="col-md-6">
            <h1 class="fs-5 fw-bold text-center">Bukti Pembayaran</h1>
            @foreach ($booking->pembayaran as $index => $pembayaran)
            <div class="card shadow">
               <div class="card-header">
                  <div class="d-flex align-items-center justify-content-between">
                     <p class="mb-0"><strong>Bukti {{$index+1}}</strong> </p>
                     <a href="{{ asset($pembayaran->bukti_pembayaran)}}" class=" bg-transparent" target="_blank"> <i class="fa-regular fa-eye"></i></a>
                  </div>
               </div>
               <div class="card-body">
                  <embed src="{{ asset($pembayaran->bukti_pembayaran)}}" type="application/pdf" width="100%" height="280px">
                  <div class="mt-2">
                     <p class="mb-0"><b>Metode Pembayaran : </b> {{$pembayaran->payment_method}}</p>
                     <p class="mb-0"><b>Keterangan : </b> {{$pembayaran->keterangan}}</p>
                  </div>
               </div>
            </div>
            @endforeach
         </div>
      </div>
   </div>
   <div class="card-footer">
      <form method="post" id="btnVerify" class="d-flex align-items-center w-100" action="{{ route('admin.destinasi.booking.payment.update') }}">
         @csrf
         <input type="text" name="keterangan" class="form-control me-3" placeholder="Masukkan keterangan" required>
         <input type="hidden" name="id_booking" id="updateIdBooking" value="{{$booking->id}}">
         <button type="submit" name="verified" class="btn btn-primary" value="yes">Setujui</button>
         <button type="submit" name="verified" class="btn btn-warning ms-1" value="no">Tolak</button>
      </form>
   </div>
</div>


@endsection

@section('js')

@endsection