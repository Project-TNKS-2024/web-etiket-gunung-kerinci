@extends('etiket.admin.template.index')

@section('css')

<style>

</style>

@endsection

@section('main')

<div class="card">
   <div class="card-header">
      <h3><b>Daftar Tiket</b></h3>
   </div>
   <div class="card-body">
      <div class="table-responsive">
         <table class="table table-bordered">
            <thead class="bg-dark text-white">
               <tr>
                  <th class="p-3 gk-bg-base-white font-bold col">Nama</th>
                  <th class="p-3 gk-bg-base-white font-bold col">Keterangan</th>
                  <th class="p-3 gk-bg-base-white font-bold col">Kategori</th>
                  <th class="p-3 gk-bg-base-white font-bold col">HTM Weekday</th>
                  <th class="p-3 gk-bg-base-white font-bold col">HTM Weekend</th>
                  <th class="p-3 gk-bg-base-white font-bold col">Min Pendaki</th>
                  <th class="p-3 gk-bg-base-white font-bold col">Aksi</th>
               </tr>
            </thead>
            <tbody class="table-group-divider">
               @foreach ($tiket as $d)
               <tr class="tiket-row">
                  <td class="font-medium col-1">{{$d->nama}}</td>
                  <td class="font-medium col">{{$d->keterangan}}</td>
                  <td class="font-medium col">
                     {{$d->tiket_pendaki[0]->kategori_pendaki == "wna" ? "Mancanegara" : "Nusantara"}}
                     <br>
                     {{$d->tiket_pendaki[1]->kategori_pendaki == "wna" ? "Mancanegara" : "Nusantara"}}
                  </td>
                  <td class="font-medium col-2">
                     Rp {{number_format($d->tiket_pendaki[0]->harga_masuk_wd)}}
                     <br>
                     Rp {{number_format($d->tiket_pendaki[1]->harga_masuk_wd)}}
                  </td>
                  <td class="font-medium col-2">
                     Rp {{number_format($d->tiket_pendaki[0]->harga_masuk_wk)}}
                     <br>
                     Rp {{number_format($d->tiket_pendaki[1]->harga_masuk_wk)}}
                  </td>
                  <td class="font-medium col text-center">{!! $d->min_pendaki == null ? '<span class="text-2xl">&infin;</span>' : $d->min_pendaki !!}</td>
                  <td class="font-medium col-1 text-center">
                     <a href="{{route('admin.destinasi.tiket.update', ['id' => $d->id])}}" class="btn btn-primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                     </a>

                     <form method="post" action="{{route('admin.destinasi.tiket.deleteAction')}}">
                        @csrf
                        <input type="hidden" name="id_tiket" value="{{$d->id}}">

                        <button type="submit" class="btn btn-danger mt-1" onclick="openswal(event, this)">
                           <i class="fa-solid fa-trash"></i>
                        </button>
                     </form>

                  </td>
               </tr>

               @endforeach
            </tbody>
         </table>
         <div class="d-flex justify-content-end">
            <a class="btn btn-primary "
               href="{{route('admin.destinasi.tiket.add', ['id' => $id_destinasi])}}">
               <i class="fa-solid fa-plus"></i>
               Tambah Tiket
            </a>
         </div>
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