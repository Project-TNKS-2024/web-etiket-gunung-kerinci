@extends('homepage.template.index')

@section('css')
<style>

</style>
@endsection

@section('main')
@include('homepage.template.header', [
'title' => 'Pendakian Gunung Kerinci',
'caption' => 'Tiket',
])

<div class="container my-5">

   <div class="container ">
      <div class="text-end mb-3">
         <button type="button" class="btn btn-primary">Unduh Tiket</button>
      </div>

      <div id="container-print overflow-x-auto">
         @include('homepage.template.tiket.cardBooking', [$booking])
      </div>

   </div>

</div>

</div>
@endsection


@section('js')
<script>

</script>
@endsection