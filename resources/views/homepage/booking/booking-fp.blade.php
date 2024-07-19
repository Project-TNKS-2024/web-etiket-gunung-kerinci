@extends('homepage.template.index')


@section('css')
<style>
   .header-bg {
      position: relative;
      background: url("{{ asset('assets/img/bg/title-header-bg.png') }}") no-repeat;
      background-size: cover;
      background-position: 50% 50%;
      color: white;
   }


   .header-bg::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      /* Adjust the alpha value for the desired opacity */
      z-index: 1;
   }

   .header-content {
      position: relative;
      z-index: 2;
   }

   .border-between {
      border-top: 2px solid white;
      width: 50px;
      margin: 20px 0;
   }

   /* formulir anggota*/

   #formulir label.mandatory::after {
      content: " *";
      color: red;
   }

   #formulir .keterangan {
      color: gray;
      font-size: 12px;
   }

   #formulir h1 {
      font-size: 20px;
      font-weight: bold;
      margin-top: 40px;
      margin-bottom: 10px;
   }

   /* formulir barang */
   #formulir .btn-newBarang {
      border: 1px solid #ccc;
      padding: 5px;
   }

   #formulir .btn-newBarang:hover {
      background-color: #ccc;
   }

   /* button newBarang barang ketika di click bukan hover*/
   #formulir .btn-newBarang:active {
      /* background-color: #007bff; */

   }

  
</style>

@endsection
@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Syarat dan Ketentuan',
])

<div class="container my-5">
   @include('homepage.booking.booking-nav', ['step' => 1])
   <form id="formulir" action="{{route('homepage.booking-fp.store')}}" method="post" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id_booking" value="{{$id}}">
      <div class="shadow px-5 pt-1 pb-4 rounded">
         <!-- anggota -->
         @for ($i = 0; $i <= 2; $i++) @include('homepage.booking.fp.formulir',['index'=>$i])
            @endfor
            <!-- barang -->
            @include('homepage.booking.fp.barang')
      </div>
      <div class="row">
         <div class="col-12 col-md-4"></div>
         <div class="col-12 col-md-4">
            <button type="submit" class="btn btn-primary w-100 fw-bold mt-3" name="action" value="save">Simpan</button>
         </div>
         <div class="col-12 col-md-4">
            <button type="submit" class="btn btn-primary w-100 fw-bold mt-3" name="action" value="next">Selanjutnya</button>
         </div>
      </div>
   </form>
</div>
</div>

@endsection
@section('js')
<script>
   const provinsiSelects = document.querySelectorAll('select.ipt-provinsi');
   const kabupatenSelect = document.querySelectorAll('select.ipt-kabupaten-kota');
   const kecamatanSelect = document.querySelectorAll('select.ipt-kecamatan');
   const kelurahanSelect = document.querySelectorAll('select.ipt-desa-kelurahan');
   document.addEventListener('DOMContentLoaded', function() {

      fetch('/assets/json/provinsi.json')
         .then(response => response.json())
         .then(data => {
            provinsiSelects.forEach(select => {
               data.forEach(provinsi => {
                  let option = document.createElement('option');
                  option.value = provinsi.id;
                  option.id = provinsi.id;
                  option.textContent = provinsi.name;
                  select.appendChild(option);
               });
            });
         })
         .catch(error => console.log('Error fetching provinsi data:', error));
   });

   const fetchKabupaten = (provinsiId, selectElement) => {
      fetch('/assets/json/kabupaten.json')
         .then(response => response.json())
         .then(data => {
            // Filter data berdasarkan provinsiId
            const filteredData = data.filter(kabupaten => kabupaten.provinsi_id == provinsiId);

            selectElement.forEach(selectE => {
               // Kosongkan <select> sebelum menambahkan opsi baru
               selectE.innerHTML = '';

               // Tambahkan opsi baru berdasarkan data yang sudah difilter
               filteredData.forEach(kabupaten => {
                  let option = document.createElement('option');
                  option.value = kabupaten.id;
                  option.textContent = kabupaten.name;
                  selectE.appendChild(option);
               });
            });
         })
         .catch(error => console.log('Error fetching kabupaten data:', error));
   };
   const fetchKecamatan = (kabupatenId, selectElement) => {
      fetch('/assets/json/kecamatan.json')
         .then(response => response.json())
         .then(data => {
            // Filter data berdasarkan kabupatenId
            const filteredData = data.filter(kecamatan => kecamatan.kabupaten_id == kabupatenId);
            selectElement.forEach(selectE => {
               // Kosongkan <select> sebelum menambahkan opsi baru
               selectE.innerHTML = '';

               // Tambahkan opsi baru berdasarkan data yang sudah difilter
               filteredData.forEach(kecamatan => {
                  let option = document.createElement('option');
                  option.value = kecamatan.id;
                  option.textContent = kecamatan.name;
                  selectE.appendChild(option);
               });
            });
         })
         .catch(error => console.log('Error fetching kecamatan data:', error));
   };

   const fetchKelurahan = (kecamatanId, selectElement) => {
      fetch('/assets/json/kelurahan.json')
         .then(response => response.json())
         .then(data => {
            // Filter data berdasarkan kecamatanId
            const filteredData = data.filter(kelurahan => kelurahan.kecamatan_id == kecamatanId);

            selectElement.forEach(selectE => {
               // Kosongkan <select> sebelum menambahkan opsi baru
               selectE.innerHTML = '';

               // Tambahkan opsi baru berdasarkan data yang sudah difilter
               filteredData.forEach(kelurahan => {
                  let option = document.createElement('option');
                  option.value = kelurahan.id;
                  option.textContent = kelurahan.name;
                  selectE.appendChild(option);
               });
            });
         })
         .catch(error => console.log('Error fetching kelurahan data:', error));
   };

   // Contoh penggunaan untuk mengaitkan event onchange pada select provinsi
   document.addEventListener('change', function(event) {
      if (event.target.classList.contains('ipt-provinsi')) {
         const selectedProvinsiId = event.target.value;
         // const kabupatenSelect = event.target.nextElementSibling; // Select kabupaten/kota berada setelah select provinsi

         fetchKabupaten(selectedProvinsiId, kabupatenSelect);
      } else if (event.target.classList.contains('ipt-kabupaten-kota')) {
         const selectedKabupatenId = event.target.value;
         // const kecamatanSelect = event.target.nextElementSibling; // Select kecamatan berada setelah select kabupaten/kota

         fetchKecamatan(selectedKabupatenId, kecamatanSelect);
      } else if (event.target.classList.contains('ipt-kecamatan')) {
         const selectedKecamatanId = event.target.value;
         // const kelurahanSelect = event.target.nextElementSibling; // Select kelurahan berada setelah select kecamatan

         fetchKelurahan(selectedKecamatanId, kelurahanSelect);
      }
   });
</script>
@endsection