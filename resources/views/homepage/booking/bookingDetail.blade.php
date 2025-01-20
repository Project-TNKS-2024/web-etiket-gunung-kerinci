@extends('homepage.template.index')


@section('css')
<style>
   /* style untuk form detail booking */
   .table tbody tr td {
      padding: 3px 5px;
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

        <div id="booking-detail">
            <h1>Detail Pemesanan</h1>
            <div class="row mt-3">
                <div class="col-12 col-md-6" id="formulir">
                    <div class="row">
                        <div class="col">
                            <h4>Nama Ketua</h4>
                            <p>{{ $formulirPendakis[0]->first_name }} {{ $formulirPendakis[0]->last_name }}</p>
                            <h4>Gerbang Masuk</h4>
                            <p>{{ $booking->gateMasuk->nama }}</p>
                            <h4>Check In</h4>
                            <p>{{ $booking->tanggal_masuk }}</p>
                            <h4>Jumlah Anggota</h4>
                            <p>5 orang</p>
                        </div>
                        <div class="col">
                            <h4>SIMAKSI</h4>
                            <p>
                                @if ($booking->lampiran_simaksi == null)
                                    <span class="c-red">Tidak</span>
                                @else
                                    <span class="c-green">Ya</span>
                                @endif
                            </p>
                            <h4>Gerbang Keluar</h4>
                            <p>{{ $booking->gateKeluar->nama }}</p>
                            <h4>Check out</h4>
                            <p>{{ $booking->tanggal_keluar }}</p>
                            <h4>Kewarganegaraan</h4>
                            <div class="row">
                                <div class="col">
                                    <p>{{ $booking->total_pendaki_wni }} WNI</p>
                                </div>
                                <div class="col">
                                    <p>{{ $booking->total_pendaki_wna }} WNA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card" id="pembayaran">
                        <div class="card-body">
                            <h4>Total Pembayaran</h4>
                            @foreach ($pendakis as $pen)
                                <p>{{ $pen->first_name }} {{ $pen->last_name }}<span class="float-right">Rp.
                                        {{ number_format($pen->tagihan) }}</span></p>
                            @endforeach
                            <p class="fw-bold c-blue">Total <span class="float-right">Rp.
                                    {{ number_format($booking->total_pembayaran) }}</span></p>
                            <p class="span">*{{ $booking->total_hari }} hari {{ $booking->total_hari - 1 }} malam
                                ({{ $booking->total_hari }}D{{ $booking->total_hari - 1 }}M)</p>
                        </div>
                    </div>
                    <div class="text-start form-group mt-2">
                        <a href="{{route('homepage.booking.payment', ['id' => $booking->id])}}" class="btn btn-gl-primary ">Lakukan Pembayaran</a>
                    </div>
                </div>
            </div>
        </div>
   <div class="card border-0 shadow">
      <div class="card-body px-4 px-md-5 pb-4">
         <div class="mt-3">
            <h1 class="fs-5 fw-bold">Booking {{$booking->gateMasuk->destinasi->nama}}</h1>
            <div class="row">
               <div class="col-12 col-md-6">
                  <table class="table table-borderless">
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
         <div class="mt-4">
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
                        <td>{{$pendaki->nik}}</td>
                     </tr>
                     <tr>
                        <td>No Telepon</td>
                        <td> : </td>
                        <td>{{$pendaki->no_hp}}</td>
                     </tr>
                     <tr>
                        <td>No Telepon Darurat</td>
                        <td> : </td>
                        <td>{{$pendaki->no_hp_darurat}}</td>
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
                  <div class="border rounded p-2" style="max-height: 300px; overflow: hidden;">
                     @php
                     $extension = pathinfo($pendaki->lampiran_identitas, PATHINFO_EXTENSION);
                     @endphp

                     @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                     <img src="{{asset($pendaki->lampiran_identitas)}}" alt="Lampiran Identitas" class="img-fluid" style="max-height: 280px; width: auto; display: block; margin: 0 auto;">
                     @else
                     <embed src="{{asset($pendaki->lampiran_identitas)}}" type="application/pdf" width="100%" height="280px">
                     @endif
                  </div>
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

        <div class="centered">
            <a class="btn btn-primary mt-4 me-3"
                href="{{ route('homepage.booking.formulir', ['id' => $booking->id]) }}">Formulir</a>
            <button class="btn btn-primary mt-4" href="#" id="pay-button">Selanjutnya</button>
        </div>
    </div>

</div>

@endsection
@section('js')
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-8mWWkdmzeR1xRVmL"></script>
    <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
    </head>

    <script>
        console.log("The Pendakis");
        console.log(@json($pendakis[0]));
        // console.log({{ $pendakis }});
    </script>


    {{-- <script type="text/javascript">
        // For example trigger on button clicked, or any time you need
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function() {
            // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
            window.snap.pay('{{ $snaptoken }}', {
                onSuccess: function(result) {
                    window.location.href =
                        "{{ route('homepage.booking.payment', ['id' => $booking->id]) }}";
                },
                onPending: function(result) {
                    /* You may add your own implementation here */
                    alert("wating your payment!");
                    console.log(result);
                },
                onError: function(result) {
                    /* You may add your own implementation here */
                    alert("payment failed!");
                    console.log(result);
                },
                onClose: function() {
                    /* You may add your own implementation here */
                    alert('you closed the popup without finishing the payment');
                }
            });
            // customer will be redirected after completing payment pop-up
        });
    </script> --}}


@endsection
