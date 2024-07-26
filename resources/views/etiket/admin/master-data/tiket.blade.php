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
                             <h5 class="card-title fw-semibold">Jenis Tiket</h5>
                          </div>
                            <div class="text-4xl font-bold gk-text-base-black">
                                {{count($tiket)}}
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
                             <h5 class="card-title fw-semibold">Total Tiket Terjual</h5>
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
                    Tambah Tiket
                </a>
           </div>
        </div>

        <div class="row">
            @include('etiket.admin.components.table', [
                "headers" => ["Destinasi", "Nama Tiket", "Gate Masuk", "Jenis Tiket", "Harga Tiket", "Aksi"],
                "data" => $tiket,
            ])
        </div>
    </main>
</div>
@endsection

