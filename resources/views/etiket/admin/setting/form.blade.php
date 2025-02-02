@extends('etiket.admin.template.index')

@section('css')
@endsection

@section('main')
<a class="btn btn-primary w-fit text-start mb-3" href="{{ route('admin.setting') }}">
   <i class="ti ti-arrow-left"></i>
   Kembali
</a>

<div class="card">
   <div class="card-body">
      <label class="text-2xl font-bold gk-text-base-black mb-2">{{ $title }}</label>

      <!-- Hidden ID -->

      <form method="post" action="{{ $action }}" class="mt-2">
         @csrf
         <input type="hidden" name="id" value="{{ $var->id ?? '' }}">

         <!-- Nama Variabel -->
         <div class="form-group mb-3">
            <label for="nama">Nama Variabel</label>
            <input type="text" class="form-control" id="nama" name="nama"
               value="{{ old('nama', $var->nama ?? '') }}"
               placeholder="Masukkan nama variabel" required>
         </div>

         <!-- Text1 -->
         <div class="form-group mb-3">
            <label for="text1">Text1</label>
            <input type="text" class="form-control" id="text1" name="text1"
               value="{{ old('text1', $var->text1 ?? '') }}"
               placeholder="Masukkan isian text1">
         </div>

         <!-- Text2 -->
         <div class="form-group mb-3">
            <label for="text2">Text2</label>
            <input type="text" class="form-control" id="text2" name="text2"
               value="{{ old('text2', $var->text2 ?? '') }}"
               placeholder="Masukkan isian text2">
         </div>

         <!-- Submit Button -->
         <div class="form-group">
            <button type="submit" class="btn btn-primary d-block ms-auto">Simpan</button>
         </div>
      </form>

   </div>
</div>

@endsection

@section('js')
@endsection