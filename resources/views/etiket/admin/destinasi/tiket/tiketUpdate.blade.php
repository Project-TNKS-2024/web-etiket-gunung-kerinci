@extends('etiket.admin.template.index')

@section('css')
@endsection

@section('main')
<a class="btn btn-primary w-fit text-start mb-3" href="{{ route('admin.destinasi.tiket', ['id' => $destinasi->id ]) }}">
   <i class="ti ti-arrow-left"></i>
   Kembali
</a>
<div class="card">
   <div class="card-header">
      <h5><b>{{ isset($tiket) ? 'Edit Tiket' : 'Tambah Tiket' }}</b></h5>
   </div>
   <div class="card-body">
      <form action="{{ isset($tiket) ? route('admin.destinasi.tiket.uppdateAction') : route('admin.destinasi.tiket.addAction') }}" method="post">
         @csrf
         @if(isset($tiket))
         <input type="hidden" name="id_tiket" value="{{ $tiket->id }}">
         @else
         <input type="hidden" name="id_destinasi" value="{{$destinasi->id}}">
         @endif

         <div class="row">
            <div class="col-md-6 mb-3">
               <label for="destinasi" class="form-label">Destinasi</label>
               <input type="text" id="destinasi" name="destinasi" class="form-control" value="{{ $destinasi->nama }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
               <label for="nama_paket" class="form-label">Nama Paket</label>
               <input type="text" id="nama_paket" name="nama_paket" class="form-control" value="{{ $tiket->nama ?? '' }}" placeholder="Masukkan nama paket">
            </div>

            <div class="col-md-6 mb-3">
               <label for="min_pendaki" class="form-label">Minimal Pendaki</label>
               <input type="number" id="min_pendaki" name="min_pendaki" class="form-control" value="{{ $tiket->min_pendaki ?? '' }}" placeholder="Masukkan jumlah minimal pendaki">
            </div>

            <div class="col-md-6 mb-3">
               <label for="title_penugasan" class="form-label">Title Penugasan</label>
               <input type="text" id="title_penugasan" name="title_penugasan" class="form-control" value="{{ $tiket->penugasan ?? '' }}" placeholder="Masukkan title penugasan">
            </div>

            <div class="col-12 mb-3">
               <label for="keterangan" class="form-label">Keterangan</label>
               <textarea id="keterangan" name="keterangan" class="form-control" placeholder="Masukkan keterangan" rows="3">{{ $tiket->keterangan ?? '' }}</textarea>
            </div>
         </div>

         @foreach(['wni', 'wna'] as $kategori)
         @php
         $harga_tiket = isset($tiket) ? $tiket->tiket_pendaki->firstWhere('kategori_pendaki', $kategori) : null;
         @endphp
         <h5 class="font-bold mb-3">Harga Tiket ({{ strtoupper($kategori) }})</h5>
         <input type="hidden" name="id_tiket_{{$kategori}}" value="{{ $harga_tiket->id ?? '' }}">
         <div class="row">
            <div class="col-md-6 mb-3">
               <label class="form-label">Harga Tiket Masuk (Weekend)</label>
               <input type="number" name="harga_masuk_weekend_{{ $kategori }}" class="form-control" value="{{ $harga_tiket->harga_masuk_wk ?? '' }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">Harga Tiket Masuk (Weekday)</label>
               <input type="number" name="harga_masuk_weekday_{{ $kategori }}" class="form-control" value="{{ $harga_tiket->harga_masuk_wd ?? '' }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">Harga Tiket Kemah</label>
               <input type="number" name="harga_kemah_{{ $kategori }}" class="form-control" value="{{ $harga_tiket->harga_kemah ?? '' }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">Harga Tiket Tracking</label>
               <input type="number" name="harga_tracking_{{ $kategori }}" placeholder="Harga Tiket Tracking" class="form-control" value="{{ $harga_tiket->harga_traking ?? '' }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">Harga Asuransi</label>
               <input type="number" name="harga_asuransi_{{ $kategori }}" class="form-control" value="{{ $harga_tiket->harga_ansuransi ?? '' }}">
            </div>
            <div class="col-md-6 mb-3">
               <label class="form-label">Masa Ansuransi (hari)</label>
               <input type="number" name="masa_asuransi_{{ $kategori }}" class="form-control" value="{{ $harga_tiket->masa_ansuransi ?? '' }}">
            </div>
         </div>
         @endforeach

         <div class="col-12 mt-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">{{ isset($tiket) ? 'Update Tiket' : 'Tambah Tiket' }}</button>
         </div>
      </form>
   </div>
</div>
@endsection

@section('js')
@endsection