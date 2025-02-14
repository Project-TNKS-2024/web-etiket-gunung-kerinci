@extends('etiket.admin.template.index')

@section('css')

<style>

</style>

@endsection

@section('main')

<a class="btn btn-primary w-fit text-start mb-3" href="{{ route('admin.destinasi.detail', ['id' => $destinasi->id]) }}">
   <i class="ti ti-arrow-left"></i>
   Kembali
</a>

<div class="card">
   <div class="card-header">
      <h5><b>Update Gates</b></h5>
   </div>
   <div class="card-body">
      <form action="{{ route('admin.destinasi.gates.updateAction') }}" method="post" enctype="multipart/form-data">
         @csrf
         <div class="row">
            <!-- Nama -->
            <div class="mb-3 col-md-6">
               <label for="nama" class="form-label">Nama</label>
               <input type="hidden" name="id_gate" value="{{ $gate->id }}">
               <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $gate->nama) }}" required>
            </div>

            <!-- Status -->
            <div class="mb-3 col-md-6">
               <label for="status" class="form-label">Status</label>
               <select class="form-select" id="status" name="status" required>
                  <option value="">Pilih Status</option>
                  <option value="1" {{ old('status', $gate->status) == '1' ? 'selected' : '' }}>Aktif</option>
                  <option value="0" {{ old('status', $gate->status) == '0' ? 'selected' : '' }}>Non-Aktif</option>
               </select>
            </div>

            <!-- Max Pendaki per Hari -->
            <div class="mb-3 col-md-6">
               <label for="max_pendaki_hari" class="form-label">Max Pendaki per Hari</label>
               <input type="number" class="form-control" id="max_pendaki_hari" name="max_pendaki_hari" value="{{ old('max_pendaki_hari', $gate->max_pendaki_hari) }}" required>
            </div>

            <!-- Min Pendaki per Booking -->
            <div class="mb-3 col-md-6">
               <label for="min_pendaki_booking" class="form-label">Min Pendaki per Booking</label>
               <input type="number" class="form-control" id="min_pendaki_booking" name="min_pendaki_booking" value="{{ old('min_pendaki_booking', $gate->min_pendaki_booking) }}" required>
            </div>

            <!-- Lokasi -->
            <div class="mb-3 col-md-6">
               <label for="lokasi" class="form-label">Lokasi</label>
               <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ old('lokasi', $gate->lokasi) }}" required>
            </div>

            <!-- Lokasi Maps -->
            <div class="mb-3 col-md-6">
               <label for="lokasi_maps" class="form-label">Lokasi Maps</label>
               <input type="text" class="form-control" id="lokasi_maps" name="lokasi_maps" value="{{ old('lokasi_maps', $gate->lokasi_maps) }}">
            </div>
         </div>

         <div class="mb-3">
            <label for="qris" class="form-label">Tampilan QRIS</label>
            <div class="input-group flex-nowrap">
               <input class="form-control" type="file" name="qris" id="qris" accept="image/*,.pdf">
               @if (isset($gate->qris) && file_exists(public_path($gate->qris)))
               <input type="hidden" value="{{asset( $gate->qris)}}" id="qris_existing">
               @endif
               <button class="input-group-text d-none" type="button" data-id-target="qris">
                  <i class=" fa-regular fa-eye"></i>
               </button>
            </div>
            <span class="keterangan" style="font-size: 12px;">Gambar Qris, Max 1Mb</span>
         </div>

         <!-- Detail -->
         <div class="mb-3">
            <label for="detail" class="form-label">Detail</label>
            <textarea class="form-control" id="detail" name="detail" rows="3">{{ old('detail', $gate->detail) }}</textarea>
         </div>

         <!-- Submit Button -->
         <button type="submit" class="btn btn-primary d-block ms-auto">Simpan</button>
      </form>
   </div>
</div>

@endsection

@section('js')
<!-- script modal show file -->
@include('homepage.template.modal-prefiewFile')
@endsection