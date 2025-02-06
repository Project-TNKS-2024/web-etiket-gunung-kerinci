<!DOCTYPE html>
<html lang="id">

<head>
   <title>{{ config('app.name', 'Laravel') }} - Bukti Pembelian</title>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">
   <style>
      .invoice-container {
         margin: 15px auto;
         padding: 70px;
         max-width: 850px;
         background-color: #fff;
         border: 1px solid #ccc;
         border-radius: 6px;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }

      .logo-text {
         font-weight: bold;
         font-size: 30px;
      }
   </style>
</head>

<body>
   <div class="container-fluid invoice-container" id="invoice">
      <div class="row">
         <div class="col-sm-9">
            <p>
               <img src="{{asset('assets/img/logo/logo bulat.png')}}" title="tnks" width="80">
               <span class="logo-text">Taman Nasional Kerinci Seblat</span>
            </p>
            <p class="fs-5">Booking <span class="fs-6">#{{$data->id}}</p>
            </h3>
         </div>
         <div class="col-sm-3 text-end">
            <div class="invoice-status">
               @if ($data->status_pembayaran)
               <span class="badge bg-success px-3 py-2">Sudah bayar</span>
               @else
               <span class="badge bg-warning px-3 py-2">Belum bayar</span>
               @endif
            </div>
         </div>
      </div>
      <hr>
      <div class="row">
         <div class="col-sm-6 ">
            <strong>Bayar Ke:</strong>
            <address>
               Balai Besar Taman Nasional Kerinci Seblat<br>
               Jambi, Indonesia<br>
               NPWP: 031.783.862.1-086.000
            </address>
         </div>
         <div class="col-sm-6 text-end">
            <strong>Ditagih ke:</strong>
            <address>
               {{$data->user->biodata->fullName ?? 'N/A'}}<br>
               {{$data->user->email ?? 'N/A'}}<br>
               {{$data->user->biodata->no_hp ?? 'N/A'}}<br>
            </address>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-6">
            <strong>Metode Pembayaran:</strong><br>
            @if ($data->status_pembayaran)
            <span>Midtrans (Bank Transfer / QRIS / Credit Card)</span>
            @else
            <span>-</span>
            @endif
         </div>
         <div class="col-sm-6 text-end">
            <strong>Tanggal Invoice:</strong><br>
            @if ($data->status_pembayaran)
            <span>{{$data->pembayaran[0]->updated_at ?? '-'}}</span>
            @else
            <span>-</span>
            @endif
         </div>
      </div>
      <br>
      <div>
         <div>
            <strong>Item Invoice</strong>
         </div>
         <div class="card p-1">
            <table class="table table-condensed">
               <thead>
                  <tr>
                     <td><strong>Deskripsi</strong></td>
                     <td><strong>Harga</strong></td>
                     <td><strong>Qty</strong></td>
                     <td class="text-center"><strong>Jumlah</strong></td>
                  </tr>
               </thead>
               @php
               $totalHarga = 0;
               @endphp
               <tbody>
                  @if($data->wniwna->wni > 0)
                  @php
                  $tiket = collect($data->gktiket->tiket_pendaki)
                  ->first(fn($item) => $item->kategori_pendaki === 'wni');
                  @endphp
                  <tr>
                     <td>
                        <b>Tiket Kategori {{$data->gktiket->nama}} WNI</b>
                        <br>
                        Tiket masuk ({{$tiket->harga_masuk_wd ?? '0'}}) x {{$data->wkwd->weekdays ?? '0'}} +
                        @if ($data->wkwd->weekends > 0)
                        Tiket masuk weekend ({{$tiket->harga_masuk_wk ?? '0'}}) x {{$data->wkwd->weekends ?? '0'}} +
                        @endif
                        Tiket kemah ({{$tiket->harga_kemah ?? '0'}}) x {{$data->total_hari -1}} +
                        Tiket pendakian ({{$tiket->harga_traking ?? '0'}}) +
                        Asuransi ({{$tiket->harga_ansuransi ?? '0'}})
                     </td>
                     <td class="text-end text-nowrap">
                        Rp. {{ number_format(($tiket->harga_masuk_wd ?? 0) * ($data->wkwd->weekdays ?? 0) + 
                        ($tiket->harga_masuk_wk ?? 0) * ($data->wkwd->weekends ?? 0) + 
                        ($tiket->harga_kemah ?? 0) * ($data->total_hari -1) + 
                        ($tiket->harga_traking ?? 0) + 
                        ($tiket->harga_ansuransi ?? 0), 0, ',', '.') }}
                     </td>
                     <td>
                        {{$data->wniwna->wni ?? 0}}
                     </td>
                     <td class="text-end text-nowrap">
                        @php
                        $harga = (($tiket->harga_masuk_wd ?? 0) * ($data->wkwd->weekdays ?? 0) +
                        ($tiket->harga_masuk_wk ?? 0) * ($data->wkwd->weekends ?? 0) +
                        ($tiket->harga_kemah ?? 0) * ($data->total_hari -1) +
                        ($tiket->harga_traking ?? 0) +
                        ($tiket->harga_ansuransi ?? 0)) * ($data->wniwna->wni ?? 0);
                        $totalHarga += $harga;
                        @endphp
                        Rp. {{ number_format($harga, 0, ',', '.') }}
                     </td>
                  </tr>
                  @endif

                  @if($data->wniwna->wna > 0)
                  @php
                  $tiket = collect($data->gktiket->tiket_pendaki)
                  ->first(fn($item) => $item->kategori_pendaki === 'wna');
                  @endphp
                  <tr>
                     <td>
                        <b>Tiket Kategori {{$data->gktiket->nama}} WNI</b>
                        <br>
                        Tiket masuk ({{$tiket->harga_masuk_wd ?? '0'}}) x {{$data->wkwd->weekdays ?? '0'}} +
                        @if ($data->wkwd->weekends > 0)
                        Tiket masuk weekend ({{$tiket->harga_masuk_wk ?? '0'}}) x {{$data->wkwd->weekends ?? '0'}} +
                        @endif
                        Tiket kemah ({{$tiket->harga_kemah ?? '0'}}) x {{$data->total_hari -1}} +
                        Tiket pendakian ({{$tiket->harga_traking ?? '0'}}) +
                        Asuransi ({{$tiket->harga_ansuransi ?? '0'}})
                     </td>
                     <td class="text-end text-nowrap">
                        Rp. {{ number_format(($tiket->harga_masuk_wd ?? 0) * ($data->wkwd->weekdays ?? 0) + 
                        ($tiket->harga_masuk_wk ?? 0) * ($data->wkwd->weekends ?? 0) + 
                        ($tiket->harga_kemah ?? 0) * ($data->total_hari -1) + 
                        ($tiket->harga_traking ?? 0) + 
                        ($tiket->harga_ansuransi ?? 0), 0, ',', '.') }}
                     </td>
                     <td>
                        {{$data->wniwna->wni ?? 0}}
                     </td>
                     <td class="text-end text-nowrap">
                        @php
                        $harga = (($tiket->harga_masuk_wd ?? 0) * ($data->wkwd->weekdays ?? 0) +
                        ($tiket->harga_masuk_wk ?? 0) * ($data->wkwd->weekends ?? 0) +
                        ($tiket->harga_kemah ?? 0) * ($data->total_hari -1) +
                        ($tiket->harga_traking ?? 0) +
                        ($tiket->harga_ansuransi ?? 0)) * ($data->wniwna->wna ?? 0);
                        $totalHarga += $harga;
                        @endphp
                        Rp. {{ number_format($harga, 0, ',', '.') }}
                     </td>
                  </tr>
                  @endif
                  <tr>
                     <td class="total-row text-right" colspan="3"><strong>Total</strong></td>
                     <td class="total-row text-center">
                        Rp. {{ number_format($totalHarga ?? 0, 0, ',', '.') }}
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
      <div>
         <strong>Laman Bukti Pembelian</strong><br>
         <a href="{{ url()->current() }}" target="_blank">{{url()->current()}}</a>
      </div>
   </div>
   <div class=" text-center">
      <a href="{{ route('homepage.booking.payment', ['id' => $data->id]) }}" class="btn btn-outline-secondary"><i class="fa fa-print"></i>Laman Pembalian</a>
      <a href="javascript:void(0)" onclick="invoicePrint()" class="btn btn-outline-secondary"><i class="fa fa-print"></i> Print</a>
      <a href="javascript:void(0)" onclick="invoiceDownload()" class="btn btn-outline-secondary"><i class="fa fa-download"></i> Download</a>
   </div>
   <script>
      function invoicePrint() {
         window.print();
      }

      function invoiceDownload() {

      }
   </script>
</body>

</html>