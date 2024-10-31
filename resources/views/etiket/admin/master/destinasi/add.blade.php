@extends('etiket.admin.template.index')

@section('css')
<style>
   /* Tambahkan CSS kustom jika diperlukan */
</style>
@endsection

@section('main')
<a class="btn btn-primary w-fit text-start mb-3" href="{{route('admin.master.destinasi')}}">
   <i class="ti ti-arrow-left"></i>
   Kembali
</a>

<div class="card">
   <div class="card-body">
      <label class="text-2xl font-bold gk-text-base-black mb-2">Daftar Destinasi</label>

      <form action="{{ route('admin.master.destinasi.addAction')}}" method="POST">
         @csrf
         <div class="row">
            <!-- Nama -->
            <div class="form-group col-12 mb-3">
               <label for="nama">Nama Destinasi</label>
               <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama destinasi" required>
            </div>

            <!-- Status dan Kategori dalam satu baris -->
            <div class="form-group col-md-6 mb-3">
               <label for="status">Status</label>
               <select class="form-control" id="status" name="status" required>
                  <option value="">Pilih Status</option>
                  <option value="1">Aktif</option>
                  <option value="0">Nonaktif</option>
               </select>
            </div>

            <div class="form-group col-md-6 mb-3">
               <label for="kategori">Kategori</label>
               <select class="form-control" id="kategori" name="kategori" required>
                  <option value="">Pilih Kategori</option>
                  <option value="gunung">Gunung</option>
                  <option value="taman">Taman</option>
                  <!-- Tambahkan kategori lainnya sesuai kebutuhan -->
               </select>
            </div>

            <!-- Lokasi -->
            <div class="form-group col-12 mb-3">
               <label for="lokasi">Lokasi</label>
               <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Masukkan lokasi destinasi" required>
            </div>

            <!-- Detail -->
            <div class="form-group col-12 mb-3">
               <label for="detail">Detail Destinasi</label>
               <textarea class="form-control" id="detail" name="detail" rows="4" placeholder="Masukkan detail mengenai destinasi" required></textarea>
            </div>

            <!-- Submit Button -->
            <div class="form-group col-12">
               <button type="submit" class="btn btn-primary d-block ms-auto">Simpan</button>
            </div>
         </div>
      </form>
   </div>
</div>
@endsection