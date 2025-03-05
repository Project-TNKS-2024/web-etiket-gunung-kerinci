@extends('homepage.template.index')


@section('css')
<style>
    .fc-daygrid-day-top a {
        text-decoration: none !important;
        color: inherit;
    }
</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Wisata Pendakian '.$destinasi->nama,
'caption' => "Paket ".$paket->nama,
])

<div class="container my-5">
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-7 ">
            <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner">
                    @forelse ($gambar as $g)
                    <div class="carousel-item {{ $loop->index == 0 ? 'active' : ''}}">
                        <img src="{{ url('/').'/'.$g->src }}" class="d-block w-100" style="object-fit: cover;height: 480px;" alt="...">
                    </div>
                    @empty
                    <div class="carousel-item active">
                        <img src="{{ asset('assets/img/sampel/sampel 2.png') }}" class="d-block w-100" style="object-fit: cover;height: 480px;" alt="...">
                    </div>
                    @endforelse

                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <div class="col-12 col-sm-12 col-lg-5 mt-3 mt-lg-0">
            <form method="post" action="{{ route('homepage.booking.destinasi.paket.tiket.action') }}" id="form-booking">
                @csrf
                <p class="mb-2 fs-5 fw-bold">Booking Tiket Wisata Pendakian {{$destinasi->nama}}</p>
                <input type="hidden" value="{{$paket->id}}" id="jenis-tiket-value" name="jenis_tiket" required />

                <div class="mb-2">
                    <label class="fw-semibold">Pilih tanggal check-in dan check-out</label>
                    <div class="row" id="iptdatevol">
                        <div class="col-md-6">
                            <label for="date_start" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="date_start" name="date_start" required value="{{ old('date_start') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="date_end" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="date_end" name="date_end" required value="{{ old('date_end') }}">
                        </div>
                    </div>
                    <input type="hidden" name="days_traking" value="0" id="days_traking">
                </div>

                <div class="mb-2">
                    <p class="mb-0 fw-semibold">Total Pendaki</p>
                    <label for="wni"> WNI</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group" data-price-weekday="" data-price-weekend="">
                                <button class="btn border" type="button" onclick="btnIptInc('wni')">+</button>
                                <input type="number" class="form-control" name="wni" id="wni" placeholder="Jumlah WNI" required value="{{ old('wni', 0) }}" readonly>
                                <button class="btn border" type="button" onclick="btnIptDec('wni')">-</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="form-control mb-0">Rp. <span id="priceWni">000</span></p>
                        </div>
                    </div>
                    <label for="wna"> WNA</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group" data-price-weekday="" data-price-weekend="">
                                <button class="btn border" type="button" onclick="btnIptInc('wna')">+</button>
                                <input type="number" class="form-control" name="wna" id="wna" placeholder="Jumlah WNA" required value="{{ old('wna', 0) }}" readonly>
                                <button class="btn border" type="button" onclick="btnIptDec('wna')">-</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="form-control mb-0">Rp. <span id="priceWna">000</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Total Harga</label>
                            <p style="font-size: 11px;" class="mb-0" id="labeliptvol">
                                *
                                <span id="countDays">0</span>
                                hari
                                <span id="countNights">0</span>
                                malam
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="form-control mb-0 mt-2">Rp. <span id="priceTotal">000</span></p>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <p class="mb-0 fw-semibold">Pilih Gerbang Masuk</p>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="gerbang_masuk">Gerbang Masuk</label>
                            <select class="form-select" id="gerbang_masuk" aria-label="destinasi" name="gerbang_masuk" required>
                                <option selected disabled>Pilih gerbang</option>
                                @foreach ($destinasi->gates as $d)
                                <option value="{{ $d->id }}" {{ old('gerbang_masuk') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="gerbang_keluar">Gerbang Keluar</label>
                            <select class="form-select" id="gerbang_keluar" aria-label="destinasi" name="gerbang_keluar" required>
                                <option selected disabled>Pilih gerbang</option>
                                @foreach ($destinasi->gates as $d)
                                <option value="{{ $d->id }}" {{ old('gerbang_keluar') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary gk-bg-primary700 w-100">Lanjut Booking</button>
                </div>

            </form>

        </div>
    </div>
    <div class="row mt-4" style="overflow-x: auto">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mt-2">Tarif PNBP <span style="font-size: 15px;">(Pendapatan Negara Bukan Pajak )</span></h5>
                </div>
                <div class="card-body" style="overflow-x: auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Kewarganegaraan</th>
                                <th scope="col">Masuk Hari Kerja</th>
                                <th scope="col">Masuk Hari Libur</th>
                                <th scope="col">Kemah</th>
                                <th scope="col">Pendakian</th>
                                <th scope="col">Ansuransi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tiket as $key => $t)
                            <tr>
                                <th scope="row">{{ $key + 1 }}</th>
                                <td> {{strtoupper($t->kategori_pendaki)}} </td>
                                <td> Rp. {{ number_format($t->harga_masuk_wd, 0, ',', '.') }} </td>
                                <td> Rp. {{ number_format($t->harga_masuk_wk, 0, ',', '.') }} </td>
                                <td> Rp. {{ number_format($t->harga_kemah, 0, ',', '.') }} </td>
                                <td> Rp. {{ number_format($t->harga_traking, 0, ',', '.') }} </td>
                                <td> Rp. {{ number_format($t->harga_ansuransi, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="mb-0"><strong>Catatan:</strong></p>
                    <p class="mb-1">Tarif tiket dihitung berdasarkan komponen berikut:</p>
                    <ul>
                        <li><strong>Tarif tiket masuk:</strong> Dikalikan dengan jumlah hari pendakian</li>
                        <li><strong>Tarif kemah:</strong> Dikalikan dengan jumlah malam pendakian</li>
                        <li><strong>Tarif pendakian:</strong> Biaya tambahan untuk pendakian</li>
                        <li><strong>Tarif asuransi:</strong> Biaya perlindungan selama pendakian</li>
                    </ul>
                    <p><strong>Total tarif tiket</strong> = (Tarif tiket masuk × jumlah hari) + (Tarif kemah × jumlah malam) + Tarif pendakian + Tarif asuransi</p>
                    <p><i>Pastikan untuk menghitung dengan benar sesuai dengan durasi pendakian dan fasilitas yang dipilih.</i></p>

                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4" style="overflow-x: auto">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mt-2">Pendakian <span style="font-size: 15px;">(Jumlah Pendaki Terdaftar )</span></h5>
                </div>
                <div class="card-body" style="overflow-x: auto">
                    <div class="row">
                        <!-- Kalender Bulan Saat Ini -->
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <div class="card">
                                <div class="card-header">
                                    <p class="mb-0">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
                                </div>
                                <div class="card-body">
                                    <div id='calendar1'></div>
                                </div>
                            </div>
                        </div>

                        <!-- Kalender Bulan Depan -->
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <div class="card">
                                <div class="card-header">
                                    <p class="mb-0">{{ \Carbon\Carbon::now()->addMonth()->format('F Y') }}</p>
                                </div>
                                <div class="card-body">
                                    <div id='calendar2'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

<script>
    var divCalendars = [
        document.getElementById('calendar1'),
        document.getElementById('calendar2'),
    ]

    const dataBulanan = JSON.parse('@json($bookingBulanan)');

    function getDate(index) {
        let today = new Date();
        return new Date(today.getFullYear(), today.getMonth() + index, 1);
    }

    // Mengubah dataBulanan menjadi format FullCalendar
    var eventsData = dataBulanan.map(item => ({
        title: `${item.gate_masuk.nama}: ${item.jumlah_pendaki} / ${item.gate_masuk.max_pendaki_hari}`,
        start: item.tanggal_masuk,
        allDay: true
    }));

    divCalendars.forEach((div, index) => {
        var calendar = new FullCalendar.Calendar(div, {
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap5',
            headerToolbar: false,
            initialDate: getDate(index),
            weekNumbers: false,
            dayMaxEvents: false,
            height: 'auto', // Pastikan tinggi menyesuaikan konten
            contentHeight: 'auto',
            dayMaxEventRows: 9999,
            eventContent: function(arg) {
                return {
                    html: `<div style="white-space: normal; word-wrap: break-word; font-size: 12px;">${arg.event.title}</div>`
                };
            },
            events: eventsData,
        });
        calendar.render();
    });
</script>


<script>
    // Data harga tiket
    const harga = JSON.parse('@json($tiket)');

    function btnIptInc(id) {
        let input = document.getElementById(id);
        let value = parseInt(input.value);
        value++;
        input.value = value;
        updatePrice();
    }

    function btnIptDec(id) {
        let input = document.getElementById(id);
        let value = parseInt(input.value);
        if (value > 0) {
            value--;
            input.value = value;
        }
        updatePrice();
    }

    const formBooking = document.getElementById('form-booking');

    // Fungsi untuk memperbarui harga total
    function updatePrice() {
        const wniCount = parseInt(formBooking['wni'].value) || 0;
        const wnaCount = parseInt(formBooking['wna'].value) || 0;

        const dateStart = formBooking['date_start'].value;
        const dateEnd = formBooking['date_end'].value;

        let priceWni = 0;
        let priceWna = 0;

        if (dateStart && dateEnd) {
            if (dateStart < dateEnd) {
                console.log('tanggal pendakian falid');

                // Iterasi melalui rentang tanggal
                for (let currentDate = new Date(dateStart); currentDate <= new Date(dateEnd); currentDate.setDate(currentDate.getDate() + 1)) {
                    const isWeekend = checkIfWeekend(currentDate);

                    // Harga tiket masuk
                    priceWni += wniCount * (isWeekend ? harga[0].harga_masuk_wk : harga[0].harga_masuk_wd);
                    priceWna += wnaCount * (isWeekend ? harga[1].harga_masuk_wk : harga[1].harga_masuk_wd);

                    // Harga kemah
                    priceWni += wniCount * harga[0].harga_kemah;
                    priceWna += wnaCount * harga[1].harga_kemah;
                }
                // Harga kemah - sehari 
                priceWni += wniCount * harga[0].harga_kemah;
                priceWna += wnaCount * harga[1].harga_kemah;

                // hitung harga ansuransi
                // priceWni += wniCount * (harga[0].harga_ansuransi * (days / (1000 * 3600 * 24)));
                // priceWni += wniCount * harga[1].harga_ansuransi;

                // Harga tracking dan asuransi (hanya dihitung sekali per pendaki)
                priceWni += wniCount * (harga[0].harga_traking + harga[0].harga_ansuransi);
                priceWna += wnaCount * (harga[1].harga_traking + harga[1].harga_ansuransi);
            } else {
                console.log('tanggal pendakian valid');
            }

            const days = (new Date(formBooking['date_end'].value) - new Date(formBooking['date_start'].value)) / (1000 * 3600 * 24);
            document.getElementById('countDays').textContent = days + 1;
            document.getElementById('countNights').textContent = days;
            formBooking['days_traking'].value = days;
        } else {
            console.log('tanggal pendakian belum dipilih');
        }

        document.getElementById('priceWni').textContent = formatRupiah(priceWni);
        document.getElementById('priceWna').textContent = formatRupiah(priceWna);

        const totalPrice = priceWni + priceWna;
        document.getElementById('priceTotal').textContent = formatRupiah(totalPrice);
    }

    // Fungsi untuk mengecek apakah tanggal adalah akhir pekan
    function checkIfWeekend(dateString) {
        const date = new Date(dateString);
        const day = date.getDay(); // 0 = Minggu, 6 = Sabtu
        return day === 0 || day === 6;
    }

    // Fungsi untuk memformat angka ke format Rupiah
    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(value).replace('Rp', '').trim();
    }

    // Validasi tanggal
    formBooking.date_start.addEventListener('change', function() {
        const dateStart = new Date(this.value);
        const today = new Date();
        if (dateStart < today) {
            alert('Tanggal mulai tidak boleh sebelum hari ini!');
            this.value = '';
        }
        updatePrice();
    });

    formBooking.date_end.addEventListener('change', function() {
        const dateEnd = new Date(this.value);
        const dateStart = new Date(formBooking.date_start.value);
        if (dateEnd <= dateStart) {
            alert('Tanggal selesai harus setelah tanggal mulai!');
            this.value = '';
        }
        updatePrice();
    });

    // Inisialisasi awal
    document.addEventListener('DOMContentLoaded', function() {
        updatePrice();
    });
</script>



@endsection