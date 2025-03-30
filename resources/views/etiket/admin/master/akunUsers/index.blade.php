@extends('etiket.admin.template.index')

@section('css')

<style>
   #biodataTable {
      width: 100%;
      border-collapse: collapse;
   }

   #biodataTable td {
      padding: 0px 5px;
   }

   #biodataTable th {
      background-color: #f2f2f2;
      padding: 0px 5px;
   }

   #search-data {
      max-width: 350px;
      margin-left: auto;
   }
</style>

@endsection

@section('main')

<div class="card">
   <div class="card-header ">
      <h3><b>Daftar Pengguna</b></h3>
   </div>
   <div class="card-body">

      <div class="">
         <form method="GET" action="{{ route('admin.master.pengunjung') }}">
            <div class="input-group mb-3" id="search-data">
               <input type="text" name="search" class="form-control" placeholder="Cari pengguna..." value="{{ request('search') }}">
               <button class="btn btn-primary" type="submit">Cari</button>
            </div>
         </form>
      </div>

      <div class="table-responsive">
         <table class="table table-bordered">
            <thead class="bg-dark text-white">
               <tr>
                  <th scope="col">#</th>
                  <th scope="col">Email</th>
                  <th scope="col">ID / Code Pendaki</th>
                  <th scope="col">Nama Lengkap</th>
                  <th scope="col">Kenegaraan</th>
                  <th scope="col">Jenis Kelamin</th>
                  <th scope="col">Satus</th>
                  <th scope="col">Action</th>
               </tr>
            </thead>
            <tbody class="table-group-divider">
               @foreach ($dataUser as $index => $user)
               <tr>
                  <th scope="row">{{ $dataUser->firstItem() + $index }}</th> <!-- Nomor urut sesuai halaman -->
                  <td>{{ $user->email }}</td>
                  <td>#{{$user->id}} / {{ $user->biodata->id }}</td>
                  <td>{{ optional($user->biodata)->first_name }} {{ optional($user->biodata)->last_name }}</td>
                  <td>{{ optional($user->biodata)->dataNegara->code }}</td>
                  <td>{{ optional($user->biodata)->jenis_kelamin == 'l' ? 'Laki-laki' : 'Perempuan' }}</td>
                  <td>{{ optional($user->biodata)->verified }}</td>
                  <td>
                     @if ($user->biodata->verified == 'pending')
                     <a href="{{route('admin.master.pengunjung.biodata', ['id' => $user->id])}}" class="btn btn-sm btn-warning">
                        <i class="fa-solid fa-circle-info"></i>
                     </a>
                     @else
                     <a href="{{route('admin.master.pengunjung.biodata', ['id' => $user->id])}}" class="btn btn-sm btn-info">
                        <i class="fa-solid fa-circle-info"></i>
                     </a>
                     @endif
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>

         <!-- Tampilkan navigasi pagination -->
         <div class="d-flex justify-content-end mt-3">
            {{ $dataUser->links('pagination::bootstrap-5') }}
         </div>

      </div>
   </div>
</div>

<!-- Modal -->
@endsection

@section('js')


@endsection