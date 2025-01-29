 <style>
    .card-tiket table tr td {
       margin: 0px;
       padding: 4px;
    }

    .card-tiket .tiket-qr img {
       margin: auto;
    }
 </style>

 @foreach ($bookings as $booking)
 <div class="card card-tiket shadow me-3" style="max-width: 400px; display: inline-block;">
    <div class="card-body">
       <div class="container">
          <table class="table table-borderless align-middle">
             <tr class="fw-bold">
                <td>Nama Ketua</td>
                <td>Status</td>
             </tr>
             <tr>
                <td>{{ $booking->pendakis[0]->first_name .' '. $booking->pendakis[0]->last_name }}</td>
                <td>
                   @if ($booking->status_booking <= 3)
                      <span class="badge text-bg-warning">Belum Bayar</span>
                      @elseif ($booking->status_booking == 4)
                      <span class="badge text-bg-success">Sudah Bayar</span>
                      @elseif ($booking->status_booking == 6)
                      <span class="badge text-bg-primary">Dalam Pendakian</span>
                      @elseif ($booking->status_booking == 7)
                      <span class="badge text-bg-success">Selesai</span>
                      @endif
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
                <td>Check In</td>
                <td>Check Out</td>
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
                         {{ $booking->total_pendaki_wni }} WNI
                      </div>
                      <div class="col">
                         {{ $booking->total_pendaki_wna }} WNA
                      </div>
                   </div>
                </td>
             </tr>
          </table>

          <div class="text-center tiket-qr mt-3">
             <div class="qrcode_kodebooking mx-auto" data-qr="{{ $booking->unique_code ?? '___ ___' }}"></div>
             <p class="mb-0 mt-1">Kode Boooking</p>
             <h4>{{ $booking->unique_code ?? '___ ___' }}</h4>

             @if ($booking->status_pembayaran)
             <a href="{{ route('homepage.booking', ['id' => $booking->id]) }}" class="btn w-100 btn-gl-primary text-white">Lihat Tiket</a>
             @else
             <a href="{{ route('homepage.booking', ['id' => $booking->id]) }}" class="btn w-100 btn-gl-primary text-white">Lanjut Booking</a>
             @endif
          </div>
       </div>
    </div>
 </div>
 @endforeach

 @if (count($bookings) == 0)
 <p class="gk-text-neutrals700 text-center">Tidak ada tiket yang aktif</p>
 @endif