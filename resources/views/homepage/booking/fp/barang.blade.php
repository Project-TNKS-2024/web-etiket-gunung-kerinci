<h1>Barang Bawaan Tim</h1>

<div class="row mb-3">
   <div class="col-12">
      <label class="w-100 fw-bold mandatory">Barang Bawaan Wajib</label>

      <div class="form-check">
         <input class="form-check-input" type="checkbox" name="barangWajib[perlengkapan_gunung_standar]" value="1" id="perle_gunung" {{ old('barangWajib.perlengkapan_gunung_standar') ? 'checked' : '' }}>
         <label class="form-check-label" for="perle_gunung">
            Perlengkapan Standar Pendaki Gunung
         </label>
      </div>
      @error('barangWajib.perlengkapan_gunung_standar')
      <div class="text-danger">{{ $message }}</div>
      @enderror

      <div class="form-check">
         <input class="form-check-input" type="checkbox" name="barangWajib[trash_bag]" value="1" id="trash_bag" {{ old('barangWajib.trash_bag') ? 'checked' : '' }}>
         <label class="form-check-label" for="trash_bag">
            Trash Bag
         </label>
      </div>
      @error('barangWajib.trash_bag')
      <div class="text-danger">{{ $message }}</div>
      @enderror

      <div class="form-check">
         <input class="form-check-input" type="checkbox" name="barangWajib[p3k_standart]" value="1" id="p3k_standart" {{ old('barangWajib.p3k_standart') ? 'checked' : '' }}>
         <label class="form-check-label" for="p3k_standart">
            P3K Standart
         </label>
      </div>
      @error('barangWajib.p3k_standart')
      <div class="text-danger">{{ $message }}</div>
      @enderror

      <div class="form-check">
         <input class="form-check-input" type="checkbox" name="barangWajib[survival_kit_standart]" value="1" id="survival_kit_standart" {{ old('barangWajib.survival_kit_standart') ? 'checked' : '' }}>
         <label class="form-check-label" for="survival_kit_standart">
            Survival Kit Standart
         </label>
      </div>
      @error('barangWajib.survival_kit_standart')
      <div class="text-danger">{{ $message }}</div>
      @enderror

   </div>



   <div class="col-8">
      <label class="w-100 fw-bold mandatory">Barang Bawaan Tambahan</label>
      <input type="hidden" name="jumlah_barang" value="0" id="jumlah_barang">
      <div id="listNewBarang">

      </div>
      <button type="button" class="w-100 rounded btn-newBarang" id="btnlistNewBarang"><i class="fa-regular fa-square-plus" style="color: grey;"></i></button>
      <!-- Tambahkan lebih banyak checkbox sesuai kebutuhan -->
   </div>
</div>

<script>
   document.getElementById('btnlistNewBarang').addEventListener('click', function(event) {
      if (event.target.classList.contains('btn-newBarang')) {
         const newBarang = document.createElement('div');
         // ambil jumlah barang
         const jumlahBarangInput = document.getElementById('jumlah_barang');
         let jumlahBarang = parseInt(jumlahBarangInput.value) || 0;
         jumlahBarang += 1;
         jumlahBarangInput.value = jumlahBarang;
         // 
         newBarang.innerHTML = `
              <div class="row mb-2">
                  <div class="col-9">
                      <input type="text" class="form-control" name="barangTambahan[${jumlahBarang}][nama]" placeholder="Nama Barang">
                  </div>
                  <div class="col-3">
                      <input type="number" class="form-control" name="barangTambahan[${jumlahBarang}][jumlah]" placeholder="0" min="0">
                  </div>
              </div>
          `;
         document.getElementById('listNewBarang').appendChild(newBarang);
      }
   });
</script>