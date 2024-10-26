@extends('etiket.admin.template.index')

@section('css')

<style>

</style>

@endsection

@section('main')

<div class="card">
   <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
         <label class="text-2xl font-bold gk-text-base-black mb-2">Daftar Tiket</label>

         <a class="text-start text-black d-flex align-items-center gap-2 w-fit border-neutrals500 btn  gk-bg-base-white"
            href="{{route('admin.destinasi.tiket.add', ['id' => $id_destinasi])}}"
            style="border: 1px solid var(--neutrals500);">
            <img src="{{asset('assets/icon/tnks-plus.svg')}}" />
            Tambah Tiket
         </a>
      </div>


      <div class="border-neutrals500">

      </div>
      <table class="table table-striped table-hover table-bordered">
         <thead>
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
                  <a href="{{route('admin.tiket.edit', ['id' => $d->id])}}" class="cursor-pointer shadow-sm"><img width="25" src="{{asset('assets/img/logo/edit.png')}}" /></a>
                  <a onclick="confirmDelete(event, '{{json_encode($d)}}',  `{{ route('admin.tiket.hapus', ['id' => $d->id])}}`)" class="cursor-pointer shadow-sm">
                     <img width="25" src="{{asset('assets/img/logo/delete.png')}}" />
                  </a>
               </td>
            </tr>

            @endforeach
         </tbody>
      </table>


      <script>
         function confirmDelete(event, data, rute) {
            const el = document.getElementById('modal-confirmation-container');
            document.getElementById('modal-confirmation-title').textContent = "Konfirmasi Hapus Tiket"
            const modalBody = document.getElementById('modal-confirmation-body');
            el.classList.remove("d-none");
            el.classList.add("d-flex")
            data = JSON.parse(data);
            console.log(rute)
            modalBody.innerHTML = "Konfirmasi hapus destinasi pada id " + data['id'];

            const modalTarget = document.getElementById('modal-confirmation-target');
            modalTarget.classList.remove("bg-primary");
            modalTarget.classList.add("bg-danger");

            const modalForm = document.getElementById('modal-confirmation-form');
            modalForm.action = rute;
         }
      </script>
   </div>
</div>

@endsection