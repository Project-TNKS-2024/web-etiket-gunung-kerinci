@extends('etiket.auth.template.index')


@section('css')
<style>
    .reset-form {
        max-width: 540px;
        padding: 40px 0;
        width: 100%;
    }
</style>
@endsection

@section('main')
<!-- <div class="position-fixed w-screen h-screen d-flex justify-content-center align-items-center" style="left:0; top:0;">
        <div class="text-xl font-semibold d-flex">
            <div class="px-3" style="border-right: 2px solid var(--neutrals500)">403</div>
            <div class="px-3">
                @if (isset($message))
                    {{ $message }}
                @else
                    Unauthorized Action.
                @endif
            </div>
        </div>
    </div> -->
<div class="card-body">
    <h5 class="mt-2 text-center fw-bold">403</h5>
    <div>
        <p class="fw-bold text-center my-5">Periksa kembali alamat URL</p>
        <div class="px-3 text-center">
            @if (isset($message))
            {{ $message }}
            @else
            Unauthorized Action.
            @endif
        </div>
    </div>
</div>
@endsection