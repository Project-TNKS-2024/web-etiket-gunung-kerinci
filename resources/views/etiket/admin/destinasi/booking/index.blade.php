@extends('etiket.admin.template.index')

@section('css')

<style>

</style>

@endsection

@section('main')

<div class="card">
   <div class="card-header">
      <h3><b>Data Booking</b></h3>
   </div>
   <div class="card-body">
      <div class="d-flex justify-content-end mb-3">
         <!-- Form Filter -->
         <form action="{{ route('admin.destinasi.booking', ['id' => $destinasi->id]) }}" method="GET" class="d-flex align-items-center gap-2">
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

      <div class="table-responsive">
         <table class="table table-bordered">
            <thead class="bg-dark text-white ">
               <tr>
                  <th>Ketua</th>
                  <th>Tanggal</th>
                  <th>Gate Masuk</th>
                  <th>Pendaki</th>
                  <th>Status Booking</th>

                  <th>Aksi</th>
               </tr>

            </thead>
            <tbody class="table-group-divider">
               @foreach($data as $item)
               <tr>
                  <td class="">{{ $item->pendakis->count() > 0 ? $item->pendakis[0]->biodata->first_name  . ' ' . $item->pendakis[0]->biodata->last_name : '-' }}</td>
                  <td class="">{{ $item->tanggal_masuk }}</td>
                  <td class="">{{ $item->gateMasuk['nama'] }}</td>
                  <td class="">{{ $item->pendakis->count() }} orang</td>
                  <td class="">{{ $item->getStatusBooking()}}</td>
                  <td class="">
                     <a href="{{route('admin.destinasi.booking.show', ['id' => $item->id])}}" class="btn btn-sm btn-info">
                        <i class="fa-solid fa-circle-info"></i>
                     </a>
                     <a href="{{route('admin.destinasi.booking.payment.show', ['id' => $item->id])}}" class="btn btn-sm btn-info mt-sm-1 mt-md-0">
                        <i class="fa-solid fa-money-bill-wave"></i>
                     </a>
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>

      <!-- Navigasi Pagination -->
      <div class="d-flex justify-content-center mt-3">
         {{ $data->appends(request()->input())->links('pagination::bootstrap-5') }}
      </div>
   </div>
</div>


@endsection

@section('js')

@endsection