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
   <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
         <label class="text-2xl font-bold gk-text-base-black mb-2">Daftar Pembayaran</label>
      </div>
      <div class="d-flex justify-content-end mb-3">
         <!-- Form Filter -->
         <form action="{{ route('admin.destinasi.pembayaran', ['id' => $destinasi->id]) }}" method="GET" class="d-flex align-items-center gap-2">
            <!-- Input Pencarian -->
            <input type="text" name="search" class="form-control" placeholder="Cari email/nama..." value="{{ request('search') }}" style="width: auto; max-width: 200px;">

            <!-- Filter Status -->
            <select name="filter" class="form-select" style="width: auto; max-width: 150px;">
               <option value="">Semua Status</option>
               <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending</option>
               <option value="success" {{ request('filter') == 'success' ? 'selected' : '' }}>Success</option>
               <option value="failed" {{ request('filter') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            <!-- Filter Tanggal -->
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" style="width: auto; max-width: 150px;">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" style="width: auto; max-width: 150px;">

            <!-- Tombol Filter -->
            <button type="submit" class="btn btn-primary">Filter</button>
         </form>
      </div>



      <!-- Tabel Data -->
      <table class="rounded table table-striped table-bordered">
         <thead>
            <tr>
               <th scope="col">#</th>
               <th scope="col">Nama Ketua</th>
               <th scope="col">Tanggal</th>
               <th scope="col">Status</th>
               <th scope="col">Keterangan</th>
               <th scope="col">Aksi</th>
            </tr>
         </thead>
         <tbody class="table-group-divider">
            @foreach ($dataBooking as $index => $booking)
            <tr>
               <th scope="row">{{ $dataBooking->firstItem() + $index }}</th>
               <td>{{ optional($booking->pendakis->first())->first_name }} {{ optional($booking->pendakis->first())->last_name }}</td>
               <td>{{ optional($booking->pembayaran->last())->created_at }}</td>
               <td>{{ optional($booking->pembayaran->last())->status }}</td>
               <td>{{ optional($booking->pembayaran->last())->keterangan }}</td>
               <td>
                  @if (optional($booking->pembayaran->last())->status == 'pending')
                  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailPembayaran" onclick="showDetailPembayaran({{json_encode($booking)}})">Verifikasi</button>
                  @else
                  <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#detailPembayaran" onclick="showDetailPembayaran({{json_encode($booking)}})">Detail</button>
                  @endif
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>

      <!-- Navigasi Pagination -->
      <div class="d-flex justify-content-center mt-3">
         {{ $dataBooking->appends(request()->input())->links('pagination::bootstrap-5') }}
      </div>
   </div>
</div>



<!-- Modal -->
<div class="modal fade" id="detailPembayaran" tabindex="-1" aria-labelledby="detailPembayaranLabel" aria-hidden="true">
   <div class="modal-dialog modal-xl">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5" id="detailPembayaranLabel">Detail Pembayaran</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <div class="container">
               <div class="row">
                  <div class="col-md-6">
                     <div class="card sticky-deskripsi" id="pembayaran">
                        <div class="card-body">

                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <h1 class="fs-5 fw-bold text-center">Bukti Pembayaran</h1>
                     <div id="buktiPembayaranContainer"></div>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">

            <form method="post" id="btnVerify" class="d-flex align-items-center w-100" action="{{ route('admin.destinasi.pembayaran.updateAction') }}">
               @csrf
               <input type="text" name="keterangan" class="form-control me-3" placeholder="Masukkan keterangan">
               <input type="hidden" name="id_booking" id="updateIdBooking" value="">
               <button type="submit" name="verified" class="btn btn-primary" value="yes">Setujui</button>
               <button type="submit" name="verified" class="btn btn-warning ms-1" value="no">Tolak</button>
            </form>
         </div>

      </div>
   </div>
</div>

@endsection

@section('js')
<!-- script untuk menampilkan detail pembayaran -->
<script>
   function showDetailPembayaran(booking) {
      console.log(booking);
      // Element-elemen yang akan diupdate
      const modal = document.getElementById('detailPembayaran');
      const pembayaranSection = modal.querySelector('#pembayaran .card-body');
      const formIdBooking = document.getElementById('updateIdBooking');

      formIdBooking.value = booking.id;

      // Menyusun HTML berdasarkan data booking
      let pembayaranContent = `
        <h1 class="fs-5 fw-bold text-center">Tagihan</h1>
        <h1 class="fs-4 fw-bold">Tagihan Ke:</h1>
        <p>Email: ${booking.user.email}</p>

        <h1 class="fs-4 fw-bold">Deskripsi Pendakian</h1>
        <table class="table table-borderless table-des">
            <tr>
                <td>Nama Ketua</td>
                <td>:</td>
                <td>${booking.pendakis[0]?.biodata.first_name || ''} ${booking.pendakis[0]?.biodata.last_name || ''}</td>
            </tr>
            <tr>
                <td>Jalur Simaksi</td>
                <td>:</td>
                <td>${booking.lampiran_simaksi ? '<span class="text-success">Ya</span>' : '<span class="text-danger">Tidak</span>'}</td>
            </tr>
            <tr>
                <td>Gate Masuk</td>
                <td>:</td>
                <td>${booking.gate_masuk}</td>
            </tr>
            <tr>
                <td>Gate Keluar</td>
                <td>:</td>
                <td>${booking.gate_keluar}</td>
            </tr>
            <tr>
                <td>Tanggal Pendakian</td>
                <td>:</td>
                <td>${booking.tanggal_masuk}</td>
            </tr>
            <tr>
                <td>Tanggal Keluar</td>
                <td>:</td>
                <td>${booking.tanggal_keluar}</td>
            </tr>
            <tr>
                <td>Total Pendaki</td>
                <td>:</td>
                <td>${booking.total_pendaki_wni} WNI dan ${booking.total_pendaki_wna} WNA</td>
            </tr>
        </table>

        <h1 class="fs-4 fw-bold">List Tagihan Tiket</h1>
        <table class="table table-des bg-transparent">
            <tr class="fw-semibold">
                <td>Nama Pendaki</td>
                <td class="text-end">Tagihan</td>
            </tr>
    `;

      // Menambahkan detail tagihan tiap pendaki
      booking.pendakis.forEach(pendaki => {
         pembayaranContent += `
            <tr>
                <td>${pendaki.biodata.first_name} ${pendaki.biodata.last_name}</td>
                <td class="text-end">Rp. ${new Intl.NumberFormat('id-ID').format(pendaki.tagihan)}</td>
            </tr>
        `;
      });

      // Menambahkan total pembayaran
      pembayaranContent += `
            <tr>
                <td class="fw-semibold text-end">Total:</td>
                <td class="text-end">Rp. ${new Intl.NumberFormat('id-ID').format(booking.total_pembayaran)}</td>
            </tr>
        </table>
    `;

      // Menyisipkan konten ke dalam modal
      pembayaranSection.innerHTML = pembayaranContent;

      // Bagian untuk bukti pembayaran
      const buktiPembayaranContainer = document.getElementById('buktiPembayaranContainer');
      buktiPembayaranContainer.innerHTML = ''; // Kosongkan kontainer sebelum menambahkan

      if (booking.pembayaran && booking.pembayaran.length > 0) {
         booking.pembayaran.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'card mb-3';

            // Pastikan URL benar dengan menghapus bagian path tambahan
            const baseUrl = window.location.origin; // http://127.0.0.1:8000
            const correctUrl = `${baseUrl}/${file.bukti_pembayaran}`; // Gabungkan base URL dengan path bukti_pembayaran

            card.innerHTML = `
                    <div class="p-2">
                        <div class="d-flex align-items-center justify-content-between mx-2">
                            <p class="mb-0"><strong>Bukti ${index + 1}</strong> </p>
                            <a href="${correctUrl}" class="btn bg-transparent" target="_blank"> <i class="fa-regular fa-eye"></i></a>
                        </div>
                        <embed src="${correctUrl}" type="application/pdf" width="100%" height="280px">
                    </div>
                `;

            buktiPembayaranContainer.appendChild(card);
         });
      } else {
         buktiPembayaranContainer.innerHTML = '<p class="text-center">Tidak ada bukti pembayaran tersedia.</p>';
      }

      // Menampilkan modal
   }
</script>
@endsection