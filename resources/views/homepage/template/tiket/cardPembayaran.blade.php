 <!-- booking->pendakis->gateMasuk->gateKeluar -->
 <div class="card sticky-deskripsi" id="pembayaran">
    <div class="card-body">
       <h1 class="fs-5 fw-bold text-center">Tagihan</h1>

       <h1 class="fs-6 fw-bold ">Tagian Ke:</h1>
       <p class="mb-0">Email : {{ $booking->user->email }}</p>
       <p>Status :
          @if ($booking->status_pembayaran == 1)
          <span class="badge text-bg-success ">Sukses</span>
          @else
          <span class="badge text-bg-warning ">Belum Bayar</span>
          @endif
       </p>

       <h1 class="fs-6 fw-bold ">Deskripsi Pendakian</h1>
       <table class="table table-borderless table-des">
          <tr>
             <td>Nama Ketua</td>
             <td> : </td>
             <td>{{ $booking->pendakis[0]->first_name . ' ' . $booking->pendakis[0]->last_name }}
             </td>
          </tr>
          <tr>
             <td>Gate Masuk</td>
             <td> : </td>
             <td>{{ $booking->gateMasuk->nama }} </td>
          </tr>
          <tr>
             <td>Gate Keluar</td>
             <td> : </td>
             <td>{{ $booking->gateKeluar->nama }} </td>
          </tr>
          <tr>
             <td>Tanggal Pendakian</td>
             <td> : </td>
             <td>{{ $booking->tanggal_masuk }} </td>
          </tr>
          <tr>
             <td>Tanggal Keluar</td>
             <td> : </td>
             <td>{{ $booking->tanggal_keluar }}</td>
          </tr>
          <tr>
             <td>Total Pendaki</td>
             <td> : </td>
             <td>{{ $booking->total_pendaki_wni }} WNI dan {{ $booking->total_pendaki_wna }} WNA
             </td>
          </tr>
       </table>

       <h1 class="fs-6 fw-bold ">List Tagihan Tiket</h1>
       <table class="table mb-0 bg-transparent">
          <tr class="fw-semibold">
             <td>Nama Pendaki</td>
             <td class="text-end">Tagihan</td>
          </tr>
          @foreach ($booking->pendakis as $pendaki)
          @php
          $tagihan = $hitung($pendaki);
          @endphp
          <tr>
             <td>
                <div>{{ $pendaki->first_name . ' ' . $pendaki->last_name }}</div>
                <div class="d-flex justify-content-between">
                   <div class="small text-muted">Biaya Masuk</div>
                </div>
                <div class="d-flex justify-content-between">
                   <div class="small text-muted">Berkemah</div>
                </div>
                <div class="d-flex justify-content-between">
                   <div class="small text-muted">Pendakian</div>
                </div>
                <div class="d-flex justify-content-between">
                   <div class="small text-muted">Asuransi</div>
                </div>
                <div class="d-flex justify-content-between fw-bold">
                   <div class="small text-muted">Total</div>
                </div>
             </td>
             <td class="text-end">
                <div class="mb-2 align-items-end d-flex" style="flex-direction: column;">
                   <div class="">
                      <p></p>
                   </div>
                   <div class="d-flex justify-content-between">
                      <div class="small text-muted">Rp.
                         {{ number_format($tagihan['masuk']) }}
                      </div>
                   </div>
                   <div class="d-flex justify-content-between">
                      <div class="small text-muted">Rp.
                         {{ number_format($tagihan['berkemah']) }}
                      </div>
                   </div>
                   <div class="d-flex justify-content-between">
                      <div class="small text-muted">Rp.
                         {{ number_format($tagihan['tracking']) }}
                      </div>
                   </div>
                   <div class="d-flex justify-content-between">
                      <div class="small text-muted">Rp.
                         {{ number_format($tagihan['asuransi']) }}
                      </div>
                   </div>
                   <div class="d-flex justify-content-between fw-bold">
                      <div class="small text-muted">Rp.
                         {{ number_format($pendaki->tagihan) }}
                      </div>
                   </div>
                </div>
             </td>
          </tr>
          @endforeach
          <tr>
             <td class="fw-semibold text-end">Total : </td>
             <td class="text-end">Rp. {{ number_format($booking->total_pembayaran, 0, ',', '.') }}
             </td>
          </tr>
       </table>
    </div>
 </div>