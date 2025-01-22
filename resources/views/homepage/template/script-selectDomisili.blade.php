<script>
   let dataProvinsi = [];
   let dataKabupaten = [];
   let dataKecamatan = [];
   let dataKelurahan = [];

   async function loadData() {
      try {
         const responseProvinsi = await fetch('/assets/json/provinsi.json');
         dataProvinsi = await responseProvinsi.json();
         // console.log('Data provinsi:', dataProvinsi);

         const responseKabupaten = await fetch('/assets/json/kabupaten.json');
         dataKabupaten = await responseKabupaten.json();
         // console.log('Data kabupaten:', dataKabupaten);

         const responseKecamatan = await fetch('/assets/json/kecamatan.json');
         dataKecamatan = await responseKecamatan.json();
         // console.log('Data kecamatan:', dataKecamatan);

         const responseKelurahan = await fetch('/assets/json/kelurahan.json');
         dataKelurahan = await responseKelurahan.json();
         // console.log('Data kelurahan:', dataKelurahan);

      } catch (error) {
         console.error('Gagal memuat data:', error);
      }
   }

   const provinsiSelects = document.querySelectorAll('select.ipt-provinsi');
   const kabupatenSelects = document.querySelectorAll('select.ipt-kabupaten-kota');
   const kecamatanSelects = document.querySelectorAll('select.ipt-kecamatan');
   const kelurahanSelects = document.querySelectorAll('select.ipt-desa-kelurahan');

   function RefreshSelect(name, select, data = [], value = 0) {
      select.innerHTML = '';
      select.innerHTML += `<option value="0" ${value == 0 ? 'selected' : ''} disabled >Pilih ${name}</option>`;
      data.forEach(function(item) {
         select.innerHTML += `<option value="${item.id}" ${value == item.id ? 'selected' : ''}>${item.name}</option>`;
      });

   }

   document.addEventListener('DOMContentLoaded', async function() {
      await loadData();

      provinsiSelects.forEach(function(select) {
         const value = select.value;
         RefreshSelect(
            'Provinsi',
            select,
            dataProvinsi,
            value
         );
         select.addEventListener('change', function(e) {
            const index = e.target.dataset.index;
            const idProv = e.target.value;
            RefreshSelect(
               'Kabupaten',
               Array.from(kabupatenSelects).find(item => item.dataset.index == index),
               dataKabupaten.filter(item => item.provinsi_id == idProv),
               0
            );
            RefreshSelect(
               'Kecamatan',
               Array.from(kecamatanSelects).find(item => item.dataset.index == index),
               [],
               0
            );
            RefreshSelect(
               'Kelurahan',
               Array.from(kelurahanSelects).find(item => item.dataset.index == index),
               [],
               0
            );
         });
      });

      kabupatenSelects.forEach(function(select) {
         const value = select.value;
         const index = select.getAttribute('data-index');
         //ambil id provinsi
         const idProv = Array.from(provinsiSelects).find(item => item.dataset.index == index).value;
         RefreshSelect(
            'Kabupaten',
            select,
            dataKabupaten.filter(item => item.provinsi_id == idProv),
            value
         );
         select.addEventListener('change', function(e) {
            const index = e.target.getAttribute('data-index');
            const idKab = e.target.value;
            RefreshSelect(
               'Kecamatan',
               Array.from(kecamatanSelects).find(item => item.dataset.index == index),
               dataKecamatan.filter(item => item.kabupaten_id == idKab),
               0
            );
            RefreshSelect(
               'Kelurahan',
               Array.from(kelurahanSelects).find(item => item.dataset.index == index),
               [],
               0
            );
         });
      });

      kecamatanSelects.forEach(function(select) {
         const value = select.value;
         const index = select.getAttribute('data-index');
         //ambil id kabupaten
         const idKab = Array.from(kabupatenSelects).find(item => item.dataset.index == index).value;
         RefreshSelect(
            'Kecamatan',
            select,
            dataKecamatan.filter(item => item.kabupaten_id == idKab),
            value
         );
         select.addEventListener('change', function(e) {
            const index = e.target.getAttribute('data-index');
            const idKec = e.target.value;
            RefreshSelect(
               'Kelurahan',
               Array.from(kelurahanSelects).find(item => item.dataset.index == index),
               dataKelurahan.filter(item => item.kecamatan_id == idKec),
               0
            );
         });
      });
      kelurahanSelects.forEach(function(select) {
         const value = select.value;
         const index = select.getAttribute('data-index');
         //ambil id kecamatan
         const idKec = Array.from(kecamatanSelects).find(item => item.dataset.index == index).value;
         RefreshSelect(
            'Kelurahan',
            select,
            dataKelurahan.filter(item => item.kecamatan_id == idKec),
            value
         );
      });


   });
</script>