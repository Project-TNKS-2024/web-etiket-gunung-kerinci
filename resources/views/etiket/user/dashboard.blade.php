@extends('homepage.template.index')

@section('main')

<main class="container content my-4">
   <div class="container-fluid p-0">

      <div class="row">
         <div class="col-md-4 col-xl-3">
            @include('etiket.user.template.sidebar')
         </div>

         <div class="col-md-8 col-xl-9">
            <div class="card">
               <div class="card-header">

                  <h5 class="card-title mb-0">Dashboard </h5>
               </div>
               <div class="card-body h-100">

               </div>
            </div>
         </div>


      </div>
   </div>
</main>

</main>

@endsection