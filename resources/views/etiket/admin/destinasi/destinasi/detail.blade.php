@extends('etiket.admin.template.index')

@section('css')
<style>
   fieldset:disabled input,
   fieldset:disabled select {
      pointer-events: none;
      background-color: #e9ecef;
   }
</style>
@endsection

@section('main')

<div class="card">
   <div class="card-body">
      <label class="text-2xl font-bold gk-text-base-black mb-2">Detail Destinasi</label>
      <div class="mb-2">
         <label class=" form-label">Nama Destinasi</label>
         <fieldset disabled>
            <input class="form-control borderx" name="nama" id="destinasi-nama" value="{{$destinasi->nama}}" readonly />
         </fieldset>
      </div>
      <div class="row">
         <div class="col-4">
            <label class="form-label" for="kategori">Kategori</label>
            <fieldset disabled>
               <select class="form-control borderx" id="kategori" name="kategori">
                  @if ($destinasi->kategori == "taman")
                  <option value="taman" selected>Taman</option>
                  <option value="gunung">Gunung</option>
                  @else
                  <option value="taman">Taman</option>
                  <option value="gunung" selected>Gunung</option>
                  @endif
               </select>
            </fieldset>
         </div>
         <div class="col-4">
            <label class="form-label" for="status">Status</label>
            <fieldset disabled>
               <select class="form-control borderx" id="status" name="status">
                  @if ($destinasi->status)
                  <option value="1" selected>Open</option>
                  <option value="0">Close</option>
                  @else
                  <option value="1">Open</option>
                  <option value="0" selected>Close</option>
                  @endif
               </select>
            </fieldset>
         </div>
      </div>

      <div class="d-flex justify-content-end mt-2">
         <a class="btn btn-primary" href="{{route('admin.destinasi.update', ['id' => $destinasi->id])}}">
            <img width="20" src="{{asset('assets/icon/tnks/save-light.svg')}}" />Edit
         </a>
      </div>
   </div>
</div>


<div class="card collapse" id="CollapseTambahGates">
   <div class="card-body">
      <h5 class="text-2x1 font-bold gk-text-base-black mb-3">Tambah Gates</h5>
      <form action="{{ route('admin.destinasi.gates.addAction') }}" method="POST" enctype="multipart/form-data">
         @csrf
         <div class="row">
            <!-- Nama -->
            <div class="mb-3 col-md-6">
               <label for="nama" class="form-label">Nama</label>
               <input type="hidden" name="id_destinasi" value="{{$destinasi->id}}">
               <input type="text" class="form-control" id="nama" name="nama" required>
            </div>

            <!-- Status -->
            <div class="mb-3 col-md-6">
               <label for="status" class="form-label">Status</label>
               <select class="form-select" id="status" name="status" required>
                  <option value="">Pilih Status</option>
                  <option value="1">Aktif</option>
                  <option value="0">Non-Aktif</option>
               </select>
            </div>

            <!-- Max Pendaki per Hari -->
            <div class="mb-3 col-md-6">
               <label for="max_pendaki_hari" class="form-label">Max Pendaki per Hari</label>
               <input type="number" class="form-control" id="max_pendaki_hari" name="max_pendaki_hari" required>
            </div>

            <!-- Min Pendaki per Booking -->
            <div class="mb-3 col-md-6">
               <label for="min_pendaki_booking" class="form-label">Min Pendaki per Booking</label>
               <input type="number" class="form-control" id="min_pendaki_booking" name="min_pendaki_booking" required>
            </div>

            <!-- Lokasi -->
            <div class="mb-3 col-md-6">
               <label for="lokasi" class="form-label">Lokasi</label>
               <input type="text" class="form-control" id="lokasi" name="lokasi" required>
            </div>

            <!-- Lokasi Maps -->
            <div class="mb-3 col-md-6">
               <label for="lokasi_maps" class="form-label">Lokasi Maps</label>
               <input type="text" class="form-control" id="lokasi_maps" name="lokasi_maps">
            </div>
         </div>

         <!-- Detail -->
         <div class="mb-3">
            <label for="detail" class="form-label">Detail</label>
            <textarea class="form-control" id="detail" name="detail" rows="3"></textarea>
         </div>

         <div class="mb-3">
            <label for="detail" class="form-label">Tampilan QRIS</label>
            <input type="file" class="form-control" id="qris" name="qris">
         </div>

         <!-- Submit Button -->
         <button type="submit" class="btn btn-primary d-block ms-auto">Simpan</button>
      </form>
   </div>
</div>


<div class="card">
   <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
         <h5 class="text-2x1 font-bold gk-text-base-black mb-0">Daftar Gates</h5>
         <a class="btn btn-primary btn-sm text-black gk-bg-base-white"
            data-bs-toggle="collapse" href="#CollapseTambahGates">
            <img src="{{asset('assets/icon/tnks-plus.svg')}}" width="17" style="margin-right: 5px;" />
            Tambah
         </a>
      </div>
      <table class="rounded table table-striped table-bordered">
         <thead>
            <tr class="bg-white">
               <th>No</th>
               <th>Nama</th>
               <th>Status</th>
               <th>Max Pendaki/Hari</th>
               <th>Lokasi</th>
               <th>Detail</th>
               <th class="text-center">Aksi</th>
            </tr>
         </thead>
         <tbody class="table-group-divider">
            @foreach ($gates as $g)
            <tr class="tiket-row">
               <td>{{$loop->index+1}}</td>
               <td>{{$g->nama}}</td>
               <td class="p-3 ">
                  @if ($g->status)
                  <span class="badge rounded-pill text-bg-success">Open</span>
                  @else
                  <span class="badge rounded-pill gk-bg-error200">Close</span>
                  @endif
               </td>
               <td>{{$g->max_pendaki_hari}}</td>
               <td>{{$g->lokasi}}</td>
               <td>{{$g->detail}}</td>
               <td class="text-center">
                  <a href="{{route('admin.destinasi.gates.update', ['id' => $g->id])}}" class="btn btn-primary">
                     <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <a href="#" class="cursor-pointer shadow-sm"
                     data-bs-toggle="modal"
                     data-bs-target="#ModalDelete"
                     data-bs-action="{{route('admin.destinasi.gates.deleteAction')}}"
                     data-bs-input-hidden-id_destinasi="{{$destinasi->id}}"
                     data-bs-input-hidden-id_gate="{{$g->id}}"
                     data-bs-title="Konfirmasi Hapus Gambar {{$destinasi->nama}}"
                     data-bs-body="Konfirmasi hapus gambar {{$g->nama}}?">
                     <img width="25" src="{{asset('assets/img/logo/delete.png')}}" />
                  </a>
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>

<div class="card collapse" id="CollapseTambahFoto">
   <div class="card-body">
      <h5 class="text-2x1 font-bold gk-text-base-black mb-2">Tambah Foto</h5>
      <form class="form row gap-2" action="{{route('admin.destinasi.picture.addAction')}}" method="post" enctype="multipart/form-data">
         @csrf
         <div class="row">
            <div class="col-6">
               <label class="form-label">Judul Foto</label>
               <input type="hidden" name="id_destinasi" value="{{$destinasi->id}}">
               <input class="form-control borderx bg-white" name="foto_nama" id="foto-nama" placeholder="Judul Foto" value="" required />
            </div>
            <div class="col-6">
               <label class="form-label" for="gate-foto">Upload Foto</label>
               <div class="d-flex gap-2">
                  <label class="borderx form-control d-flex align-items-center w-fit p-0 py-2 px-2 bg-white cursor-pointer text-nowrap" for="gate-foto" style="user-select: none;width: -webkit-fill-available;border: 1px solid var(--neutrals500); overflow-x: hidden;">
                     <div class="m-0 p-0 pe-3 py-0 borderx gk-text-primary700 font-medium d-flex gap-1">
                        <img class="p-0 m-0" width="20" src="{{asset('assets/icon/tnks/upload.svg')}}" />
                        <div>Pilih Foto</div>
                     </div>
                     <div id="input-file-label" class="m-0 p-0 d-flex"> No File Chosen</div>
                  </label>
               </div>
               <input class="form-control borderx bg-white d-none" type="file" name="foto" id="gate-foto" accept="image/*" onchange="collectPics(event)" />
            </div>
         </div>
         <div class="col-12">
            <label class="form-label">Detail Foto</label>
            <textarea cols="1" name="foto_detail" style="height: 130px;" id="foto-detail" class="form-control bg-white borderx" placeholder="Detail Foto" required></textarea>
         </div>
         <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
               <img width="20" src="{{asset('assets/icon/tnks/save-light.svg')}}" style="margin-right: 5px;" />Upload
            </button>
         </div>
      </form>
   </div>
</div>

<div class="card">
   <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
         <h5 class="text-2x1 font-bold gk-text-base-black mb-0">Daftar Gambar</h5>
         <a class="btn btn-primary btn-sm text-black gk-bg-base-white"
            data-bs-toggle="collapse" href="#CollapseTambahFoto">
            <img src="{{asset('assets/icon/tnks-plus.svg')}}" width="17" style="margin-right: 5px;" />
            Tambah
         </a>
      </div>
      <table class="table table-striped table-bordered">
         <thead>
            <tr class="bg-white">
               <th class="col-1">No</th>
               <th class="col-4">Nama</th>
               <th class="col-4">Detail</th>
               <th class="col-2 text-center">Aksi</th>
            </tr>
         </thead>
         <tbody id="data-body" class="table-group-divider">
            @if(count($gambar) == 0)
            <tr>
               <td colspan="4">Belum Ada Gambar</td>
            </tr>
            @else
            @foreach ($gambar as $g)
            <tr class="tiket-row">
               <td class="col-1">{{$loop->index+1}}</td>
               <td class="col-4">{{$g->nama}}</td>
               <td class="col-4">{{$g->detail}}</td>
               <td class="d-flex gap-1 bg-transparent align-items-center justify-content-center">
                  <div onclick="openModal([ {{ $g }} ])" class="text-black h-fit d-flex align-items-center gap-1">
                     <img class="gk-bg-success100 rounded shadow-sm" width="25" src="{{asset('assets/icon/img_rol.svg')}}" />
                  </div>
                  <div class="text-black h-fit d-flex align-items-center gap-1">
                     <a href="#" class="cursor-pointer shadow-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#ModalDelete"
                        data-bs-action="{{route('admin.destinasi.picture.deleteAction')}}"
                        data-bs-input-hidden-id_destinasi="{{$destinasi->id}}"
                        data-bs-input-hidden-id_gambar="{{$g->id}}"
                        data-bs-title="Konfirmasi Hapus Gambar {{$destinasi->nama}}"
                        data-bs-body="Konfirmasi hapus gambar {{$g->nama}}?">
                        <img width="25" src="{{asset('assets/img/logo/delete.png')}}" />
                     </a>
                  </div>
               </td>
            </tr>
            @endforeach
            @endif
         </tbody>
      </table>
   </div>
</div>
@endsection

@section('js')
<script>
   // script input foto destinasi
   const selectedFiles = [];

   function collectPics(event) {
      const fileInput = event.target;
      if (fileInput.files.length > 0) {
         const file = fileInput.files[0];
         const inputFileLabel = document.getElementById("input-file-label");
         inputFileLabel.textContent = event.target.value.split("\\").slice(-1);
      }
   }
</script>
@endsection