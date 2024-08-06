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
                        <img src="{{ url('/').'/'.$g->src }}" class="d-block w-100" style=";object-fit: cover;height: 480px;" alt="...">
                    </div>
                    @endforeach
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
            <form method="post" action="{{ route('homepage.postBooking') }}">
                @csrf
                <h4 class="mb-4">Booking tiket pendakian {{$paket->nama}} - {{$gunung->nama}}</h4>

                <input type="hidden" name="id" value="1">
                <div class="form-group">
                    <label for="date-start">Pilih tanggal check-in dan check-out</label>
                    <div class="row" id="iptdatevol">
                        <div class="col-md-6 mb-3">
                            <div id="date-start" onclick="generateDate('date-start-value','tanggal-start-label');" class="w-full cursor-pointer btn btn-outline-secondary p-2 rounded d-flex justify-content-between" style="border: 1px solid var(--neutrals300)">
                                <div id="tanggal-start-label">dd/mm/yy</div>
                                <img src="{{ asset('assets/icon/tnks/date-dark.svg')}}"></img>
                                <input type="hidden" class="" name="date_start" id="date-start-value" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div id="date-end" onclick="generateDate('date-end-value','tanggal-end-label');" class="w-full cursor-pointer btn btn-outline-secondary p-2 rounded d-flex justify-content-between" style="border: 1px solid var(--neutrals300)">
                                <div id="tanggal-end-label">dd/mm/yy</div>
                                <img src="{{ asset('assets/icon/tnks/date-dark.svg')}}"></img>
                                <input type="hidden" class="" name="date_end" id="date-end-value" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Total Pendaki</label>
                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <label for="wni">WNI: </label>
                            <div class="input-group mb-1 inputVolume1" data-price-weekday="" data-price-weekend="">
                                <button class="btn btn-outline-secondary" type="button" data-input-vol="ipt+">+</button>
                                <input type="number" class="form-control" name="wni" id="wni" placeholder="Jumlah WNI" required value="0" readonly>
                                <button class="btn btn-outline-secondary" type="button" data-input-vol="ipt-">-</button>
                            </div>
                            <label for="wna">WNA: </label>
                            <div class="input-group mb-1 inputVolume1" data-price-weekday="" data-price-weekend="">
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
    <div style="max-width: 500px; " class="w-full h-fit bg-white rounded d-flex flex-column justify-content-between">
        <div class="w-full h-full">
            <header class="d-flex align-items-center justify-content-between p-2" style="border-bottom: 1px solid var(--neutrals100)">
                <div class=""><span id="date-month">August</span> <span id="date-year">2019</span></div>
                <div class="d-flex gap-2">
                    <div id="date-prev" class="d-block gk-bg-neutrals100 rounded px-1 p-0 m-0 d-flex justify-content-center" onclick="prevMonth()"><i class="p-0 m-0 bi bi-arrow-left-short text-2xl" style=""></i></div>
                    <div id="date-next" class="d-block gk-bg-neutrals100 rounded px-1 p-0 m-0 d-flex justify-content-center" onclick="nextMonth()"><i class="p-0 m-0 bi bi-arrow-right-short text-2xl" style=""></i></div>
                </div>
            </header>
            <table class="row p-2 row justify-content-center py-3">
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
                </tbody>
            </table>
        </div>
        <footer class="d-flex align-items-center justify-content-between p-2" style="border-top: 1px solid var(--neutrals100)">
            <div>* Merah berarti penuh</div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary gk-bg-primary700 font-medium" id="select-date">Pilih tanggal</button>
                <button class="btn btn-secondary gk-bg-neutrals200 font-medium text-black" onclick="closeDate()">Batal</button>
            </div>
        </footer>
    </div>
</div>
@endsection

@section('js')
<script>
    const currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let currentDay = currentDate.getDate();
    const months = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember"
    ];

    function generateCalendar() {
        const datePrev = document.getElementById("date-prev");

        if (currentMonth === currentDate.getMonth()) {
            datePrev.classList.add("d-none");
            datePrev.classList.remove("d-block");
        } else {
            datePrev.classList.add("d-block");
            datePrev.classList.remove("d-none");
        }
        const dateContainer = document.getElementById("modal-date-container");
        dateContainer.classList.add("d-flex");
        dateContainer.classList.remove("d-none");

        const year = currentYear;
        const month = currentMonth;
        const day = currentDay;

        document.getElementById("date-month").textContent = months[month];
        document.getElementById("date-year").textContent = year;
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const dayStart = getDayOfWeek(1, month, year);
        const dataBody = document.getElementById("date-body");
        let tr = document.createElement("tr");
        tr.classList.add(...("row justify-content-center text-center mt-2 week-row".split(" ")));


        for (i = 0; i < dayStart; i++) {
            const td = document.createElement("td");
            td.classList.add(...("col font-medium gk-text-neutrals500".split(" ")));
            td.textContent = ``;
            tr.appendChild(td);
        }


        for (i = 0; i < daysInMonth; i++) {
            const td = document.createElement("td");
            const div = document.createElement("div");
            div.textContent = `${i+1}`;
            if (currentMonth === currentDate.getMonth() && i + 1 === currentDate.getDate() && currentYear === currentDate.getFullYear()) {
                div.classList.add("border", "border-primary", "gk-bg-primary700", "text-white", "rounded-pill");
            }
            td.appendChild(div)
            td.classList.add(...("col font-medium gk-text-neutrals500".split(" ")));
            td.style.cursor = "pointer";

            td.addEventListener("click", function(event) {
                if (
                    (parseInt(td.textContent) >= currentDate.getDate() && currentMonth === currentDate.getMonth() && currentYear === currentDate.getFullYear()) || (currentMonth > currentDate.getMonth() && currentYear === currentDate.getFullYear())
                ) {
                    document.querySelectorAll(".week-row").forEach(o => {
                        const tableData = o.querySelectorAll(".border .border-primary, .gk-bg-primary700, .text-white, .rounded-pill");
                        if (tableData.length > 0) {
                            tableData[0].classList.remove("border", "border-primary", "gk-bg-primary700", "text-white", "rounded-pill");
                        }
                    });
                    td.querySelector("div").classList.add("border", "border-primary", "gk-bg-primary700", "text-white", "rounded-pill");
                    currentDay = td.textContent;
                }
            });


            tr.appendChild(td);
            if ((i + 1 + dayStart) % 7 === 0 || i === daysInMonth - 1) {
                dataBody.appendChild(tr);
                tr = document.createElement("tr");
                tr.classList.add(...("row justify-content-center text-center mt-2 week-row".split(" ")));
            }
        }

        const lastWeek = Array.from(document.querySelectorAll(".week-row")).slice(-1)[0];
        const dataInLastWeek = lastWeek.querySelectorAll("td");
        if (dataInLastWeek.length < 7) {
            for (i = 0; i < 7 - dataInLastWeek.length; i++) {
                const td = document.createElement("td");
                td.classList.add(...("col font-medium gk-text-neutrals500".split(" ")));
                td.textContent = ``;
                lastWeek.appendChild(td);
            }
        }

    }

    function generateDate(input, label) {

        const selectButton = document.getElementById("select-date");

        generateCalendar();
        // Remove existing event listeners before adding a new one
        const newSelectButton = selectButton.cloneNode(true);
        selectButton.parentNode.replaceChild(newSelectButton, selectButton);

        newSelectButton.addEventListener("click", function(event) {
            if (currentDay < currentDate.getDate()) {
                return;
            }
            const dateLabel = document.getElementById(label);
            document.getElementById(input).value = `${currentMonth + 1}/${currentDay}/${currentYear}`;
            dateLabel.textContent = `${currentDay}/${currentMonth + 1}/${currentYear}`;
            closeDate();
        });
    }

    function closeDate() {
        const dateContainer = document.getElementById("modal-date-container");
        dateContainer.classList.add("d-none");
        dateContainer.classList.remove("d-flex");
        clearDate();
        currentMonth = currentDate.getMonth();
        currentYear = currentDate.getFullYear();

    }

    function getDayOfWeek(day, month, year) {
        const date = new Date(year, month, day);
        const dayOfWeekNumber = date.getDay();
        return dayOfWeekNumber;
    }

    function clearDate() {
        const weekRow = document.querySelectorAll(".week-row");
        weekRow.forEach(element => {
            element.remove();
        });
    }

    function prevMonth() {
        if (currentMonth - 1 < currentDate.getMonth()) {
            return;
        }
        clearDate();
        currentMonth = (currentMonth - 1);
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        // generateDate();
        generateCalendar();
    }

    function nextMonth() {
        clearDate();
        currentMonth = (currentMonth + 1);
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        // generateDate();
        generateCalendar();
    }
</script>


<script>
    // Get all elements with the class 'inputVolume1'
    const inputGroups = document.querySelectorAll('.inputVolume1');

    const inputDate = document.getElementById('iptdatevol');
    const dateStartInput = inputDate.querySelector('input#date-start-value');
    const dateEndInput = inputDate.querySelector('input#date-end-value');


    const inputPrice = document.querySelectorAll('.iptvol');
    const inputTotalPrice = document.getElementById('iptvol-total');
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
        console.log("perubahan waktu masuk dan keluar");
        return adjustedDays;
    }

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
</script>


@endsection