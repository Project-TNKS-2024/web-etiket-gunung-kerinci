@extends('etiket.admin.template.index')

@section('css')

<style>

</style>

@endsection

@section('main')

<div class="card">
   <div class="card-header ">
      <h3 class="mb-0"><b>Daftar Destinasi</b></h3>
   </div>
   <div class="card-body">
      <div class="table-responsive">
         <table class="table table-bordered">
            <thead class="bg-dark text-white">
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
                  <td>

                     <a href="{{ route('admin.destinasi.detail', ['id' => $d->id]) }}" class="btn btn-primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                     </a>
                     <form action="{{ route('admin.master.destinasi.deleteAction')}}" method="post">
                        @csrf
                        <input type="hidden" name="id_destinasi" value="{{$d->id}}">

                        <button type="submit" class="btn btn-danger mt-1" onclick="openswal(event, this)">
                           <i class="fa-solid fa-trash"></i>
                        </button>
                     </form>

                     <button onclick="openModal({{ json_encode($gambar) }}.filter(o => o.id_destinasi === {{$d->id}}))" class="btn btn-warning mt-1">
                        <i class="fa-solid fa-file-image"></i>
                     </button>
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>

      <div class="d-flex justify-content-end">
         <a class="btn btn-primary"
            href="{{route('admin.master.destinasi.add')}}">
            Tambah Destinasi
         </a>
      </div>
   </div>
</div>

<!-- Modal Fullscreen dengan Carousel -->
<div id="imageModal" class="modal fade" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog modal-fullscreen">
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
   function openModal(gambar) {
      console.log(gambar);

      const modalGallery = document.getElementById('modalGallery');
      modalGallery.innerHTML = ''; // Kosongkan carousel sebelum menampilkan gambar baru

      if (gambar.length === 0) {
         modalGallery.innerHTML = '<div class="text-center text-white p-5">Tidak ada gambar untuk destinasi ini.</div>';
      } else {
         gambar.forEach((img, index) => {
            const activeClass = index === 0 ? 'active' : ''; // Gambar pertama dibuat aktif
            const carouselItem = document.createElement('div');
            // carouselItem.className = `carousel-item ${activeClass} d-flex align-items-center justify-content-center`;
            carouselItem.className = `carousel-item ${activeClass} text-center`;

            const imgElement = document.createElement('img');
            imgElement.src = `${window.location.origin}/${img.src}`;
            imgElement.className = "img-fluid";
            imgElement.style = "max-height: 90vh; max-width: 100%;";
            imgElement.alt = "Gambar Destinasi";

            carouselItem.appendChild(imgElement);
            modalGallery.appendChild(carouselItem);
         });
      }

      // **Inisialisasi ulang Carousel agar bisa berfungsi dengan baik**
      const carousel = new bootstrap.Carousel(document.getElementById('modalGalleryCarousel'), {
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

@endsection