@for ($i=0; $i <=($booking->total_pendaki_wni+$booking->total_pendaki_wna-1); $i++)
   <div>
      <input type="hidden">

      <div class="row mb-3">
         <div class="col-12 col-md-6">
            <label class="w-100 fw-bold mandatory">Kode Pendaki</label>
            <div class="input-group mb-3">
               <input type="text" class="form-control" placeholder="Kode User" name="formulir[{{$i}}][kode_bio]" value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->id : '' }}">
               <input type="hidden" class="form-control" name="formulir[{{$i}}][id_pendaki]" value="{{ isset($pendaki[$i]) ? $pendaki[$i]->id : '' }}">

               @if ($i !==0)
               <button class="btn btn-outline-secondary" type="button" id="button-addon2" onclick="addKodePendaki('formulir[{{$i}}]')"><i class="fa-solid fa-magnifying-glass"></i></button>
               @endif
            </div>
         </div>
      </div>

      <div class="row mb-3">
         <div class="col-12 col-md-6">
            <div class="row">
               <div class="col-12 col-md-6">
                  <label class="w-100 fw-bold">Nama Depan</label>
                  <input type="text" class="form-control" name="formulir[{{$i}}][first_name]" value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->first_name : '' }}" readonly disabled>
               </div>
               <div class="col-12 col-md-6">
                  <label class="w-100 fw-bold">Nama Belakang</label>
                  <input type="text" class="form-control" name="formulir[{{$i}}][last_name]" value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->last_name : '' }}" readonly disabled>
               </div>
            </div>
         </div>
         <div class="col-12 col-md-6">
            <div class="row">
               <div class="col-6">
                  <label class="w-100 fw-bold">Kewarganegaraan</label>
                  <input type="text" class="form-control" name="formulir[{{$i}}][kewarganegaraan]" value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->kenegaraan : '' }}" readonly disabled>
               </div>
               <!-- <div class="col-6">
                  <label class="w-100 fw-bold">No Induk</label>
                  <input type="number" class="form-control" name="formulir[{{$i}}][identitas]" value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->nik : '' }}" readonly disabled>
               </div> -->
               <div class="col-6">
                  <label class="w-100 fw-bold">Jenis Kelamin</label>
                  <input type="text" class="form-control" name="formulir[{{$i}}][jenis_kelamin]" value="{{ isset($pendaki[$i]) ? ($pendaki[$i]->biodata->jenis_kelamin == 'l' ? 'Laki-laki' : 'Perempuan') : '' }}" readonly disabled>
               </div>
            </div>

         </div>
      </div>

      <div class="row mb-3">
         <div class="col-12 col-md-6">
            <div class="row">
               <div class="col-12 col-md-6">
                  <label class="w-100 fw-bold mandatory">No Telepon</label>
                  <input type="text" class="form-control" name="formulir[{{$i}}][no_hp]" value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->no_hp : '' }}" readonly disabled>
                  <span class="keterangan"></span>
               </div>
               <div class="col-12 col-md-6">
                  <label class="w-100 fw-bold mandatory">No Telepon Darurat</label>
                  <input type="text" class="form-control" name="formulir[{{$i}}][no_hp_darurat]" value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->no_hp_darurat : '' }}">
                  <span class="keterangan"></span>
               </div>
            </div>
         </div>
         <div class="col-12 col-md-6">
            <!-- <div class="row">
               <div class="col-8">
                  <label class="w-100 fw-bold">Tanggal Lahir</label>
                  <input type="date" class="form-control ipt-tanggal-lahir" data-index="{{$i}}" id="tanggal_lahir-{{$i}}" name="formulir[{{$i}}][tanggal_lahir]" value="{{ isset($pendaki[$i]) ? Carbon\Carbon::parse($pendaki[$i]->biodata->tanggal_lahir)->format('Y-m-d')  : '' }}" readonly disabled>
               </div>
               <div class="col-4">
                  <label class="w-100 fw-bold">Usia</label>
                  <input type="number" class="form-control" id="usia-{{$i}}" readonly disabled>
               </div>
            </div> -->
            <div class="" id="div-surat_izin_ortu-{{$i}}">
               <label for="surat_izin_ortu-{{$i}}" class="w-100 fw-bold mandatory">Surat Izin Orang Tua</label>
               <div class="input-group flex-nowrap">
                  <input class="form-control" type="file" name="formulir[{{$i}}][surat_izin_ortu]" id="surat_izin_ortu-{{$i}}">
                  </input>
                  @if (isset($pendaki[$i]) && $pendaki[$i]->lampiran_surat_izin_ortu)
                  <input type="hidden" id="surat_izin_ortu-{{$i}}_existing" value="{{asset($pendaki[$i]->lampiran_surat_izin_ortu)}}">
                  @endif
                  <button class="input-group-text d-none" type="button" data-id-target="surat_izin_ortu-{{$i}}">
                     <i class="fa-regular fa-eye"></i>
                  </button>
               </div>

            </div>
         </div>
      </div>

      <!-- <div class="row">
         <label class="w-100 fw-bold mandatory">Alamat Domisili</label>
      </div>
      <div class="row mb-3">
         <div class="col-12 col-md-6">
            <div class="row">
               <div class="col-12 col-md-6">
                  <label for="provinsi-{{$i}}" class="w-100">Provinsi</label>
                  <select class="form-control ipt-provinsi" name="formulir[{{$i}}][provinsi]" id="provinsi-{{$i}}" data-index="{{$i}}" disabled>
                     <option value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->provinsi : 0 }}" selected>Pilih Provinsi</option>
                  </select>

               </div>
               <div class="col-12 col-md-6">
                  <label for="kabupaten_kota-{{$i}}" class="w-100">Kabupaten/Kota</label>
                  <select class="form-control ipt-kabupaten-kota" name="formulir[{{$i}}][kabupaten_kota]" id="kabupaten_kota-{{$i}}" data-index="{{$i}}" disabled>
                     <option value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->kabupaten : 0 }}" selected>Pilih Kabupaten/Kota</option>
                  </select>

               </div>
            </div>
         </div>
         <div class="col-12 col-md-6">
            <div class="row">
               <div class="col-12 col-md-6">
                  <label for="kecamatan-{{$i}}" class="w-100">Kecamatan</label>
                  <select class="form-control ipt-kecamatan" name="formulir[{{$i}}][kecamatan]" id="kecamatan-{{$i}}" data-index="{{$i}}" disabled>
                     <option value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->kec : 0 }}" selected disabled>Pilih Kecamatan</option>
                  </select>

               </div>
               <div class="col-12 col-md-6">
                  <label for="desa_kelurahan-{{$i }}" class="w-100">Desa/Kelurahan</label>
                  <select class="form-control ipt-desa-kelurahan" name="formulir[{{$i}}][desa_kelurahan]" id="desa_kelurahan-{{$i }}" data-index="{{$i}}" disabled>
                     <option value="{{ isset($pendaki[$i]) ? $pendaki[$i]->biodata->desa : 0 }}" selected>Pilih Desa/Kelurahan"</option>
                  </select>

               </div>
            </div>
         </div>
      </div> -->

   </div>


   <hr style="border-width: 5px;">
   @endfor



   <!-- Modal -->
   <div class="modal fade" id="ModalShowFile" tabindex="-1">
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-header">
               <h1 class="modal-title fs-5 my-0" id="exampleModalLabel">File Preview</h1>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <embed id="filePreview" src="" type="application/pdf" style="width: 100%;" />
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>