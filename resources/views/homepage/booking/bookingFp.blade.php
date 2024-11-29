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
      margin-top: 20px;
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
'caption' => "Formulir Paket ".$booking->gktiket->nama
])

<script>
</script>
<div class="container my-5">
   @include('homepage.booking.booking-nav', ['step' => $booking->status_booking])
   <form id="formulir" action="{{route('homepage.booking.formulir.action')}}" method="post" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id_booking" value="{{$id}}">

      <div class="shadow px-5 pt-1 pb-4 rounded">
         <!-- anggota -->
         @for ($i = 0; $i <= ($booking->total_pendaki_wni+$booking->total_pendaki_wna-1); $i++)

            @if(isset($pendaki[$i]))
            @include('homepage.booking.fp.formulir', [
            'index' => $i,
            'pendaki' => $pendaki[$i]
            ])
            @else
            @include('homepage.booking.fp.formulir', [
            'index' => $i,
            'pendaki' => null
            ])
            @endif

            <hr style="border-width: 5px;">
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
   const kabupatenSelect = document.querySelectorAll('select.ipt-kabupaten-kota');
   const kecamatanSelect = document.querySelectorAll('select.ipt-kecamatan');
   const kelurahanSelect = document.querySelectorAll('select.ipt-desa-kelurahan');

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
         const index = select.getAttribute('data-index');
         RefreshSelect(
            'Provinsi',
            select,
            dataProvinsi,
            value
         );
         select.addEventListener('change', function(e) {
            const index = e.target.getAttribute('data-index');
            const idProv = e.target.value;
            RefreshSelect(
               'Kabupaten',
               kabupatenSelect[index],
               dataKabupaten.filter(item => item.provinsi_id == idProv),
               0
            );
            RefreshSelect(
               'Kecamatan',
               kecamatanSelect[index],
               [],
               0
            );
            RefreshSelect(
               'Kelurahan',
               kelurahanSelect[index],
               [],
               0
            );
         });
      });

      kabupatenSelect.forEach(function(select) {
         const value = select.value;
         const index = select.getAttribute('data-index');
         //ambil id provinsi
         const idProv = provinsiSelects[index].value;
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
               kecamatanSelect[index],
               dataKecamatan.filter(item => item.kabupaten_id == idKab),
               0
            );
            RefreshSelect(
               'Kelurahan',
               kelurahanSelect[index],
               [],
               0
            );
         });
      });

      kecamatanSelect.forEach(function(select) {
         const value = select.value;
         const index = select.getAttribute('data-index');
         //ambil id kabupaten
         const idKab = kabupatenSelect[index].value;
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
               kelurahanSelect[index],
               dataKelurahan.filter(item => item.kecamatan_id == idKec),
               0
            );
         });
      });
      kelurahanSelect.forEach(function(select) {
         const value = select.value;
         const index = select.getAttribute('data-index');
         //ambil id kecamatan
         const idKec = kecamatanSelect[index].value;
         RefreshSelect(
            'Kelurahan',
            select,
            dataKelurahan.filter(item => item.kecamatan_id == idKec),
            value
         );
      });


   });
</script>

<script>
   function generateUsia(index) {
      // Ambil elemen input tanggal lahir berdasarkan index
      var tanggalLahirInput = document.getElementById('tanggal_lahir-' + index);
      var usiaInput = document.getElementById('usia-' + index);
      var divSuratIzin = document.getElementById('div-surat_izin_ortu-' + index);

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

         if (age >= 17) {
            divSuratIzin.classList.add('d-none');
         } else {
            divSuratIzin.classList.remove('d-none');
         }
      } else {
         // Jika tanggal lahir tidak valid, kosongkan input usia
         usiaInput.value = 0;
      }
   }

   const inputsTanggalLahir = document.querySelectorAll('input.ipt-tanggal-lahir');
   document.addEventListener('DOMContentLoaded', function() {
      inputsTanggalLahir.forEach(function(input) {
         const index = input.getAttribute('data-index');
         generateUsia(index);
         input.addEventListener('change', function() {
            generateUsia(index);
         });
      });
   });
</script>

@endsection