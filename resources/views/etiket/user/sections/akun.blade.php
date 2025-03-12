@extends('etiket.user.template.index')

@section('sub-css')
    <style>
        .form-group {
            margin-bottom: 10px;
        }

        .input-none input,
        .input-none select,
        .input-none .dropdown-notelp {
            pointer-events: none;
            background-color: #e9ecef;
            opacity: 1;
        }

        .input-none .iptFile-label {
            display: block;
        }

        .iptFile-label {
            display: none;
        }

        .input-none .iptFile-input {
            display: none;
        }
    </style>
@endsection


@section('sub-main')
@endsection
