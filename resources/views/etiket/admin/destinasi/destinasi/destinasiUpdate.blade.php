@extends('etiket.admin.template.index')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<style>
   .ql-editor {
      font-size: 15px !important;
   }

   .ql-container {
      min-height: 200px;
      /* Atur tinggi minimum */
      overflow-y: auto;
      /* Tambahkan scroll jika isi terlalu panjang */
   }
</style>
@endsection

@section('main')

<a class="btn btn-primary w-fit text-start mb-3" href="{{ route('admin.destinasi.detail', ['id' => $destinasi->id]) }}">
   <i class="ti ti-arrow-left"></i>
   Kembali
</a>

<div class="card">
   <div class="card-header">
      <h5><b>Update Destinasi</b></h5>
   </div>
   <div class="card-body">
      <form id="destinasiForm" class="row gap-2" action="{{ route('admin.destinasi.update.action', ['id' => $destinasi->id]) }}" method="post">
         @csrf
         <div class="col-12">
            <label class="form-label">Nama Destinasi</label>
            <input class="form-control borderx bg-white" name="nama" id="destinasi-nama" value="{{ $destinasi->nama }}" required />
         </div>
         <div class="col-12 ">
            <div class="row">
               <div class="col-6">
                  <label class="form-label" for="kategori">Kategori</label>
                  <select class="form-control borderx bg-white" id="kategori" name="kategori" required>
                     <option value="taman" {{ $destinasi->kategori == 'taman' ? 'selected' : '' }}>Taman</option>
                     <option value="gunung" {{ $destinasi->kategori == 'gunung' ? 'selected' : '' }}>Gunung</option>
                  </select>
               </div>
               <div class="col-6">
                  <label class="form-label" for="status">Status</label>
                  <select class="form-control borderx bg-white" id="status" name="status" required>
                     <option value="2" {{ $destinasi->status == 2 ? 'selected' : '' }}>{{$destinasi->getStatus(2)}}</option>
                     <option value="1" {{ $destinasi->status == 1 ? 'selected' : '' }}>{{$destinasi->getStatus(1)}}</option>
                     <option value="0" {{ $destinasi->status == 0 ? 'selected' : '' }}>{{$destinasi->getStatus(0)}}</option>
                  </select>
               </div>
            </div>
         </div>
         <div class="col-12">
            <label class="form-label" for="lokasi">Lokasi</label>
            <input class="form-control borderx bg-white" name="lokasi" id="lokasi" placeholder="Lokasi Destinasi" value="{{ $destinasi->lokasi }}" required />
         </div>
         <div class="col-12">
            <label class="form-label">Detail</label>
            <div id="editor-detail" style="height: max-content;"></div>
            <textarea name="detail" id="destinasi-detail" class="form-control d-none">{{ $destinasi->detail }}</textarea>
         </div>
         <div class="col-12">
            <label class="form-label">SOP</label>
            <div id="editor-sop" style="height: max-content;"></div>
            <textarea name="sop" id="destinasi-sop" class="form-control d-none">{{ $destinasi->sop }}</textarea>
         </div>

         <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
               <i class="fa-solid fa-floppy-disk"></i>
               Simpan
            </button>
         </div>
      </form>
   </div>
</div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<script>
   const toolbarOptions = [
      ['bold', 'italic', 'underline', 'strike'],
      ['link'],
      // heading
      [{
         'header': [1, 2, 3, false]
      }],
      // background
      [{
         'background': []
      }],
      [{
         'list': 'ordered'
      }, {
         'list': 'bullet'
      }],
      [{
         'indent': '-1'
      }, {
         'indent': '+1'
      }],
      [{
         'color': []
      }],
      [{
         'align': []
      }],
      ['clean']
   ];

   // Inisialisasi Quill Editor untuk Detail
   const quillDetail = new Quill('#editor-detail', {
      theme: 'snow',
      placeholder: 'Tulis detail destinasi...',
      modules: {
         toolbar: toolbarOptions
      }
   });

   // Inisialisasi Quill Editor untuk SOP
   const quillSop = new Quill('#editor-sop', {
      theme: 'snow',
      placeholder: 'Tulis SOP destinasi...',
      modules: {
         toolbar: toolbarOptions
      }
   });

   // Ambil input tersembunyi untuk menyimpan isi editor sebelum submit
   const detailInput = document.getElementById('destinasi-detail');
   const sopInput = document.getElementById('destinasi-sop');

   // Masukkan data lama ke dalam editor jika ada
   quillDetail.root.innerHTML = detailInput.value;
   quillSop.root.innerHTML = sopInput.value;

   // Saat form dikirim, simpan nilai Quill ke dalam input hidden
   document.getElementById('destinasiForm').addEventListener('submit', function() {
      detailInput.value = quillDetail.root.innerHTML;
      sopInput.value = quillSop.root.innerHTML;
   });
</script>
@endsection