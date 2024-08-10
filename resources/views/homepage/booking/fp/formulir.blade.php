@php
$index = $index ?? 0;
$title = ($index == 0) ? 'Biodata Ketua' : 'Biodata Anggota '.$index;
@endphp

<h1>{{$title}}</h1>
<input type="hidden" name="formulir[{{$index}}][id_pendaki]" value="{{$pendaki ? $pendaki->id : null}}">
<div class="row mb-3">
   <div class="col-12 col-md-6">
      <label for="kewarganegaraan" class="w-100 fw-bold mandatory">Jenis Kewarganegaraan</label>
      <div class="form-check form-check-inline">
         <input class="form-check-input" type="radio" name="formulir[{{$index}}][kewarganegaraan]" id="kewarganegaraan-1-{{$index}}" value="wni" {{!$pendaki ? "" : ($pendaki->kategori_pendaki == "wni" ? "checked" : "")}}>
         <label class="form-check-label" for="kewarganegaraan-1-{{$index}}">Warga Negara Indonesia</label>
      </div>
      <div class="form-check form-check-inline">
         <input class="form-check-input" type="radio" name="formulir[{{$index}}][kewarganegaraan]" id="kewarganegaraan-2-{{$index}}" value="wna" {{!$pendaki ? "" : ($pendaki->kategori_pendaki == "wna" ? "checked" : "")}}>
         <label class="form-check-label" for="kewarganegaraan-2-{{$index}}">Warga Negara Asing</label>
      </div>
      @error('formulir.'.$index.'.kewarganegaraan')
      <div class="text-danger">{{ $message }}</div>
      @enderror
   </div>
   <div class="col-12 col-md-6">
      <label for="jenis_kelamin-{{$index}}" class="w-100 fw-bold mandatory">Jenis Kelamin</label>
      <div class="form-check form-check-inline">
         <input class="form-check-input" type="radio" name="formulir[{{$index}}][jenis_kelamin]" id="jenis_kelamin-1-{{$index}}" value="l" {{!$pendaki ? "" : ($pendaki->jenis_kelamin == "l" ? "checked" : "")}}>
         <label class="form-check-label" for="jenis_kelamin-1-{{$index}}">Laki-Laki</label>
      </div>
      <div class="form-check form-check-inline">
         <input class="form-check-input" type="radio" name="formulir[{{$index}}][jenis_kelamin]" id="jenis_kelamin-2-{{$index}}" value="p" {{!$pendaki ? "" : ($pendaki->jenis_kelamin == "p" ? "checked" : "")}}>
         <label class="form-check-label" for="jenis_kelamin-2-{{$index}}">Perempuan</label>
      </div>
      @error('formulir.'.$index.'.jenis_kelamin')
      <div class="text-danger">{{ $message }}</div>
      @enderror
   </div>
</div>

<div class="row mb-3">
   <div class="col-12 col-md-6">
      <label for="jenis_identitas-{{$index}}" class="w-100 fw-bold mandatory">Jenis Identitas</label>
      <select class="form-control" name="formulir[{{$index}}][jenis_identitas]" id="jenis_identitas-{{$index}}" readonly>
         <option value="ktp" {{ !$pendaki ? "" :($pendaki->kategori_pendaki == "wni" ? 'selected' : '' )}}>KTP</option>
         <option value="pasport" {{ !$pendaki ? "" :($pendaki->kategori_pendaki == "wna" ? 'selected' : '' )}}>Pasport</option>
      </select>
      @error('formulir.'.$index.'.jenis_identitas')
      <div class="text-danger">{{ $message }}</div>
      @enderror
   </div>
   <div class="col-12 col-md-6">
      <label for="identitas" class="w-100 fw-bold mandatory">Identitas</label>
      <input type="text" class="form-control" name="formulir[{{$index}}][identitas]" id="identitas" value="{{ $pendaki? $pendaki->nik : '' }}">
      @error('formulir.'.$index.'.identitas')
      <div class="text-danger">{{ $message }}</div>
      @enderror
   </div>
</div>

<div class="row mb-3">
   <div class="col-12 col-md-6">
      <label for="nama-{{$index}}" class="w-100 fw-bold">Nama</label>
      <input type="text" class="form-control" name="formulir[{{$index}}][nama]" id="nama-{{$index}}" value="{{ !$pendaki ? '' :$pendaki->nama }}">
      @error('formulir.' .$index. '.nama')
      <div class="text-danger">{{ $message }}</div>
      @enderror
   </div>
   <div class="col-12 col-md-6">
      <label for="lampiran_identitas-{{$index}}" class="w-100 fw-bold mandatory">Lampiran Identitas</label>
      <input class="form-control" type="file" name="formulir[{{$index}}][lampiran_identitas]" id="lampiran_identitas-{{$index}}">
      <span class="keterangan">Lampiran KTP Orang Tua/wali Untuk Pendaki di bawah 17 tahun</span>
      @error('formulir.'.$index.'.lampiran_identitas')
      <div class="text-danger">{{ $message }}</div>
      @enderror
   </div>
</div>

<div class="row mb-3">
   <div class="col-12 col-md-6">
      <div class="row">
         <div class="col-12 col-md-6">
            <label for="no_hp-{{$index}}" class="w-100 fw-bold mandatory">Nomor Telepon</label>
            <input type="text" class="form-control" name="formulir[{{$index}}][no_hp]" id="no_hp-{{$index}}" value="{{ !$pendaki ? '' :$pendaki->no_hp}}">
            <span class="keterangan">Isikan No. Telepon yang terkoneksi dengan WhatsApp</span>
            @error('formulir.'.$index.'.no_hp')
            <div class="text-danger">{{ $message }}</div>
            @enderror
         </div>
         <div class="col-12 col-md-6">
            <label for="no_hp_darurat-{{$index}}" class="w-100 fw-bold mandatory">Nomor Telepon Darurat</label>
            <input type="text" class="form-control" name="formulir[{{$index}}][no_hp_darurat]" id="no_hp_darurat-{{$index}}" value="{{ !$pendaki ? '' :$pendaki->no_hp_darurat}}">
            <span class="keterangan">*Silahkan diisi dengan No. Telepon Orang Tua / Kerabat</span>
            @error('formulir.'.$index.'.no_hp_darurat')
            <div class="text-danger">{{ $message }}</div>
            @enderror
         </div>
      </div>
   </div>
   <div class="col-12 col-md-6">
      <div class="row">
         <div class="col-8">
            <label for="tanggal_lahir-{{$index}}" class="w-100 fw-bold">Tanggal Lahir</label>
            <input type="date" class="form-control" name="formulir[{{$index}}][tanggal_lahir]" id="tanggal_lahir-{{$index}}" value="{{ !$pendaki ? '' :Carbon\Carbon::parse($pendaki->tanggal_lahir)->format('Y-m-d') }}" onkeyup="generateUsia('{{$index}}')" onchange="generateUsia('{{$index}}')">
            @error('formulir.'.$index.'.tanggal_lahir')
            <div class="text-danger">{{ $message }}</div>
            @enderror
         </div>
         <div class="col-4">
            <label for="usia-{{$index}}" class="w-100 fw-bold">Usia</label>
            <input type="number" class="form-control" name="formulir[{{$index}}][usia]" id="usia-{{$index}}" value="{{ !$pendaki ? '' :$pendaki->usia }}" readonly>
            @error('formulir.'.$index.'.usia')
            <div class="text-danger">{{ $message }}</div>
            @enderror
         </div>
      </div>
   </div>
</div>

<div class="row">
   <label for="alamat_domisili" class="w-100 fw-bold mandatory">Alamat Domisili</label>
</div>
<div class="row mb-3">
   <div class="col-12 col-md-6">
      <div class="row">
         <div class="col-12 col-md-6">
            <label for="provinsi-{{$index}}" class="w-100">Provinsi</label>
            <select class="form-control ipt-provinsi" name="formulir[{{$index}}][provinsi]" id="provinsi-{{$index}}">
               <option value="Null">{{$pendaki ? $pendaki->provinsi : "Pilih Provinsi"}}</option>
            </select>
            @error('formulir.'.$index.'.provinsi')
            <div class="text-danger">{{ $message }}</div>
            @enderror
         </div>
         <div class="col-12 col-md-6">
            <label for="kabupaten_kota-{{$index}}" class="w-100">Kabupaten/Kota</label>
            <select class="form-control ipt-kabupaten-kota" name="formulir[{{$index}}][kabupaten_kota]" id="kabupaten_kota-{{$index}}">
               <option value="Null">{{$pendaki ? $pendaki->kabupaten : "Pilih Kabupaten/Kota"}}</option>
               <!-- Tambahkan opsi sesuai kebutuhan -->
            </select>
            @error('formulir.'.$index.'.kabupaten_kota')
            <div class="text-danger">{{ $message }}</div>
            @enderror
         </div>
      </div>
   </div>
   <div class="col-12 col-md-6">
      <div class="row">
         <div class="col-12 col-md-6">
            <label for="kecamatan-{{$index}}" class="w-100">Kecamatan</label>
            <select class="form-control ipt-kecamatan" name="formulir[{{$index}}][kecamatan]" id="kecamatan-{{$index}}">
               <option value="Null">{{$pendaki ? $pendaki->kec : "Pilih Kecamatan"}}</option>
               <!-- Tambahkan opsi sesuai kebutuhan -->
            </select>
            @error('formulir.'.$index.'.kecamatan')
            <div class="text-danger">{{ $message }}</div>
            @enderror
         </div>
         <div class="col-12 col-md-6">
            <label for="desa_kelurahan-{{$index }}" class="w-100">Desa/Kelurahan</label>
            <select class="form-control ipt-desa-kelurahan" name="formulir[{{$index}}][desa_kelurahan]" id="desa_kelurahan-{{$index }}">
               <option value="Null">{{$pendaki ? $pendaki->desa : "Pilih Desa/Kelurahan"}}</option>
               <!-- Tambahkan opsi sesuai kebutuhan -->
            </select>
            @error('formulir.'.$index.'.desa_kelurahan')
            <div class="text-danger">{{ $message }}</div>
            @enderror
         </div>
      </div>
   </div>
</div>

<div class="row mb-3">
   <div class="col-12 col-md-6">
      <label for="surat_keterangan_sehat-{{$index}}" class="w-100 fw-bold mandatory">Surat Keterangan Sehat</label>
      <input class="form-control" type="file" name="formulir[{{$index}}][surat_keterangan_sehat]" id="surat_keterangan_sehat-{{$index}}">
      @error('formulir.'.$index.'.surat_keterangan_sehat')
      <div class="text-danger">{{ $message }}</div>
      @enderror
   </div>
   <div class="col-12 col-md-6 mb-3">
      @if ($index == 0)
      <div class="mb-3">
         <label for="surat_simaksi-{{$index}}" class="w-100 fw-bold mandatory">Surat Simaksi</label>
         <input class="form-control" type="file" name="formulir[{{$index}}][surat_simaksi]" id="surat_simaksi-{{$index}}">
         @error('formulir.'.$index.'.surat_simaksi')
         <div class="text-danger">{{ $message }}</div>
         @enderror
      </div>
      @endif
      <div>
         <label for="surat_izin_ortu-{{$index}}" class="w-100 fw-bold mandatory">Surat Izin Orang Tua</label>
         <input class="form-control" type="file" name="formulir[{{$index}}][surat_izin_ortu]" id="surat_izin_ortu-{{$index}}">
         @error('formulir.'.$index.'.surat_simaksi')
         <div class="text-danger">{{ $message }}</div>
         @enderror
      </div>
   </div>
</div>

<script>
   function generateUsia(index) {
      // Ambil elemen input tanggal lahir berdasarkan index
      var tanggalLahirInput = document.getElementById('tanggal_lahir-' + index);
      var usiaInput = document.getElementById('usia-' + index);

      // Ambil nilai tanggal lahir dari input
      var tanggalLahir = new Date(tanggalLahirInput.value);
      var today = new Date();

      // Periksa apakah input tanggal lahir diisi
      if (!isNaN(tanggalLahir.getTime())) {
         // Hitung usia berdasarkan tanggal lahir
         var age = today.getFullYear() - tanggalLahir.getFullYear();
         var monthDiff = today.getMonth() - tanggalLahir.getMonth();

         // Jika bulan belum lewat, kurangi usia
         if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < tanggalLahir.getDate())) {
            age--;
         }

         // Isi input usia dengan nilai yang dihitung
         usiaInput.value = age;
      } else {
         // Jika tanggal lahir tidak valid, kosongkan input usia
         usiaInput.value = 0;
      }
   }
</script>