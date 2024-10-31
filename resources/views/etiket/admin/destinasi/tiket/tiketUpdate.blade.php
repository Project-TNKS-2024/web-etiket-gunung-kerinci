@extends('etiket.admin.template.index')

@section('css')

@endsection

@section('main')
<a class="btn btn-primary w-fit text-start mb-3" href="{{route('admin.destinasi.tiket', ['id' => $destinasi->id ])}}">
   <i class="ti ti-arrow-left"></i>
   Kembali
</a>
<div class="card">
   <div class="card-body">
      <div>
         <label class="text-2xl font-bold gk-text-base-black mb-2">Daftar Tiket</label>

         <form action="{{ route('admin.destinasi.tiket.uppdateAction') }}" method="post">
            @csrf
            <input type="hidden" name="id_tiket" value="{{ $tiket->id }}">

            <!-- Destinasi (Read-only) -->
            <div class="row">
               <div class="col-md-6 mb-3">
                  <label for="destinasi" class="form-label">Destinasi</label>
                  <input type="text" id="destinasi" name="destinasi" class="form-control" value="{{ $destinasi->nama }}" readonly>
               </div>

               <!-- Nama Paket (Text) -->
               <div class="col-md-6 mb-3">
                  <label for="nama_paket" class="form-label">Nama Paket</label>
                  <input type="text" id="nama_paket" name="nama_paket" class="form-control" value="{{ $tiket->nama }}" placeholder="Masukkan nama paket">
               </div>

               <!-- Minimal Pendaki (Number) -->
               <div class="col-md-6 mb-3">
                  <label for="min_pendaki" class="form-label">Minimal Pendaki</label>
                  <input type="number" id="min_pendaki" name="min_pendaki" class="form-control" value="{{ $tiket->min_pendaki }}" placeholder="Masukkan jumlah minimal pendaki">
               </div>

               <!-- Title Penugasan (Text) -->
               <div class="col-md-6 mb-3">
                  <label for="title_penugasan" class="form-label">Title Penugasan</label>
                  <input type="text" id="title_penugasan" name="title_penugasan" class="form-control" value="{{ $tiket->penugasan }}" placeholder="Masukkan title penugasan">
               </div>

               <!-- Keterangan (Text) -->
               <div class="col-12 mb-3">
                  <label for="keterangan" class="form-label">Keterangan</label>
                  <textarea id="keterangan" name="keterangan" class="form-control" placeholder="Masukkan keterangan" rows="3">{{ $tiket->keterangan }}</textarea>
               </div>

               <!-- Harga Tiket (WNI) -->
               <div class="col-md-6">
                  <h5 class="text-2x1 font-bold gk-text-base-black mb-3">Harga Tiket (WNI)</h5>
                  @php
                  $wni_tiket = $tiket->tiket_pendaki->firstWhere('kategori_pendaki', 'wni');
                  @endphp
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <input type="hidden" name="id_tiket_wni" value="{{$wni_tiket->id}}">
                        <label for="harga_masuk_weekend_wni" class="form-label">Harga Tiket Masuk (Weekend)</label>
                        <input type="number" id="harga_masuk_weekend_wni" name="harga_masuk_weekend_wni" class="form-control" placeholder="Harga Weekend" value="{{ $wni_tiket ? $wni_tiket->harga_masuk_wk : '' }}">
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="harga_masuk_weekday_wni" class="form-label">Harga Tiket Masuk (Weekday)</label>
                        <input type="number" id="harga_masuk_weekday_wni" name="harga_masuk_weekday_wni" class="form-control" placeholder="Harga Weekday" value="{{ $wni_tiket ? $wni_tiket->harga_masuk_wd : '' }}">
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="harga_kemah_wni" class="form-label">Harga Tiket Kemah</label>
                        <input type="number" id="harga_kemah_wni" name="harga_kemah_wni" class="form-control" placeholder="Harga Tiket Kemah" value="{{ $wni_tiket ? $wni_tiket->harga_kemah : '' }}">
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="harga_tracking_wni" class="form-label">Harga Tiket Tracking</label>
                        <input type="number" id="harga_tracking_wni" name="harga_tracking_wni" class="form-control" placeholder="Harga Tiket Tracking" value="{{ $wni_tiket ? $wni_tiket->harga_traking : '' }}">
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="harga_asuransi_wni" class="form-label">Harga Asuransi</label>
                        <input type="number" id="harga_asuransi_wni" name="harga_asuransi_wni" class="form-control" placeholder="Harga Asuransi" value="{{ $wni_tiket ? $wni_tiket->harga_ansuransi : '' }}">
                     </div>
                  </div>
               </div>

               <!-- Harga Tiket (WNA) -->
               <div class="col-md-6">
                  <h5 class="text-2x1 font-bold gk-text-base-black mb-3">Harga Tiket (WNA)</h5>
                  @php
                  $wna_tiket = $tiket->tiket_pendaki->firstWhere('kategori_pendaki', 'wna');
                  @endphp
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <input type="hidden" name="id_tiket_wna" value="{{$wna_tiket->id}}">
                        <label for="harga_masuk_weekend_wna" class="form-label">Harga Tiket Masuk (Weekend)</label>
                        <input type="number" id="harga_masuk_weekend_wna" name="harga_masuk_weekend_wna" class="form-control" placeholder="Harga Weekend" value="{{ $wna_tiket ? $wna_tiket->harga_masuk_wk : '' }}">
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="harga_masuk_weekday_wna" class="form-label">Harga Tiket Masuk (Weekday)</label>
                        <input type="number" id="harga_masuk_weekday_wna" name="harga_masuk_weekday_wna" class="form-control" placeholder="Harga Weekday" value="{{ $wna_tiket ? $wna_tiket->harga_masuk_wd : '' }}">
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="harga_kemah_wna" class="form-label">Harga Tiket Kemah</label>
                        <input type="number" id="harga_kemah_wna" name="harga_kemah_wna" class="form-control" placeholder="Harga Tiket Kemah" value="{{ $wna_tiket ? $wna_tiket->harga_kemah : '' }}">
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="harga_tracking_wna" class="form-label">Harga Tiket Tracking</label>
                        <input type="number" id="harga_tracking_wna" name="harga_tracking_wna" class="form-control" placeholder="Harga Tiket Tracking" value="{{ $wna_tiket ? $wna_tiket->harga_traking : '' }}">
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="harga_asuransi_wna" class="form-label">Harga Asuransi</label>
                        <input type="number" id="harga_asuransi_wna" name="harga_asuransi_wna" class="form-control" placeholder="Harga Asuransi" value="{{ $wna_tiket ? $wna_tiket->harga_ansuransi : '' }}">
                     </div>
                  </div>
               </div>

               <!-- Submit Button -->
               <div class="col-12 mt-4 d-flex justify-content-end">
                  <button type="submit" class="btn btn-primary">Update Tiket</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
@endsection

@section('js')

@endsection