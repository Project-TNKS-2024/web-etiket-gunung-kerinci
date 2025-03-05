<div class="row mb-3">
   <div class="col-12">
      <label class="w-100 fw-bold mandatory">Barang Bawaan Wajib</label>

      <div class="form-check">
         <input class="form-check-input" type="checkbox" name="barangWajib[perlengkapan_gunung_standar]" value="1" id="perle_gunung" {{ old('barangWajib.perlengkapan_gunung_standar') ? 'checked' : '' }}>
         <label class="form-check-label" for="perle_gunung">
            Perlengkapan Standar Pendaki Gunung
         </label>
      </div>

      <div class="form-check">
         <input class="form-check-input" type="checkbox" name="barangWajib[trash_bag]" value="1" id="trash_bag" {{ old('barangWajib.trash_bag') ? 'checked' : '' }}>
         <label class="form-check-label" for="trash_bag">
            Kantong Sampah
         </label>
      </div>

      <div class="form-check">
         <input class="form-check-input" type="checkbox" name="barangWajib[p3k_standart]" value="1" id="p3k_standart" {{ old('barangWajib.p3k_standart') ? 'checked' : '' }}>
         <label class="form-check-label" for="p3k_standart">
            P3K Standar
         </label>
      </div>

      <div class="form-check">
         <input class="form-check-input" type="checkbox" name="barangWajib[survival_kit_standart]" value="1" id="survival_kit_standart" {{ old('barangWajib.survival_kit_standart') ? 'checked' : '' }}>
         <label class="form-check-label" for="survival_kit_standart">
            Survival Kit Standar
         </label>
      </div>

   </div>
</div>