@extends('homepage.template.index')


@section('css')
<style>
    .pdf-container {
        max-width: 700px;
        width: 100%;
    }

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
</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => '.',
])
<div class="container my-5">
    <div class="row">
        <div class="col-12 col-sm-6 col-lg-7">
            <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="{{asset('assets/img/cover/danau-kaco.png')}}" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="{{asset('assets/img/cover/gunung-tujuh.png')}}" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="{{asset('assets/img/cover/kerinci.png')}}" class="d-block w-100" alt="...">
                    </div>
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
        <div class="col-12 col-sm-6 col-lg-5">
            <form method="post" action="{{route('homepage.postBooking')}}">
                @csrf
                <h4 class="mb-4">Booking tiket pendakian gung kerici</h4>

                <input type="hidden" name="id_tiket" value="1">
                <div class="form-group">
                    <label for="date-start">Pilih tanggal check-in dan check-out</label>
                    <div class="row" id="iptdatevol">
                        <div class="col-md-6 mb-3">
                            <input type="date" class="form-control" name="date-start" id="date-start" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="date" class="form-control" name="date-end" id="date-end" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Total Pendaki</label>
                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <label for="wni">WNI: {{$tiket['wni_weekday']}}</label>
                            <div class="input-group mb-1 inputVolume1" data-price-weekday="{{$tiket['wni_weekday']}}" data-price-weekend="{{$tiket['wni_weekend']}}">
                                <button class="btn btn-outline-secondary" type="button" data-input-vol="ipt+">+</button>
                                <input type="number" class="form-control" name="wni" id="wni" placeholder="Jumlah WNI" required value="0" readonly>
                                <button class="btn btn-outline-secondary" type="button" data-input-vol="ipt-">-</button>
                            </div>
                            <label for="wna">WNA: {{$tiket['wna_weekday']}} </label>
                            <div class="input-group mb-1 inputVolume1" data-price-weekday="{{$tiket['wna_weekday']}}" data-price-weekend="{{$tiket['wna_weekend']}}">
                                <button class="btn btn-outline-secondary" type="button" data-input-vol="ipt+">+</button>
                                <input type="number" class="form-control" name="wna" id="wna" placeholder="Jumlah WNA" required value="0" readonly>
                                <button class="btn btn-outline-secondary" type="button" data-input-vol="ipt-">-</button>
                            </div>
                            <div>
                                <label for="totalharga">Total Harga</label>
                                <p style="font-size: 11px;" id="labeliptvol">*0 hari 0 malam (2D1N)</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3 ">
                            <br>
                            <div class="card ">
                                <div class="card-body p-2">
                                    <p>
                                        Rp. <span class="iptvol">000</span>
                                    </p>
                                    <br>
                                    <p class="mb-0">
                                        Rp. <span class="iptvol">000</span>
                                    </p>
                                </div>
                            </div>
                            <div class="m-2">
                                <p>
                                    Rp. <span id="iptvol-total">000</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class=" form-group row">
                    <div class="col-6">
                        <label for="gerbang-masuk">Gerbang Masuk</label>
                        <select class="form-control" name="gerbang-masuk" id="gerbang-masuk" required>
                            <option value="" selected disabled>Pilih Gerbang Masuk</option>
                            @foreach ($gates as $gate)
                            <option value="{{$gate['id']}}">{{$gate['nama']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="gerbang-keluar">Gerbang Keluar</label>
                        <select class="form-control" name="gerbang-keluar" id="gerbang-keluar" required>
                            <option value="" selected disabled>Pilih Gerbang Keluar</option>
                            @foreach ($gates as $gate)
                            <option value="{{$gate['id']}}">{{$gate['nama']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary w-100">Lanjut Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Get all elements with the class 'inputVolume1'
    const inputGroups = document.querySelectorAll('.inputVolume1');
    const inputPrice = document.querySelectorAll('.iptvol');
    const inputTotalPrice = document.getElementById('iptvol-total');
    const inputDate = document.getElementById('iptdatevol');
    const dateStartInput = inputDate.querySelector('input[name="date-start"]');
    const dateEndInput = inputDate.querySelector('input[name="date-end"]');
    const labelTotalPrice = document.getElementById('labeliptvol');

    function calculateAdjustedDays() {
        const startDate = new Date(dateStartInput.value);
        const endDate = new Date(dateEndInput.value);
        let dayDifference = 0; //hari
        if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate)) {
            const timeDifference = endDate - startDate;
            dayDifference = timeDifference / (1000 * 3600 * 24);
            console.log(dayDifference);
        }
        const adjustedDays = (dayDifference - 1); //malam
        labelTotalPrice.textContent = `${dayDifference} Hari ${adjustedDays} malam (${dayDifference}D${adjustedDays}M)`;
        return dayDifference;
    }


    function updateTotalPrice() {
        let totalPrice = 0;
        let dayDifference = calculateAdjustedDays();
        let adjustedDays = dayDifference - 1;

        inputPrice.forEach(span => {
            let price = parseInt(span.textContent);
            if (!isNaN(price)) {
                totalPrice += price;
            }
        });

        totalPrice *= adjustedDays;
        inputTotalPrice.textContent = adjustedDays;
    }

    dateStartInput.addEventListener('change', updateTotalPrice);
    dateEndInput.addEventListener('change', updateTotalPrice);

    inputGroups.forEach((group, index) => {
        const inputField = group.querySelector('input[type="number"]');
        const incrementButton = group.querySelector('button[data-input-vol="ipt+"]');
        const decrementButton = group.querySelector('button[data-input-vol="ipt-"]');

        const price_weekday = parseInt(group.getAttribute('data-price-weekday'));
        const price_weekend = parseInt(group.getAttribute('data-price-weekend'));

        incrementButton.addEventListener('click', () => {
            // tambah nilai inputfield
            let currentValue = parseInt(inputField.value);
            if (isNaN(currentValue)) {
                currentValue = 0;
            }
            inputField.value = currentValue + 1;

            // masukkan nilai harga ke inputprice urutan each goup
            inputPrice[index].textContent = parseInt(price_weekday) * parseInt(inputField.value);
            // update total price
            updateTotalPrice()
        });

        decrementButton.addEventListener('click', () => {
            let currentValue = parseInt(inputField.value);
            if (isNaN(currentValue)) {
                currentValue = 0;
            }
            if (currentValue == 0) {
                currentValue = 0;
            } else {
                inputField.value = parseInt(inputField.value) - 1;
            }

            // masukkan nilai harga ke inputprice urutan each goup
            inputPrice[index].textContent = parseInt(price_weekday) * parseInt(inputField.value);
            // update total price
            updateTotalPrice()
        });
    });
</script>
@endsection