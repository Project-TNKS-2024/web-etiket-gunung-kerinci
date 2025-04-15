@extends('etiket.admin.template.index')

@section('css')

@endsection

@section('main')

<div class="row">
   <div class="col-lg-8 d-flex align-items-strech">
      <div class="card w-100">
         <div class="card-body">
            <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
               <div class="mb-3 mb-sm-0">
                  <h5 class="card-title fw-semibold">Booking Overview</h5>
               </div>
               <div>
                  <select class="form-select">
                     <option value="1">2025</option>
                  </select>
               </div>
            </div>
            <div id="chart"></div>
         </div>
      </div>
   </div>
   <div class="col-lg-4">
      <div class="row">
         <div class="col-lg-12">
            <!-- Yearly Breakup -->
            <div class="card overflow-hidden">
               <div class="card-body p-4">
                  <h5 class="card-title mb-9 fw-semibold">Yearly Breakup</h5>
                  <div class="row align-items-center">
                     <div class="col-8">
                        <h4 class="fw-semibold mb-3">Rp. {{ $dataBreakup['total'] }}</h4>
                        <div class="d-flex align-items-center mb-3">
                           <span
                              class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                              @if($dataBreakup['growth'] >= 0)
                              <i class="ti ti-arrow-up-left text-success"></i>
                              @else
                              <i class="ti ti-arrow-down-right text-danger"></i>
                              @endif
                           </span>
                           <p class="text-dark me-1 fs-3 mb-0">
                              {{ $dataBreakup['growth'] > 0 ? '+' : '' }}{{ $dataBreakup['growth'] }}%
                           </p>
                           <p class="fs-3 mb-0">last year</p>
                        </div>
                        <div class="d-flex align-items-center">
                           <div class="me-4">
                              <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                              <span class="fs-2">{{ $dataBreakup['year'] }}</span>
                           </div>
                           <div>
                              <span class="round-8 bg-light-primary rounded-circle me-2 d-inline-block"></span>
                              <span class="fs-2">{{ $dataBreakup['lastYear'] }}</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="d-flex justify-content-center">
                           <div id="breakup"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-lg-12">
            <!-- Monthly Earnings -->
            <div class="card">
               <div class="card-body">
                  <div class="row align-items-start">
                     <div class="col-8">
                        <h5 class="card-title mb-9 fw-semibold"> Monthly Earnings </h5>
                        <h4 class="fw-semibold mb-3">Rp. {{ $dataEarning['total'] }}</h4>
                        <div class="d-flex align-items-center pb-1">
                           <span class="me-2 rounded-circle 
                  {{ $dataEarning['growth'] >= 0 ? 'bg-light-success' : 'bg-light-danger' }} 
                  round-20 d-flex align-items-center justify-content-center">
                              <i class="ti 
                     {{ $dataEarning['growth'] >= 0 ? 'ti-arrow-up-left text-success' : 'ti-arrow-down-right text-danger' }}">
                              </i>
                           </span>
                           <p class="text-dark me-1 fs-3 mb-0">
                              {{ $dataEarning['growth'] >= 0 ? '+' : '' }}{{ $dataEarning['growth'] }}%
                           </p>
                           <p class="fs-3 mb-0"> {{ $dataEarning['lastMonth'] }}</p>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="d-flex justify-content-end">
                           <div class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                              <i class="ti ti-currency-dollar fs-6"></i>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div id="earning"></div>
            </div>

         </div>
      </div>
   </div>
</div>

<div class="row">
   @forelse ($listDestinasi as $d)
   <div class="col-sm-6 col-xl-3">
      <div class="card overflow-hidden rounded-2">
         <div class="position-relative">
            <a href="{{route('admin.destinasi.detail', ['id' => $d->id])}}"><img src="{{$d->gambar_destinasi->last() ? asset($d->gambar_destinasi->last()->src) : asset('images/no-image.jpg')}}" class="card-img-top rounded-0" style="height: 200px;" alt="..."></a>
            <a href="javascript:void(0)" class="bg-primary rounded-circle p-2 text-white d-inline-flex position-absolute bottom-0 end-0 mb-n3 me-3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add To Cart"><i class="ti ti-basket fs-4"></i></a>
         </div>
         <div class="card-body pt-3 p-4">
            <h6 class="fw-semibold fs-4">{{$d->nama}}</h6>
            <div class="d-flex align-items-center justify-content-between">
               <h6 class="fw-semibold fs-4 mb-0">{{$d->totalPengunjunf()}}<span class="ms-2 fw-normal text-muted fs-3">Pendakian</span></h6>
            </div>
         </div>
      </div>
   </div>
   @empty

   @endforelse

</div>


@endsection

@section('js')
<script src="{{asset('modernize/libs/apexcharts/dist/apexcharts.min.js')}}"></script>
<script>
   // =====================================
   // Earning
   // =====================================
   const dataEarning = JSON.parse('@json($dataEarning)');
   var earning = {
      chart: {
         id: "sparkline3",
         type: "area",
         height: 60,
         sparkline: {
            enabled: true,
         },
         group: "sparklines",
         fontFamily: "Plus Jakarta Sans', sans-serif",
         foreColor: "#adb0bb",
      },
      series: [{
         name: "Earnings",
         color: "#49BEFF",
         data: dataEarning.series,
      }, ],
      stroke: {
         curve: "smooth",
         width: 2,
      },
      fill: {
         colors: ["#f3feff"],
         type: "solid",
         opacity: 0.05,
      },

      markers: {
         size: 0,
      },
      tooltip: {
         theme: "dark",
         fixed: {
            enabled: true,
            position: "right",
         },
         x: {
            show: false,
         },
      },
   };
   new ApexCharts(document.querySelector("#earning"), earning).render();
</script>
<script>
   // =====================================
   // Breakup
   // =====================================
   const dataBreakup = JSON.parse('@json($dataBreakup)');
   var breakup = {
      color: "#adb5bd",
      series: dataBreakup['series'],
      labels: dataBreakup['labels'],
      chart: {
         width: 180,
         type: "donut",
         fontFamily: "Plus Jakarta Sans', sans-serif",
         foreColor: "#adb0bb",
      },
      plotOptions: {
         pie: {
            startAngle: 0,
            endAngle: 360,
            donut: {
               size: '75%',
            },
         },
      },
      stroke: {
         show: false,
      },

      dataLabels: {
         enabled: false,
      },

      legend: {
         show: false,
      },
      colors: ["#5D87FF", "#ecf2ff", "#F9F9FD"],

      responsive: [{
         breakpoint: 991,
         options: {
            chart: {
               width: 150,
            },
         },
      }, ],
      tooltip: {
         theme: "dark",
         fillSeriesColor: false,
      },
   };

   var chart = new ApexCharts(document.querySelector("#breakup"), breakup);
   chart.render();
</script>
<script>
   // =====================================
   // gravik
   // =====================================
   const dataGravik = JSON.parse('@json($dataGravik)');
   var chart = {
      series: dataGravik['series'],

      xaxis: {
         type: "category",
         categories: dataGravik['categories'],
         labels: {
            style: {
               cssClass: "grey--text lighten-2--text fill-color"
            },
         },
      },

      chart: {
         type: "bar",
         height: 345,
         offsetX: -15,
         toolbar: {
            show: true
         },
         foreColor: "#adb0bb",
         fontFamily: 'inherit',
         sparkline: {
            enabled: false
         },
      },

      colors: [
         "#5D87FF", // Biru terang sedikit keunguan  
         "#FFD700", // Kuning emas  
         "#49BEFF", // Biru cerah (sky blue)  
         "#FFC300", // Kuning keemasan yang lebih cerah  
         "#3F92FF", // Biru medium yang lebih seimbang  
         "#FFB000", // Kuning oranye (golden yellow)  
         "#1E6BFF", // Biru royal (lebih pekat)  
         "#FFA500", // Kuning jingga  
         "#0048D1", // Biru navy yang lebih gelap  
         "#FF9800" // Kuning amber  
      ],

      plotOptions: {
         bar: {
            horizontal: false,
            columnWidth: "70%",
            borderRadius: [6],
            borderRadiusApplication: 'end',
            borderRadiusWhenStacked: 'all'
         },
      },
      markers: {
         size: 0
      },

      dataLabels: {
         enabled: false,
      },


      legend: {
         show: false,
      },


      grid: {
         borderColor: "rgba(0,0,0,0.1)",
         strokeDashArray: 3,
         xaxis: {
            lines: {
               show: false,
            },
         },
      },

      yaxis: {
         show: true,
         min: 0,
         tickAmount: 4,
         labels: {
            style: {
               cssClass: "grey--text lighten-2--text fill-color",
            },
         },
      },
      stroke: {
         show: true,
         width: 3,
         lineCap: "butt",
         colors: ["transparent"],
      },


      tooltip: {
         theme: "light"
      },

      responsive: [{
         breakpoint: 600,
         options: {
            plotOptions: {
               bar: {
                  borderRadius: 3,
               }
            },
         }
      }]


   };

   var chart = new ApexCharts(document.querySelector("#chart"), chart);
   chart.render();
</script>
@endsection