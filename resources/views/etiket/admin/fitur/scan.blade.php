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
         <div class="card-header d-flex justify-content-between align-items-center">
            <h3><b>Scan Reader</b></h3>
            <a onclick="html5QrcodeScanner.render(onScanSuccess, onScanFailure)" class="btn btn-light text-black">
               <i class="fa-solid fa-rotate text-black"></i>
            </a>
         </div>
         <div class="card-body text-center">
            <div class="container">
               <div id="reader" width="600px"></div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-12 col-sm-6 col-md-4">
      <div class="card">
         <div class="card-body">
            <p>kwhbfhbe</p>
         </div>
      </div>
   </div>
</div>

@endsection

@section('js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
   function onScanSuccess(decodedText) {
      console.log('Scanned:', decodedText);
      window.location.href = `{{ url('admin/fitur/scanTiketAction') }}/${encodeURIComponent(decodedText)}`;
   }

   function onScanFailure(error) {
      console.warn('Scan failed:', error);
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