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
            <select name="filter-waktu" class="form-select" style="width: auto; max-width: 150px;">
               <option value="dalam_booking" {{ request('filter-waktu') == 'dalam_booking' ? 'selected' : '' }}>Dalam Booking</option>
               <option value="sudah_selesai" {{ request('filter-waktu') == 'sudah_selesai' ? 'selected' : '' }}>Sudah Selesai</option>
               <option value="akan_datang" {{ request('filter-waktu') == 'akan_datang' ? 'selected' : '' }}>Akan Datang</option>
               <option value="" {{ request('filter-waktu') == '' ? 'selected' : '' }}>Semua</option>
            </select>


            <!-- Tombol Filter -->
            <button type="submit" class="btn btn-primary">Filter</button>
         </form>
      </div>

      <div class="table-responsive">
         <table class="table table-bordered">
            <thead class="bg-dark text-white ">
               <tr>
                  <th>No</th>
                  <th>Id</th>
                  <th>Ketua</th>
                  <th>Tanggal</th>
                  <th>Gate Masuk</th>
                  <th>Pendaki</th>
                  <th>Status Booking</th>

                  <th>Aksi</th>
               </tr>

            </thead>
            <tbody class="table-group-divider">

               @if ($dataPrioritas->count() > 0)
               <tr>
                  <td colspan="8" style="padding: 5px 16px; text-align:center; font-style:italic; background:#d6dde5;">Prioritas</td>
               </tr>

               @foreach($dataPrioritas as $item)
               <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{$item->id}}</td>
                  <td class="">{{ $item->pendakis->count() > 0 ? $item->pendakis[0]->biodata->first_name  . ' ' . $item->pendakis[0]->biodata->last_name : '-' }}</td>
                  <td class="">{{ $item->tanggal_masuk }}</td>
                  <td class="">{{ $item->gateMasuk['nama'] }}</td>
                  <td class="">{{ $item->pendakis->count() }} orang</td>
                  <td class="">{{ $item->getStatusBooking()->status}}</td>
                  @php
                  $colorStatus = 'outline-info';
                  if($item->pembayaran && $item->pembayaran->count() > 0) {

                  if($item->pembayaran->last()->status == 'pending'){
                  $colorStatus = 'warning';
                  }else if($item->pembayaran->last()->status == 'success'){
                  $colorStatus = 'success';
                  }else if($item->pembayaran->last()->status == 'failed'){
                  $colorStatus = 'danger';
                  }else{
                  $colorStatus = 'info';
                  }

                  } @endphp
                  <td class="">
                     <a href="{{route('admin.destinasi.booking.show', ['id' => $item->id])}}" class="btn btn-sm btn-info">
                        <i class="fa-solid fa-circle-info"></i>
                     </a>
                     <a href="{{route('admin.destinasi.booking.payment.show', ['id' => $item->id])}}" class="btn btn-sm btn-{{$colorStatus}} mt-sm-1 mt-md-0">
                        <i class="fa-solid fa-money-bill-wave"></i>
                     </a>
                  </td>
               </tr>
               @endforeach

               <tr>
                  <td colspan="8" style="padding: 5px 16px; text-align:center; font-style:italic; background:#d6dde5;">Booking</td>
               </tr>
               @endif

               @foreach($data as $item)
               <tr>
                  <td>{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                  <td>{{$item->id}}</td>
                  <td class="">{{ $item->pendakis->count() > 0 ? $item->pendakis[0]->biodata->first_name  . ' ' . $item->pendakis[0]->biodata->last_name : '-' }}</td>
                  <td class="">{{ $item->tanggal_masuk }}</td>
                  <td class="">{{ $item->gateMasuk['nama'] }}</td>
                  <td class="">{{ $item->pendakis->count() }} orang</td>
                  <td class="">{{ $item->getStatusBooking()->status}}</td>
                  @php
                  $colorStatus = 'outline-info';
                  if($item->pembayaran && $item->pembayaran->count() > 0) {

                  if($item->pembayaran->last()->status == 'pending'){
                  $colorStatus = 'warning';
                  }else if($item->pembayaran->last()->status == 'success'){
                  $colorStatus = 'success';
                  }else if($item->pembayaran->last()->status == 'failed'){
                  $colorStatus = 'danger';
                  }else{
                  $colorStatus = 'info';
                  }

                  } @endphp
                  <td class="">
                     <a href="{{route('admin.destinasi.booking.show', ['id' => $item->id])}}" class="btn btn-sm btn-info">
                        <i class="fa-solid fa-circle-info"></i>
                     </a>
                     <a href="{{route('admin.destinasi.booking.payment.show', ['id' => $item->id])}}" class="btn btn-sm btn-{{$colorStatus}} mt-sm-1 mt-md-0">
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
<script>
   const data = @json($data)
</script>
@endsection