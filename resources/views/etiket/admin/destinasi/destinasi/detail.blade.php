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

<div class="card mb-3">
   <div class="card-header">
      <h3 class="mb-0"><b>Destinasi : {{$destinasi->nama}}</b></h3>
   </div>
</div>

<div class="card">
   <div class="card-header ">
      <h5 class="mb-0"><b>Detail Destinasi </b></h5>
   </div>
   <div class="card-body">
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
                  <option selected>{{$destinasi->getStatus()}}</option>
               </select>
            </fieldset>
         </div>
      </div>

      <div class="d-flex justify-content-end mt-2">
         <a class="btn btn-primary" href="{{route('admin.destinasi.update', ['id' => $destinasi->id])}}">
            <i class="fa-solid fa-pen-to-square me-2"></i> Edit
         </a>
      </div>
   </div>
</div>

<div class="card">
   <div class="card-header ">
      <h5><b>Daftar Gates</b></h5>
   </div>
   <div class="card-body">
      <div class="table-responsive">
         <table class="table table-bordered">
            <thead class="bg-dark text-white">
               <tr>
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
                  <td><b>{{$g->nama}}</b></td>
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
                     <form action="{{ route('admin.destinasi.gates.deleteAction')}}" method="post">
                        @csrf
                        <input type="hidden" name="id_destinasi" value="{{$destinasi->id}}">
                        <input type="hidden" name="id_gate" value="{{$g->id}}">

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
      <div class="d-flex justify-content-end">
         <a class="btn btn-primary"
            data-bs-toggle="collapse" href="#CollapseTambahGates">
            <i class="fa-solid fa-plus me-2"></i>Tambah
         </a>
      </div>
   </div>
</div>

<div class="card collapse" id="CollapseTambahGates">
   <div class="card-header ">
      <h5><b>Tambah Gates</b></h5>
   </div>
   <div class="card-body">
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
            <label for="detail" class="form-label">
               Tampilan QRIS
               <span type="span" class="text-secondary"
                  data-bs-toggle="popover"
                  data-bs-placement="right"
                  data-bs-trigger="hover focus"
                  data-bs-content="Silakan unggah foto dalam format JPG atau PNG. Max 2Mb">
                  <i class="fa-regular fa-circle-question"></i>
               </span>
            </label>
            <div class="input-group flex-nowrap">
               <input type="file" class="form-control" id="qris" name="qris">
               <button class="input-group-text d-none" type="button" data-id-target="qris">
                  <i class="fa-regular fa-eye"></i>
               </button>
            </div>
         </div>

         <!-- Submit Button -->
         <button type="submit" class="btn btn-primary d-block ms-auto">
            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan
         </button>
      </form>
   </div>
</div>

<div class="card">
   <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><b>Daftar Gambar</b></h5>
   </div>
   <div class="card-body">
      <div class="row">
         @if(count($gambar) == 0)
         <div class="col-12 text-center text-muted">Belum Ada Gambar</div>
         @else
         @foreach ($gambar as $g)
         <div class="col-md-3 mb-3">
            <div class="card shadow-sm position-relative">
               <img src="{{ asset($g->src) }}" class="card-img-top" alt="{{ $g->nama }}" onclick="openModal({{ json_encode($gambar) }}, {{$g->id}})" style="cursor: pointer;">
               <form action="{{route('admin.destinasi.picture.deleteAction')}}" method="post">
                  @csrf
                  <input type="hidden" name="id_destinasi" value="{{$destinasi->id}}">
                  <input type="hidden" name="id_gambar" value="{{$g->id}}">
                  <button type="submit" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="openswal(event, this)">
                     <i class=" fa-solid fa-trash"></i>
                  </button>
               </form>
               <div class="card-body text-center py-2">
                  <p class="fw-bold mb-1 text-truncate" title="{{ $g->nama }}">{{ $g->nama }}</p>
               </div>
            </div>
         </div>
         @endforeach
         @endif
      </div>
      <div class="d-flex justify-content-end">
         <a class="btn btn-primary"
            data-bs-toggle="collapse" href="#CollapseTambahGates">
            <i class="fa-solid fa-plus me-2"></i>Tambah
         </a>

      </div>
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
               <input class="form-control" name="foto_nama" id="foto-nama" placeholder="Judul Foto" value="" required />
            </div>
            <div class="col-6">
               <label class="form-label" for="gate-foto">
                  Upload Foto
                  <span type="span" class="text-secondary"
                     data-bs-toggle="popover"
                     data-bs-placement="right"
                     data-bs-trigger="hover focus"
                     data-bs-content="Silakan unggah foto dalam format JPG atau PNG. Max 5Mb">
                     <i class="fa-regular fa-circle-question"></i>
                  </span>
               </label>
               <div class="input-group flex-nowrap">
                  <input type="file" class="form-control" id="foto" name="foto">
                  <button class="input-group-text d-none" type="button" data-id-target="foto">
                     <i class="fa-regular fa-eye"></i>
                  </button>
               </div>

            </div>
         </div>
         <div class="col-12">
            <label class="form-label">Detail Foto</label>
            <textarea cols="1" name="foto_detail" style="height: 130px;" id="foto-detail" class="form-control" placeholder="Detail Foto" required></textarea>
         </div>
         <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
               <i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload
            </button>
         </div>
      </form>
   </div>
</div>


<!-- Modal Fullscreen dengan Carousel -->
<div id="imageModal" class="modal fade" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog modal-fullscreen m-0">
      <div class="modal-content bg-transparent">
         <div class="modal-header bg-white">
            <h5 class="modal-title">Galeri Destinasi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body p-0">
            <div id="modalGalleryCarousel" class="carousel slide">
               <div class="carousel-inner" id="modalGallery">
                  <!-- Gambar akan dimasukkan dengan JavaScript -->
               </div>
               <button class="carousel-control-prev" type="button" data-bs-target="#modalGalleryCarousel" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
               </button>
               <button class="carousel-control-next" type="button" data-bs-target="#modalGalleryCarousel" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
               </button>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('js')
<script>
   function openModal(gambarList, clickedId) {

      const modalGallery = document.getElementById('modalGallery');
      modalGallery.innerHTML = ''; // Kosongkan carousel sebelum menambahkan gambar baru

      if (gambarList.length === 0) {
         modalGallery.innerHTML = `
            <div class="carousel-item active text-center">
               <p class="text-white p-5">Tidak ada gambar untuk destinasi ini.</p>
            </div>`;
      } else {
         // **Cari index gambar yang diklik dalam daftar**
         let clickedIndex = gambarList.findIndex(img => img.id == clickedId);
         console.log(clickedIndex);
         if (clickedIndex === -1) clickedId = gambarList[0].id; // Jika tidak ditemukan, gunakan indeks pertama

         // **Tambahkan gambar ke dalam carousel**
         gambarList.forEach((img, index) => {
            const activeClass = (img.id === clickedId) ? 'active' : ''; // Gambar pertama dibuat aktif
            modalGallery.innerHTML += `
               <div class="carousel-item ${activeClass} text-center">
                  <img src="${window.location.origin}/${img.src}" 
                       class="img-fluid" 
                       style="max-height: 90vh; max-width: 100%;" 
                       alt="${img.nama}">
                  <p class="text-white mt-2">${img.nama}</p>
               </div>`;
         });
      }

      // **Inisialisasi ulang Carousel setiap kali modal dibuka**
      let carouselElement = document.getElementById('modalGalleryCarousel');
      new bootstrap.Carousel(carouselElement, {
         interval: false, // Hanya berpindah saat tombol diklik
         wrap: true // Bisa looping dari terakhir ke pertama
      });

      // Tampilkan modal
      const modal = new bootstrap.Modal(document.getElementById('imageModal'));
      modal.show();
   }
</script>

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

<!-- script modal show file -->
@include('homepage.template.modal-prefiewFile')

@endsection