@extends('etiket.admin.template.index')

@section('css')

<style>
   #biodataTable {
      width: 100%;
      border-collapse: collapse;
   }

   #biodataTable td {
      padding: 3px 5px;
   }

   #biodataTable th {
      background-color: #f2f2f2;
      padding: 0px 5px;
   }
</style>

@endsection

@section('main')

<div class="card">
   <div class="card-header ">
      <h5><b>Biodata Penngunjung</b></h5>
   </div>
   <div class="card-body">
      <div class="row">
         <div class="col-12 col-md-6">
            <h5><b>Biodata</b></h5>
            <table id="biodataTable">
               <tr>
                  <td>NIK/Passport</td>
                  <td>:</td>
                  <td>{{$user->biodata->nik}}</td>
               </tr>
               <tr>
                  <td>Nama Lengkap</td>
                  <td>:</td>
                  <td>{{$user->biodata->fullName}}</td>
               </tr>
               <tr>
                  <td>Kewarganegaraan</td>
                  <td>:</td>
                  <td>{{$user->biodata->dataNegara->name}}</td>
               </tr>
               <tr>
                  <td>Tanggal Lahir</td>
                  <td>:</td>
                  <td>{{$user->biodata->tanggal_lahir}}</td>
               </tr>
               <tr>
                  <td>Jenis Kelamin</td>
                  <td>:</td>
                  <td>{{$user->biodata->jenis_kelamin == 'l' ? 'Laki-Laki' : 'Perempuan'}}</td>
               </tr>
               <tr>
                  <td>No Telp</td>
                  <td>:</td>
                  <td>{{$user->biodata->no_hp}}</td>
               </tr>
               <tr>
                  <td>No Telp Darurat</td>
                  <td>:</td>
                  <td>{{$user->biodata->no_hp_darurat}}</td>
               </tr>
               <tr>
                  <td>Alamat</td>
                  <td>:</td>
                  <td>{{$user->biodata->dataDesa->name}}, {{$user->biodata->dataKecamatan->name}}, {{$user->biodata->dataKabupaten->name}}, {{$user->biodata->dataProvinsi->name}}</td>
               </tr>
            </table>
         </div>
         <div class="col-12 col-md-6">
            <div class="border rounded p-2" style="max-height: 300px; overflow: hidden;">
               <embed src="{{asset($user->biodata->lampiran_identitas)}}#toolbar=0" type="application/pdf" width="100%" height="280px">
            </div>
         </div>

         <h5><b>Riwayat Pendakian</b></h5>
         <div class="table-responsive">
            <table class="table table-bordered">
               <thead class="bg-dark text-white">
                  <tr>
                     <th>No.</th>
                     <th>Destinasi</th>
                     <th>Tanggal</th>
                     <th>Status</th>
                     <th></th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($user->booking as $key => $b)
                  <tr>
                     <td>{{ $loop->iteration }}</td>
                     <td>{{ $b->destinasi->nama ?? '-' }}</td>
                     <td>{{ \Carbon\Carbon::parse($b->tanggal)->format('d M Y') }}</td>
                     <td>
                        @if ($b->status_booking < 4)
                           <span class="badge bg-secondary">{{$b->getStatusBooking()}}</span>
                           @elseif ($b->status_booking < 6)
                              <span class="badge bg-primary">{{$b->getStatusBooking()}}</span>
                              @elseif ($b->status_booking == 6)
                              <span class="badge bg-warning">{{$b->getStatusBooking()}}</span>
                              @elseif ($b->status_booking > 6)
                              <span class="badge bg-success">{{$b->getStatusBooking()}}</span>
                              @else
                              <span class="badge bg-secondary">Tidak Diketahui {{$b->status_booking}}</span>
                              @endif
                     </td>
                     <td>
                        <a href="{{ route('admin.destinasi.booking.show', $b->id) }}" class="btn btn-info btn-sm">
                           <i class="fa-solid fa-circle-info"></i>
                        </a>
                     </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
         @if ($user->biodata->verified == 'pending')
         <form method="post" id="btnVerify" class="d-flex align-items-center w-100" action="{{ route('admin.master.pengunjung.biodata.verified') }}">
            @csrf
            <!-- Input keterangan dengan fleksibilitas penuh -->
            <input type="text" name="keterangan" class="form-control me-3" placeholder="Masukkan keterangan">
            <input type="hidden" name="id_user" id="id_user" value="{{$user->id}}">

            <!-- Tombol aksi -->
            <button type="submit" name="verified" class="btn btn-success me-2" value="verified">Verifikasi</button>
            <button type="submit" name="verified" class="btn btn-danger me-2" value="unverified">Unverifikasi</button>
         </form>
         @endif

      </div>
   </div>
</div>


@endsection

@section('js')


@endsection