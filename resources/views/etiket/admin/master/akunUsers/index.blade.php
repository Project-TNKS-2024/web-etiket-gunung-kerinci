@extends('etiket.admin.template.index')

@section('css')

<style>

</style>

@endsection

@section('main')

<div class="card">
   <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
         <label class="text-2xl font-bold gk-text-base-black mb-2">Daftar Pengguna</label>
      </div>

      <table class="rounded table table-striped table-bordered">
         <thead>
            <tr>
               <th scope="col">#</th>
               <th scope="col">Email</th>
               <th scope="col">Nama Depan</th>
               <th scope="col">Nama Belakang</th>
               <th scope="col">Jenis Kelamin</th>
               <th scope="col">Satus</th>
               <th scope="col">Action</th>
            </tr>
         </thead>
         <tbody class="table-group-divider">
            @foreach ($dataUser as $index => $user)
            <tr>
               <th scope="row">{{ $dataUser->firstItem() + $index }}</th> <!-- Nomor urut sesuai halaman -->
               <td>{{ $user->email }}</td>
               <td>{{ optional($user->biodata)->first_name }}</td>
               <td>{{ optional($user->biodata)->last_name }}</td>
               <td>{{ optional($user->biodata)->jenis_kelamin == 'l' ? 'Laki-laki' : 'Perempuan' }}</td>
               <td>{{ optional($user->biodata)->verified }}</td>
               <td>
                  @if ($user->biodata->verified == 'pending')
                  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal" data-detail="{{json_encode($user)}}" data-lampiran={{json_encode(asset($user->biodata->lampiran_identitas))}}>Verifikasi</button>
                  @else
                  <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal" data-detail="{{json_encode($user)}}" data-lampiran={{json_encode(asset($user->biodata->lampiran_identitas))}}>Detail</button>
                  @endif
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>

      <!-- Tampilkan navigasi pagination -->
      <div class="d-flex justify-content-center mt-3">
         {{ $dataUser->links('pagination::bootstrap-5') }}
      </div>

   </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-xl">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5" id="detailModalLabel">Detail Pengguna</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <div class="container">
               <h1 class="fs-4 fw-bold">Biodata</h1>
               <div class="row">
                  <div class="col-12 col-md-6">
                     <table id="biodataTable">
                        <tr>
                           <td>NIK/Passport</td>
                           <td>:</td>
                           <td id="detailNIK">-</td>
                        </tr>
                        <tr>
                           <td>Nama Lengkap</td>
                           <td>:</td>
                           <td id="detailNama">-</td>
                        </tr>
                        <tr>
                           <td>Kewarganegaraan</td>
                           <td>:</td>
                           <td id="detailKewarganegaraan">-</td>
                        </tr>
                        <tr>
                           <td>Tempat/Tanggal Lahir</td>
                           <td>:</td>
                           <td id="detailTTL">-</td>
                        </tr>
                        <tr>
                           <td>Jenis Kelamin</td>
                           <td>:</td>
                           <td id="detailJenisKelamin">-</td>
                        </tr>
                        <tr>
                           <td>No Telp</td>
                           <td>:</td>
                           <td id="detailNoTelp">-</td>
                        </tr>
                        <tr>
                           <td>No Telp Darurat</td>
                           <td>:</td>
                           <td id="detailNoDarurat">-</td>
                        </tr>
                        <tr>
                           <td>Alamat</td>
                           <td>:</td>
                           <td id="detailAlamat">-</td>
                        </tr>
                     </table>
                  </div>
                  <div class="col-12 col-md-6">
                     <div class="border rounded p-2" style="max-height: 300px; overflow: hidden;">
                        <embed id="detailLampiran" src="" type="application/pdf" width="100%" height="280px">
                     </div>
                  </div>
               </div>
               <h1 class="fs-4 fw-bold mt-3">Riwayat Pendakian</h1>
               <!-- Tambahkan jika diperlukan -->
            </div>
         </div>
         <div class="modal-footer">

            <form method="post" id="btnVerify" class="d-none" action="{{ route('admin.master.pengunjung.verified')}}">
               @csrf
               <input type="hidden" name="id_user" id="id_user" value="">
               <button type="submit" name="verified" class="btn btn-success" value="verified">Verifikasi</button>
               <button type="submit" name="verified" class="btn btn-danger" value="enverified">Unverifikasi</button>
            </form>

            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
         </div>

      </div>
   </div>
</div>
@endsection

@section('js')
<!-- modal detail pendaki -->
<script>
   document.addEventListener('DOMContentLoaded', () => {
      const detailModal = document.getElementById('detailModal');

      document.querySelector('tbody').addEventListener('click', function(e) {
         if (e.target && e.target.matches('button[data-detail]')) {
            const detailData = JSON.parse(e.target.getAttribute('data-detail'));
            const lampiran = JSON.parse(e.target.getAttribute('data-lampiran'));

            // Update modal content
            document.getElementById('detailNIK').textContent = detailData.biodata?.nik || '-';
            document.getElementById('id_user').value = detailData.id || '-';
            document.getElementById('detailNama').textContent = detailData.biodata?.first_name + ' ' + (detailData.biodata?.last_name || '');
            document.getElementById('detailKewarganegaraan').textContent = detailData.biodata?.kenegaraan || '-';
            document.getElementById('detailTTL').textContent = detailData.biodata?.tanggal_lahir || '-';
            document.getElementById('detailJenisKelamin').textContent = detailData.biodata?.jenis_kelamin === 'l' ? 'Laki-laki' : 'Perempuan';
            document.getElementById('detailNoTelp').textContent = detailData.biodata?.no_hp || '-';
            document.getElementById('detailNoDarurat').textContent = detailData.biodata?.no_hp_darurat || '-';
            document.getElementById('detailAlamat').textContent = detailData.biodata?.provinsi + ' ' + detailData.biodata?.kabupaten + ' ' + detailData.biodata?.kec + ' ' + detailData.biodata?.desa || '-';
            document.getElementById('detailLampiran').setAttribute('src', lampiran + '#toolbar=0' || '');

            // Show or hide buttons based on verification status
            const btnVerify = document.getElementById('btnVerify');
            if (detailData.biodata?.verified === 'pending') {
               btnVerify.classList.remove('d-none');
            } else {
               btnVerify.classList.add('d-none');
            }



            // Show modal
            const modalInstance = bootstrap.Modal.getOrCreateInstance(detailModal);
            modalInstance.show();
         }
      });
   })
</script>

@endsection