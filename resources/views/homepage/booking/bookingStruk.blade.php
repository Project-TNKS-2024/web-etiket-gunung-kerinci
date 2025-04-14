<!DOCTYPE html>
<html lang="id">

<head>
   <title>{{ config('app.name', 'Laravel') }} - Bukti Pembelian</title>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="shortcut icon" type="image/png" href="{{ asset('assets/icon/tnks.png') }}" />
   <link rel="stylesheet" href="{{ asset('assets/icon/tnks.png') }}" />
   <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">

</head>

<body>

   @include('homepage.template.tiket.cardStruk', ['data' => $data])
   <div class="mb-2 text-center">
      <a href="{{ route('homepage.booking.payment', ['id' => $data->id]) }}" class="btn btn-outline-secondary"><i class="fa fa-print"></i>Laman Pembelian</a>
      <a href="javascript:void(0)" onclick="invoicePrint()" class="btn btn-outline-secondary"><i class="fa fa-print"></i> Print</a>
      <!-- <a href="javascript:void(0)" onclick="invoiceDownload()" class="btn btn-outline-secondary"><i class="fa fa-download"></i> Download</a> -->
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