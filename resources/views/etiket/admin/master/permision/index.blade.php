@extends('etiket.admin.template.index')

@section('css')
<style>

</style>
@endsection

@section('main')
<div class="card mb-3">
   <div class="card-header">
      <h3 class="text-white mb-0"><b>Manajemen Role & Permission</b></h3>
   </div>
</div>

<div class="row">
   <div class="col-md-6">
      <div class="card mb-3">
         <div class="card-header">
            <h5 class="text-white mb-0"><b>Tambah Role</b></h5>
         </div>
         <div class="card-body">
            <form action="{{ route('roles.addAction') }}" method="POST">
               @csrf
               <div class="form-group">
                  <label for="role_name" class="form-label">Nama Role</label>
                  <input type="text" id="role_name" name="name" class="form-control" placeholder="Masukkan nama role" required>
               </div>
               <button type="submit" class="btn btn-primary w-100">Tambah Role</button>
            </form>
         </div>
      </div>
   </div>

   <div class="col-md-6">
      <div class="card mb-3">
         <div class="card-header">
            <h5 class="text-white mb-0"><b>Tambah Permission</b></h5>
         </div>
         <div class="card-body">
            <form action="{{ route('permissions.addAction') }}" method="POST">
               @csrf
               <div class="form-group">
                  <label for="permission_name" class="form-label">Nama Permission</label>
                  <input type="text" id="permission_name" name="name" class="form-control" placeholder="Masukkan nama permission" required>
               </div>
               <button type="submit" class="btn btn-primary w-100">Tambah Permission</button>
            </form>
         </div>
      </div>
   </div>
</div>

<div class="card mb-4">
   <div class="card-header ">
      <h5 class="text-white mb-0"><b>Daftar Role dan Permissions</b></h5>
   </div>
   <div class="card-body">
      <div class="table-responsive">
         <table class="table table-bordered">
            <thead class="bg-dark text-white">
               <tr>
                  <th>Role</th>
                  <th>Permissions</th>
                  <th>Aksi</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($roles as $role)
               <tr>
                  <td><strong>{{ $role->name }}</strong></td>
                  <td>
                     @foreach ($role->permissions as $permission)
                     <span class="badge bg-warning mt-1 text-dark">{{ $permission->name }}</span>
                     @endforeach
                  </td>
                  <td>
                     <a href="{{route('roles.update', ['id'=>$role->id])}}" class="btn btn-primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                     </a>
                     <form action="{{route('roles.deleteAction')}}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{$role->id}}">
                        <button type="submit" class="btn btn-danger mt-1" onclick="openswal(event, this)">
                           <i class="fa-solid fa-trash"></i>
                        </button>
                     </form>

                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </div>
</div>
@endsection

@section('js')
<script>
   function openswal(event, button) {
      event.preventDefault(); // Mencegah submit otomatis

      // Ambil form terdekat dari tombol yang ditekan
      const form = button.closest("form");

      // Tampilkan konfirmasi SweetAlert2
      Swal.fire({
         title: "Apakah Anda yakin?",
         text: "Data yang dihapus tidak dapat dikembalikan!",
         icon: "warning",
         showCancelButton: true,
         confirmButtonColor: "#d33",
         cancelButtonColor: "#3085d6",
         confirmButtonText: "Ya, hapus!",
         cancelButtonText: "Batal"
      }).then((result) => {
         if (result.isConfirmed) {
            // Jika dikonfirmasi, submit form
            form.submit();
         }
      });
   }
</script>
@endsection