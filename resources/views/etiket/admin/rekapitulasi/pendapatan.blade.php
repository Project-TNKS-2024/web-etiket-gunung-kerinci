@extends('etiket.admin.template.index')

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('DataTables/datatables.css')}}">
@endsection
@section('main')

<div class="card mb-4">
   <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="mb-0"><b>Rekapitulasi Pendapatan</b></h3>
   </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
   <div class="card-header">
      <h5><b>Filter Data</b></h5>
   </div>
   <div class="card-body">
      <form action="{{ route('admin.rekap.pendapatan') }}" method="GET" class="row g-3 filter-form">
         <div class="col-md-4">
            <label for="d" class="form-label">Destinasi</label>
            <select name="d" id="d" class="form-control">
               <option value="">Semua Destinasi</option>
               @foreach($daftar_destinasi as $dest)
               <option value="{{ $dest->id }}" {{ request('d') == $dest->id ? 'selected' : '' }}>
                  {{ $dest->nama }}
               </option>
               @endforeach
            </select>
         </div>
         <div class="col-md-3">
            <label for="rs" class="form-label">Rentang Awal</label>
            <input type="date" name="rs" id="rs" class="form-control" value="{{ request('rs') }}">
         </div>
         <div class="col-md-3">
            <label for="ra" class="form-label">Rentang Akhir</label>
            <input type="date" name="ra" id="ra" class="form-control" value="{{ request('ra') }}">
         </div>
         <div class="col-md-2">
            <button type="submit" class="btn btn-primary mb-1 w-100">Terapkan</button>
            <a href="{{ route('admin.rekap.pendapatan.download', request()->query()) }}" target="_blank" class="btn btn-success w-100">Download</a>
         </div>
      </form>
   </div>
</div>

{{-- Tabel Pendapatan --}}
<div class="card">
   <div class="card-header">
      <h5 class="mb-0"><b>Pendapatan</b></h5>
      <div class="d-flex justify-content-between">
         <p class="mb-0">{{ $rentang_awal->format('d M Y') }} - {{ $rentang_akhir->format('d M Y') }}</p>
         <p class="mb-0 text-black fw-bold">Total: Rp. {{ number_format($total, 0, ',', '.') }}</p>
      </div>
   </div>
   <div class="card-body">
      <div class="table-responsive">
         <table class="table table-bordered table-hover" id="table-pendapatan">
            <thead class="bg-dark text-white text-center align-middle">
               <tr>
                  <th>No</th>
                  <th>Id Booking</th>
                  <th>Destinasi</th>
                  <th>Pembayaran</th>
                  <th>Pendakian</th>
                  <th>Nominal</th>
                  <th>Validator</th>
               </tr>
            </thead>
            <tbody>
               @forelse ($data as $d)
               <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><a href="{{route('admin.destinasi.booking.show', ['id' => $d->id])}}">{{ $d->id }}</a></td>
                  <td>{{ $d->destinasi->nama ?? '-' }}</td>
                  <td>{{ optional($d->pembayaran->last())->created_at?->format('Y-m-d') ?? '-' }}</td>
                  <td>{{ $d->tanggal_masuk }}</td>
                  <td class="text-end">{{ number_format($d->total_pembayaran, 0, ',', '.') }}</td>
                  <td>#{{ optional($d->pembayaran->last())->validator ?? '-' }}</td>
               </tr>
               @empty
               <tr>
                  <td colspan="7" class="text-center">Tidak ada data ditemukan.</td>
               </tr>
               @endforelse
            </tbody>
         </table>
      </div>
   </div>
</div>

@endsection

@section('js')
<script src="{{asset('DataTables/datatables.js')}}"></script>
<script>
   // document.addEventListener('DOMContentLoaded', function() {
   new DataTable('#table-pendapatan', {
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "language": {
         "lengthMenu": "Tampilkan _MENU_ data per halaman",
         "zeroRecords": "Tidak ada data yang cocok",
         "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
         "infoEmpty": "Tidak ada data yang tersedia",
         "infoFiltered": "(disaring dari _MAX_ total data)",
         "search": "Cari:",
         "paginate": {
            "first": "Pertama",
            "last": "Terakhir",
            "next": "Selanjutnya",
            "previous": "Sebelumnya"
         }
      }
   });
   // });
</script>
@endsection