@extends('etiket.admin.template.index')

@section('css')

@endsection

@section('main')

<div style="min-height: 100vh;">
    <main class="p-10 d-flex flex-column gap-3">
        <header class="text-2xl font-bold gk-text-base-black">Kelola Tiket</header>
        <div class="row">
            <div class="col-md-4 col-sm-6 d-flex align-items-strech">
                <div class="card w-100">
                    <div class="card-body">
                       <div class="">
                          <div class="">
                             <h5 class="card-title fw-semibold">Jumlah Destinasi</h5>
                          </div>
                            <div class="text-5xl font-bold gk-text-base-black">
                                {{count($destinasi)}} <span class="text-sm gk-text-neutrals500">destinasi</span>
                          </div>
                       </div>
                    </div>
                 </div>
            </div>
            <div class="col-md-4 col-sm-6 d-flex align-items-strech">
                <div class="card w-100">
                    <div class="card-body">
                       <div class="">
                          <div class="">
                             <h5 class="card-title fw-semibold">Rata-Rata Tiket Terjual / Destinasi</h5>
                          </div>
                          <div>
                              <div class="text-4xl font-bold gk-text-base-black">
                                {{$totalTerjual}}
                              </div>
                              <div class="d-flex flex-row align-items-center gap-2">
                                <img src="{{asset('assets/img/logo/path-down.png')}}" width="20"/>
                                <span class="gk-text-error300 font-bold text-lg">1.3%</span>
                                <span class="text-sm">Turun dari minggu lalu</span>
                              </div>
                          </div>
                       </div>
                    </div>
                 </div>
            </div>
        </div>
        <div class="row">
           <div class="">
                <a class="btn btn-primary text-start text-white font-bold d-flex align-items-center gap-2 w-fit" href="{{route('admin.tiket.tambah')}}">
                    <i class="ti ti-plus p-1 rounded-pill font-bold"></i>
                    Tambah Destinasi
                </a>
           </div>
        </div>

        <div class="row"  style="overflow: visible;">
            <div class="col-12 p-0 shadow" style="overflow:auto;">
                @include('etiket.admin.master-data.destinasi.daftar', [
                    "headers" => ["Nama Destinasi", "Lokasi", "Aksi"],
                    "data" => $destinasi,
                ])
            </div>
        </div>
    </main>

</div>
@endsection

