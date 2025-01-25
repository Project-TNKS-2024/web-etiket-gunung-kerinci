@extends('homepage.template.index')


@section('css')
    <style>
        /* modal preview file */
        .modal-dialog {
            height: calc(100vh - 50px);
            /* Kurangi sedikit untuk margin */
            margin: 15px auto;
            /* Margin atas dan bawah */
        }

        .modal-content {
            height: 100%;
        }

        .modal-body {
            height: calc(100% - 60px);
            /* Kurangi tinggi header dan footer modal */
            overflow: hidden;
            /* Agar tidak ada scroll di modal-body */
        }

        #filePreview {
            height: 100%;
            width: 100%;
            border: none;
        }
    </style>
@endsection
@section('main')
    @include('homepage.template.header', [
        'title' => 'Pendakian Gunung Kerinci',
        'caption' => 'Formulir Paket ' . $booking->gktiket->nama,
    ])

    <script></script>
    <div class="container my-5">
        @include('homepage.booking.booking-nav', ['step' => $booking->status_booking])
        <form id="formulir" action="{{ route('homepage.booking.formulir.action') }}" method="post"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_booking" value="{{ $id }}">

            <div class="card border-0 shadow">
                <div class="card-body px-4 px-md-5">
                    <!-- anggota -->
                    <h1 class="fs-6 fw-bold mt-3">Anggota Pendakian</h1>

                    @include('homepage.booking.fp.formulir', [
                        'pendaki' => $pendaki,
                    ])


                    <!-- barang -->

                    @include('homepage.booking.fp.barang')

                </div>
            </div>
            <div class="row justify-content-center">
                {{-- <div class="col-12 col-md-4"></div> --}}
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-primary gk-bg-primary700 fw-thin border-0 w-100 mt-3"
                        name="action" value="save">Simpan</button>
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-primary gk-bg-primary700 fw-thin border-0 w-100 mt-3"
                        name="action" value="next">Selanjutnya</button>
                </div>
            </div>
        </form>
    </div>
    </div>
@endsection
@section('js')
    <!-- scipt refresh option select domisili -->
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
                select.innerHTML +=
                    `<option value="${item.id}" ${value == item.id ? 'selected' : ''}>${item.name}</option>`;
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

    <!-- script auto generate usia -->
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
            console.log(age);
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

    <!-- script modal show file -->
    @include('homepage.template.modal-prefiewFile')

    <!-- script menambah anggota -->
    <form id="formAddKodePendaki" action="{{ route('homepage.booking.formulir.pebdaki.add') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="" id="idAddKodePendaki">
        <input type="hidden" name="code" value="" id="codeAddKodePendaki">
        <input type="hidden" name="booking" value="{{ $booking->id }}">
    </form>
    <script>
        function addKodePendaki(nameipt) {
            const id = document.querySelector(`input[name="${nameipt}[id_pendaki]"]`).value;
            const code = document.querySelector(`input[name="${nameipt}[kode_bio]"]`).value;
            document.getElementById('idAddKodePendaki').value = id;
            document.getElementById('codeAddKodePendaki').value = code;
            document.getElementById('formAddKodePendaki').submit();
        }
    </script>
@endsection
