@extends('etiket.admin.template.index')

@section('css')

<style>
   .tabel1 th {
      text-align: center;
      vertical-align: middle;
   }
</style>

@endsection

@section('main')

<div class="card">
   <div class="card-body">
      <label class="text-2xl font-bold gk-text-base-black mb-2">Data Booking</label>


      <div class="d-flex justify-content-between align-items-center mb-2">
         <div class="filter">
            <strong>Filter</strong>
            <form action="{{ route('admin.destinasi.booking', ['id' => $id]) }}" method="get">
               <div class="input-group">
                  <select class="form-select d-inline-block w-auto" name="filter_ipt" aria-label="Filter Kategori" onchange="this.form.submit()">
                     <option value="0" {{ $filter == 0 ? "selected" : "" }}>Semua</option>
                     <option value="1" {{ $filter == 1 ? "selected" : "" }}>Booking</option>
                     <option value="2" {{ $filter == 2 ? "selected" : "" }}>Sudah Bayar</option>
                     <option value="3" {{ $filter == 3 ? "selected" : "" }}>Sedang Mendaki</option>
                  </select>
               </div>
            </form>
         </div>

         <div class="pencarian">
            <strong>Pencarian</strong>
            <form action="{{ route('admin.destinasi.booking', ['id' => $id]) }}" method="get">
               <div class="input-group">
                  <input type="text" class="form-control" name="search_ipt" placeholder="Cari..." aria-label="Cari..." value="{{ $search ? $search : "" }}">
                  <input type="hidden" name="filter_ipt" value="{{ $filter }}">
                  <button class="btn btn-outline-secondary" type="submit">Cari</button>
               </div>
            </form>
         </div>
      </div>
      <table class="table table-striped table-hover table-bordered tabel1">
         <table class="table table-striped table-hover table-bordered tabel1">
            <thead>
               <tr>
                  <th rowspan="2" class="p-1 font-bold col">Ketua</th>
                  <th colspan="2" class="p-1 font-bold col">Pendakian</th>
                  <th rowspan="2" class="p-1 font-bold col">Gate Masuk</th>
                  <th rowspan="2" class="p-1 font-bold col">Pendaki</th>
                  <th rowspan="2" class="p-1 font-bold col">Status</th>

                  <th rowspan="2" class="p-1 font-bold col">Aksi</th>
               </tr>
               <tr>
                  <!-- = -->
                  <th class="p-1 font-bold col">Start</th>
                  <th class="p-1 font-bold col">End</th>
               </tr>
            </thead>
            <tbody class="table-group-divider">
               @foreach($data as $item)
               <tr>
                  <td class="p-1 text-center">{{ $item->pendakis[0]->nama }}</td>
                  <td class="p-1 text-center">{{ $item->tanggal_masuk }}</td>
                  <td class="p-1 text-center">{{ $item->tanggal_keluar }}</td>
                  <td class="p-1 text-center">{{ $item->gateMasuk['nama'] }}</td>
                  <td class="p-1 text-center">{{ $item->pendakis->count() }}</td>
                  <td class="p-1 text-center">{{ $item->status_booking}}</td>
                  <td class="p-1 text-center">
                     <a href="" class="btn btn-sm btn-info">Detail</a>
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </table>

      <div>
         <ul class="pagination justify-content-end">
            <li class="page-item {{ $data->currentPage() == 1 ? 'disabled' : '' }}">
               <a class="page-link" href="{{ $data->previousPageUrl() }}&filter_ipt={{ $filter }}&search_ipt={{ $search }}" tabindex="-1" aria-disabled="true">Previous</a>
            </li>

            @for ($i = 1; $i <= $data->lastPage(); $i++)
               <li class="page-item {{ $data->currentPage() == $i ? 'active' : '' }}">
                  <a class="page-link" href="{{ $data->url($i) }}&filter_ipt={{ $filter }}&search_ipt={{ $search }}">{{ $i }}</a>
               </li>
               @endfor

               <li class="page-item {{ $data->currentPage() == $data->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $data->nextPageUrl() }}&filter_ipt={{ $filter }}&search_ipt={{ $search }}">Next</a>
               </li>
         </ul>
      </div>

   </div>
</div>

<div class="card">
   <div class="card-body">
   </div>
</div>

@endsection

@section('js')

@endsection