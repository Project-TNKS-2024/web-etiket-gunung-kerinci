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
            <div class="col-12 col-sm-12 col-lg-7 ">
                <div id="carouselExample" class="carousel slide">
                    <div class="carousel-inner">

                        @foreach ($gambar as $g)
                            <div class="carousel-item {{ $loop->index == 0 ? 'active' : ''}}">
                                <img src="{{ $g->src }}" class="d-block w-100" style=";object-fit: cover;height: 480px;" alt="...">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-5 mt-3 mt-lg-0">
                <form method="post" action="{{ route('homepage.postBooking') }}">
                    @csrf
                    <h4 class="mb-4">Booking tiket pendakian gunungg kerici</h4>

                <input type="hidden" name="id" value="1">
                <div class="form-group">
                    <label for="date-start">Pilih tanggal check-in dan check-out</label>
                    <div class="row" id="iptdatevol">
                        <div class="col-md-6 mb-3">
                            <div id="date-start" onclick="generateDate();" class="w-full cursor-pointer btn btn-outline-secondary p-2 rounded d-flex justify-content-between" style="border: 1px solid var(--neutrals300)">
                                <div>dd/mm/yy</div>
                                <img src="{{ asset('assets/icon/tnks/date-dark.svg')}}"></img>
                                <input type="date" class="form-control d-none" name="date_start" id="date-start-value" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div id="date-end" onclick="generateDate();" class="w-full cursor-pointer btn btn-outline-secondary p-2 rounded d-flex justify-content-between" style="border: 1px solid var(--neutrals300)">
                                <div>dd/mm/yy</div>
                                <img src="{{ asset('assets/icon/tnks/date-dark.svg')}}"></img>
                                <input type="date" class="form-control d-none" name="date_end" id="date-end-value" required>
                            </div>
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
                                    <option value="{{ $gate['id'] }}">{{ $gate['nama'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="gerbang-keluar">Gerbang Keluar</label>
                            <select class="form-control" name="gerbang-keluar" id="gerbang-keluar" required>
                                <option value="" selected disabled>Pilih Gerbang Keluar</option>
                                @foreach ($gates as $gate)
                                    <option value="{{ $gate['id'] }}">{{ $gate['nama'] }}</option>
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

    <div id="modal-date-container" class="px-2 d-none justify-content-center align-items-center w-screen h-screen position-fixed top-0 left-0" style="z-index: 999; background-color: rgba(0,0,0,.2);">
        <div style="max-width: 500px; max-height: 500px; " class="w-full h-full bg-white rounded d-flex flex-column justify-content-between">
            <div class="w-full h-full">
                <header class="d-flex align-items-center justify-content-between p-2" style="border-bottom: 1px solid var(--neutrals100)">
                    <div class="">August 2019</div>
                    <div class="d-flex gap-2">
                        <div class="gk-bg-neutrals100 rounded px-1 p-0 m-0 d-flex justify-content-center"><i class="p-0 m-0 bi bi-arrow-left-short text-2xl" style=""></i></div>
                        <div class="gk-bg-neutrals100 rounded px-1 p-0 m-0 d-flex justify-content-center"><i class="p-0 m-0 bi bi-arrow-right-short text-2xl" style=""></i></div>
                    </div>
                </header>
                <table class="row p-2 row justify-content-center">
                    <thead>
                        <tr class="row justify-content-center text-center">
                            <th class="col font-medium gk-text-neutrals500">S</th>
                            <th class="col font-medium gk-text-neutrals500">M</th>
                            <th class="col font-medium gk-text-neutrals500">T</th>
                            <th class="col font-medium gk-text-neutrals500">W</th>
                            <th class="col font-medium gk-text-neutrals500">T</th>
                            <th class="col font-medium gk-text-neutrals500">F</th>
                            <th class="col font-medium gk-text-neutrals500">S</th>
                        </tr>
                    </thead>
                    <tbody id="date-body">
                        <tr class="row justify-content-center text-center mt-2">
                            <td class="col font-medium gk-text-neutrals500">S</td>
                            <td class="col font-medium gk-text-neutrals500">M</td>
                            <td class="col font-medium gk-text-neutrals500">T</td>
                            <td class="col font-medium gk-text-neutrals500">W</td>
                            <td class="col font-medium gk-text-neutrals500">T</td>
                            <td class="col font-medium gk-text-neutrals500">F</td>
                            <td class="col font-medium gk-text-neutrals500">S</td>
                        </tr>
                    </thead>
                    </tbody>
                </table>
            </div>
            <footer class="d-flex align-items-center justify-content-between p-2" style="border-top: 1px solid var(--neutrals100)">
                <div>* Merah berarti penuh</div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary gk-bg-primary700 font-medium ">Pilih tanggal</button>
                    <button class="btn btn-secondary gk-bg-neutrals200 font-medium text-black" onclick="closeDate()">Batal</button>
                </div>
            </footer>
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
            let dayDifference = 0;
            if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate)) {
                const timeDifference = endDate - startDate;
                dayDifference = timeDifference / (1000 * 3600 * 24);
            }
            const adjustedDays = Math.floor((dayDifference) / 2) + 1;
            labelTotalPrice.textContent = `${dayDifference+1} Hari ${adjustedDays} malam (${dayDifference+1}D${adjustedDays}M)`;
            return adjustedDays;
        }

        // dateStartInput.addEventListener('change', calculateAdjustedDays);
        // dateEndInput.addEventListener('change', calculateAdjustedDays);

        function updateTotalPrice() {
                let totalPrice = 0;
                let adjustedDays = calculateAdjustedDays();

                inputPrice.forEach(span => {
                    let price = parseInt(span.textContent);
                    if (!isNaN(price)) {
                        totalPrice += price;
                    }
                });

            totalPrice *= adjustedDays;
            inputTotalPrice.textContent = totalPrice;
        }

        inputGroups.forEach((group, index) => {
            const inputField = group.querySelector('input[type="number"]');
            const incrementButton = group.querySelector('button[data-input-vol="ipt+"]');
            const decrementButton = group.querySelector('button[data-input-vol="ipt-"]');
            const price = parseInt(group.getAttribute('data-price-vol'));

                incrementButton.addEventListener('click', () => {
                    // tambah nilai inputfield
                    let currentValue = parseInt(inputField.value);
                    if (isNaN(currentValue)) {
                        currentValue = 0;
                    }
                    inputField.value = currentValue + 1;

                // masukkan nilai harga ke inputprice urutan each goup
                inputPrice[index].textContent = parseInt(price) * parseInt(inputField.value);
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
                inputPrice[index].textContent = parseInt(price) * parseInt(inputField.value);
                // update total price
                updateTotalPrice()
            });
        });

        function generateDate(current = null) {
            const dateContainer = document.getElementById("modal-date-container");
            dateContainer.classList.add("d-flex");
            dateContainer.classList.remove("d-none");
            if (!current) {
                const now = new Date();
                const year = now.getFullYear();
                const month = now.getMonth();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                const row = Math.floor(daysInMonth/7);
                const dayStart = getDayOfWeek(1, month, year);
                console.log(daysInMonth, month, year);
                console.log(dayStart);


                const dataBody = document.getElementById("data-body");
                for (let i = 0; i < row; i++) {
                    const tr = document.createElement("tr");
                    tr.classList.add(...("row justify-content-center text-center mt-2".split(" ")));
                    for (let j = 0; j < 7; j++) {
                        const td = document.createElement("td");
                        td.classList.add(...("col font-medium gk-text-neutrals500".split(" ")));
                        td.textContent = `${i}${j}`;
                        tr.appendChild(td);
                    }
                    dataBody.appendChild(tr);  // Correcting this line
                }

            }

        }

        function closeDate() {
            const dateContainer = document.getElementById("modal-date-container");
            dateContainer.classList.add("d-none");
            dateContainer.classList.remove("d-flex");
        }

        function getDayOfWeek(day, month, year) {
            const date = new Date(year, month, day);
            const dayOfWeekNumber = date.getDay();
            return dayOfWeekNumber;
        }

    </script>
@endsection
