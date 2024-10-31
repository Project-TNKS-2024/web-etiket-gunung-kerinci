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
         <a class="btn btn-primary text-black gk-bg-base-white"
            href="{{route('admin.destinasi.tiket.add', ['id' => $id_destinasi])}}">
            <img src="{{asset('assets/icon/tnks-plus.svg')}}" width="20" style="margin-right: 5px;" />
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
                  <a href="{{route('admin.destinasi.tiket.update', ['id' => $d->id])}}" class="cursor-pointer shadow-sm"><img width="25" src="{{asset('assets/img/logo/edit.png')}}" /></a>
                  <a href="#" class="cursor-pointer shadow-sm"
                     data-bs-toggle="modal"
                     data-bs-target="#ModalDelete"
                     data-bs-action="{{ route('admin.destinasi.tiket.deleteAction')}}"
                     data-bs-input-hidden-id_tiket="{{$d->id}}"
                     data-bs-title="Konfirmasi Hapus Tiket"
                     data-bs-body="Konfirmasi hapus destinasi pada id {{$d->id}}">
                     <img width="25" src="{{asset('assets/img/logo/delete.png')}}" />
                  </a>

               </td>
            </tr>

            @endforeach
         </tbody>
      </table>

   </div>
</div>

@endsection