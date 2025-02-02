@extends('etiket.admin.template.index')

@section('css')

<style>

</style>

@endsection

@section('main')

<div class="card">
   <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
         <label class="text-2xl font-bold gk-text-base-black mb-2">Daftar Destinasi</label>
         <a class="btn btn-primary text-black gk-bg-base-white"
            href="{{route('admin.master.destinasi.add')}}">
            <img src="{{asset('assets/icon/tnks-plus.svg')}}" width="20" style="margin-right: 5px;" />
            Tambah Destinasi
         </a>
      </div>

      <table class="w-full">
         <thead>
            <tr>
               <th class="p-3 gk-bg-base-white font-bold">Status</th>
               <th class="p-3 gk-bg-base-white font-bold">Nama</th>
               <th class="p-3 gk-bg-base-white font-bold">Kategori</th>
               <th class="p-3 gk-bg-base-white font-bold">Lokasi</th>
               <th class="p-3 gk-bg-base-white font-bold">Detail</th>
               <th class="p-3 gk-bg-base-white font-bold">Aksi</th>
            </tr>
         </thead>
         <tbody>
            @foreach ($destinasi as $d)
            <tr class="tiket-row">
               <td class="p-3 ">
                  @if ($d->status)
                  <span class="badge rounded-pill text-bg-success">Open</span>
                  @else

                  <span class="badge rounded-pill gk-bg-error200">Close</span>

                  @endif
               </td>
               <td class="p-3 ">{{$d->nama}}</td>
               <td class="p-3 ">{{$d->kategori}}</td>
               <td class="p-3 ">{{$d->lokasi}}</td>
               <td class="p-3">
                  <div style="display: -webkit-box;-webkit-box-orient: vertical;overflow: hidden;text-overflow: ellipsis;-webkit-line-clamp: 15;">
                     {!!$d->detail!!}
                  </div>
               </td>
               <td class="p-3 d-flex gap-1 bg-transparent align-items-center justify-content-start">

                  <a href="{{ route('admin.destinasi.detail', ['id' => $d->id]) }}" class="cursor-pointer">
                     <img width="25" class="gk-bg-primary100 rounded shadow-sm" src="{{asset('assets/icon/tnks-pen.svg')}}" class="bg-transparent" />
                  </a>
                  <a href="#" class="cursor-pointer shadow-sm"
                     data-bs-toggle="modal"
                     data-bs-target="#ModalDelete"
                     data-bs-action="{{ route('admin.master.destinasi.deleteAction')}}"
                     data-bs-input-hidden-id_destinasi="{{$d->id}}"
                     data-bs-title="Konfirmasi Hapus Destinasi"
                     data-bs-body="Konfirmasi hapus destinasi {{$d->nama}}?">
                     <img width="25" src="{{asset('assets/img/logo/delete.png')}}" />
                  </a>
                  <div onclick="openModal({{ json_encode($gambar) }}.filter(o => o.id_destinasi === {{$d->id}}))">
                     <!-- <img class="gk-bg-success100 rounded shadow-sm" width="25" src="{{asset('assets/icon/tnks-detail.svg')}}" /> -->
                     <img class="gk-bg-success100 rounded " width="25" src="{{asset('assets/icon/tnks/img_rol-dark.svg')}}" />
                  </div>
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>

@endsection