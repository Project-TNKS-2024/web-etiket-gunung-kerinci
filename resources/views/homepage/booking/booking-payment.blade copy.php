@extends('homepage.template.index')


@section('css')
<style>
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

   /* payment detail */
   .icon-status-payment {
      height: 100px;
      background-size: cover;
   }

   .icon-status-payment.success {
      width: 100px;
      background-image: url("{{ asset('assets/img/dashboard/Successmark.png') }}");
   }
</style>

@endsection
@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => "Pembayaran"
])

<script>
</script>
<div class="container my-5">
   @include('homepage.booking.booking-nav', ['step' => $booking->status_booking])
   <div class="row">
      <div class="col-12 col-md-6 col-lg-5 m-auto">
         <div class="card">
            <div class="card-body pt-5 pb-4">
               @if ($statusPayment['status'] == 200)
               <div class="icon-status-payment success m-auto">
                  <p></p>
               </div>
               <h4 class="text-center mt-4 fw-bold mt-3">Pembayaran Berhasil</h4>
               @else
               <div class="icon-status-payment m-auto">
                  <p style="font-size: 50px;" class="fw-bold text-center">{{ $statusPayment['status'] }}</p>
               </div>
               <h4 class="text-center mt-4 fw-bold mt-3">Galat</h4>
               @endif
               <p style="color: gray;" class="text-center">{{ $statusPayment['message'] }}</p>
               <a href="{{ $statusPayment['redirect_url'] }}" class="btn btn-primary d-block m-auto">{{ $statusPayment['redirect_name'] }}</a>
            </div>
         </div>
      </div>
   </div>
</div>
</div>

@endsection
@section('js')

@endsection