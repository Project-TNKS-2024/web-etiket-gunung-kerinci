@php
$index = $index ?? 0;
$title = ($index == 0) ? 'Biodata Ketua' : 'Biodata Anggota '.$index;
@endphp

<h1>{{$title}}</h1>
<div class="row mb-3">
   <div class="col-12 col-md-6">
      <label for="kewarganegaraan" class="w-100 fw-bold mandatory">Jenis Kewarganegaraan</label>
      <div class="form-check form-check-inline">
         <input class="form-check-input" type="radio" name="formulir[{{$index}}][kewarganegaraan]" id="kewarganegaraan-1" value="Warga Negara Indonesia">
         <label class="form-check-label" for="kewarganegaraan-1">Warga Negara Indonesia</label>
      </div>
      <div class="form-check form-check-inline">
         <input class="form-check-input" type="radio" name="formulir[{{$index}}][kewarganegaraan]" id="kewarganegaraan-2" value="Warga Negara Asing">
         <label class="form-check-label" for="kewarganegaraan-2">Warga Negara Asing</label>
      </div>
   </div>
   <div class="col-12 col-md-6">
      <label for="jenis_kelamin" class="w-100 fw-bold mandatory">Jenis Kelamin</label>
      <div class="form-check form-check-inline">
         <input class="form-check-input" type="radio" name="formulir[{{$index}}][jenis_kelamin]" id="jenis_kelamin-1" value="Laki-Laki">
         <label class="form-check-label" for="jenis_kelamin-1">Laki-Laki</label>
      </div>
      <div class="form-check form-check-inline">
         <input class="form-check-input" type="radio" name="formulir[{{$index}}][jenis_kelamin]" id="jenis_kelamin-2" value="Perempuan">
         <label class="form-check-label" for="jenis_kelamin-2">Perempuan</label>
      </div>
   </div>
</div>

<div class="row mb-3">
   <div class="col-12 col-md-6">
      <label for="jenis_identitas" class="w-100 fw-bold mandatory">Jenis Identitas</label>
      <select class="form-control" name="formulir[{{$index}}][jenis_identitas]" id="jenis_identitas">
         <option value="ktp">KTP</option>
         <option value="pasport">Pasport</option>
      </select>
   </div>
   <div class="col-12 col-md-6">
      <label for="identitas" class="w-100 fw-bold mandatory">Identitas</label>
      <input type="text" class="form-control" name="formulir[{{$index}}][identitas]" id="identitas">
   </div>
</div>

<div class="row mb-3">
   <div class="col-12 col-md-6">
      <div class="row mb-3">
         <div class="col-12 col-md-6">
            <label for="nama_depan" class="w-100 fw-bold">Nama Depan</label>
            <input type="text" class="form-control" name="formulir[{{$index}}][nama_depan]" id="nama_depan">
         </div>
         <div class="col-12 col-md-6">
            <label for="nama_belakang" class="w-100 fw-bold">Nama Belakang</label>
            <input type="text" class="form-control" name="formulir[{{$index}}][nama_belakang]" id="nama_belakang">
         </div>
      </div>
   </div>
   <div class="col-12 col-md-6">
      <label for="lampiran_identitas" class="w-100 fw-bold mandatory">Lampiran Identitas</label>
      <input class="form-control" type="file" name="formulir[{{$index}}][lampiran_identitas]" id="lampiran_identitas">
      <span class="keterangan">Lampiran KTP Orang Tua/wali Untuk Pendaki di bawah 17 tahun</span>
   </div>
</div>

<div class="row mb-3">
   <div class="col-12 col-md-6">
      <div class="row">
         <div class="col-12 col-md-6">
            <label for="nomor_telepon" class="w-100 fw-bold mandatory">Nomor Telepon</label>
            <input type="text" class="form-control" name="formulir[{{$index}}][nomor_telepon]" id="nomor_telepon">
            <span class="keterangan">Isikan No. Telepon yang terkoneksi dengan WhatsApp</span>
         </div>
         <div class="col-12 col-md-6">
            <label for="nomor_telepon_darurat" class="w-100 fw-bold mandatory">Nomor Telepon Darurat</label>
            <input type="text" class="form-control" name="formulir[{{$index}}][nomor_telepon_darurat]" id="nomor_telepon_darurat">
            <span class="keterangan">*Silahkan diisi dengan No. Telepon Orang Tua / Kerabat</span>
         </div>
      </div>
   </div>
   <div class="col-12 col-md-6">
      <div class="row">
         <div class="col-8">
            <label for="tanggal_lahir" class="w-100 fw-bold">Tanggal Lahir</label>
            <input type="date" class="form-control" name="formulir[{{$index}}][tanggal_lahir]" id="tanggal_lahir">
         </div>
         <div class="col-4">
            <label for="usia" class="w-100 fw-bold">Usia</label>
            <input type="number" class="form-control" name="formulir[{{$index}}][usia]" id="usia" readonly>
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
            <label for="provinsi" class="w-100">Provinsi</label>
            <select class="form-control ipt-provinsi" name="formulir[{{$index}}][provinsi]" id="provinsi">
               <option value="Null">Pilih Provinsi</option>
               <!-- Tambahkan opsi sesuai kebutuhan -->
            </select>
         </div>
         <div class="col-12 col-md-6">
            <label for="kabupaten_kota" class="w-100">Kabupaten/Kota</label>
            <select class="form-control ipt-kabupaten-kota" name="formulir[{{$index}}][kabupaten_kota]" id="kabupaten_kota">
               <option value="Null">Pilih Kabupaten/Kota</option>
               <!-- Tambahkan opsi sesuai kebutuhan -->
            </select>
         </div>
      </div>
   </div>
   <div class="col-12 col-md-6">
      <div class="row">
         <div class="col-12 col-md-6">
            <label for="kecamatan" class="w-100">Kecamatan</label>
            <select class="form-control ipt-kecamatan" name="formulir[{{$index}}][kecamatan]" id="kecamatan">
               <option value="Null">Pilih Kecamatan</option>
               <!-- Tambahkan opsi sesuai kebutuhan -->
            </select>
         </div>
         <div class="col-12 col-md-6">
            <label for="desa_kelurahan" class="w-100">Desa/Kelurahan</label>
            <select class="form-control ipt-desa-kelurahan" name="formulir[{{$index}}][desa_kelurahan]" id="desa_kelurahan">
               <option value="Null">Pilih Desa/Kelurahan</option>
               <!-- Tambahkan opsi sesuai kebutuhan -->
            </select>
         </div>

      </div>
   </div>
</div>

<div class="row mb-3">
   <div class="col-12 col-md-6">
      <label for="surat_keterangan_sehat" class="w-100 fw-bold mandatory">Surat Keterangan Sehat</label>
      <input class="form-control" type="file" name="formulir[{{$index}}][surat_keterangan_sehat]" id="surat_keterangan_sehat">
   </div>
   <div class="col-12 col-md-6">
      <label for="surat_simaksi" class="w-100 fw-bold mandatory">Surat Simaksi</label>
      <input class="form-control" type="file" name="formulir[{{$index}}][surat_simaksi]" id="surat_simaksi">
   </div>
</div>