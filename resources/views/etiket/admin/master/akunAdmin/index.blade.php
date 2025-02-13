@extends('etiket.admin.template.index')

@section('main')

<div class="card">
   <div class="card-header">
      <h3 class=" mb-0"><b> Daftar Admin</b></h3>
   </div>

   <div class="card-body">
      <div class="table-responsive">
         <table class="table table-bordered">
            <thead class="bg-dark text-white">
               <tr>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Destinasi</th>
                  <th>Role</th>
                  <th>Aksi</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($admins as $admin)
               <tr>
                  <td>{{ $admin->biodata ? $admin->biodata->first_name : '-' }}</td>
                  <td>{{ $admin->email }}</td>
                  <td>
                     @foreach ($admin->destinasis as $destinasi)
                     <span class="badge bg-warning mt-1 text-dark">{{ $destinasi->nama }}</span>
                     @endforeach

                  </td>
                  <td>{{ $admin->roles->pluck('name')->implode(', ') }}</td>
                  <td>
                     <a href="{{ route('admins.akun.edit', $admin->id) }}" class="btn btn-primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                     </a>
                     <form action="{{ route('admins.akun.delete') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="id" value="{{ $admin->id }}">
                        <button type="submit" class="btn btn-danger mt-1"
                           onclick="return confirm('Yakin ingin menghapus admin ini?')">
                           <i class="fa-solid fa-trash"></i></button>
                     </form>
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
      <div class="d-flex justify-content-end">
         <a href="{{ route('admins.akun.create') }}" class="btn btn-primary mb-3">Tambah Admin</a>
      </div>
   </div>
</div>
@endsection