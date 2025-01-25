@extends('etiket.admin.template.index')

@section('css')

<style>
   #reader__scan_region video {
      width: -webkit-fill-available !important;
   }

   #html5-qrcode-select-camera {
      width: -webkit-fill-available !important;
   }
</style>
@endsection

@section('main')
<div class="row">
   <div class="col-12 col-sm-6 col-md-8">
      <div class="card">
         <div class="card-header">
            <h3 class="card-title w-100">Reader Scan</h3>
            <a onclick=" html5QrcodeScanner.render(onScanSuccess, onScanFailure)"><i class="fa fa-rotate-right" style="font-size: 18px;"></i></a>
         </div>
         <div class="card-body text-center">
            <div class="container">
               <div id="reader" width="600px"></div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-12 col-sm-6 col-md-4">
      <div class="d-none">
         <a href="{{route('homepage.booking.tiket', ['id' => 1])}}" class="d-flex align-items-center gap-2 w-100 btn btn-warning  d-block mx-auto p-1 px-3 rounded shadow " style="cursor: pointer;">
            <img src="{{asset('assets/icon/tnks/search_alt-dark.svg')}}" /> Detail
         </a>
      </div>
      <div id="list-tiket">

      </div>
   </div>
</div>

@endsection

@section('js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
   const listtiket = document.querySelector('#list-tiket');
   let listcodetiket = [];

   function onScanSuccess(decodedText, decodedResult) {
      console.log(decodedText);
      // cek decodedText ada atau tidak di dalam listcodetiket
      if (!listcodetiket.includes(decodedText)) {
         listcodetiket.push(decodedText);
         console.log('Kode berhasil ditambahkan');
         // Buat elemen anchor baru
         const ticketLink = document.createElement('a');
         ticketLink.href = `{{route('admin.fitur.detailTiket', ['uq' => ':id'])}}`.replace(':id', decodedText);
         ticketLink.className = 'd-flex align-items-center gap-2 w-100 btn btn-warning d-block mx-auto p-1 px-3 rounded shadow';
         ticketLink.style.cursor = 'pointer';
         ticketLink.innerHTML = `<img src="{{asset('assets/icon/tnks/search_alt-dark.svg')}}" /> Detail: ${decodedText}`;

         // Tambahkan elemen anchor ke list-tiket
         listtiket.appendChild(ticketLink);

      }
   }

   function onScanFailure(error) {}

   let html5QrcodeScanner = new Html5QrcodeScanner(
      "reader", {
         fps: 10,
         qrbox: {
            width: 250,
            height: 250
         }
      },
      false
   );
   html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>


@endsection