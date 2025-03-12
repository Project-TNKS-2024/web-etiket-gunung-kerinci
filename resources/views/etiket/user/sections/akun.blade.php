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
    <div class="card shadow">
        <div class="card-body">
            <div class="container-fluid">
                <h4 class="font-semibold">Akun</h4>
                <form action="{{ route('user.dashboard.akun.action') }}" method="post" id="form-profile"
                    enctype="multipart/form-data"
                    class="{{ isset($user->biodata) && $user->biodata->verified !== 'unverified' ? 'input-none' : '' }}">
                    @csrf
                    <div class="row">
                        <div class="form-group col-12 iptFile-input">
                            <label for="avatar" class="w-100 fw-bold mandatory">Pilih Foto Profil</label>
                            <div class="input-group flex-nowrap">
                                <input class="form-control border-secondary" type="file" name="avatar" id="avatar"
                                    accept="image/*,.pdf">
                                @if (isset($user->biodata) &&
                                        isset($user->biodata->lampiran_identitas) &&
                                        file_exists(public_path($user->biodata->lampiran_identitas)))
                                    <input type="hidden" value="{{ asset($user->biodata->lampiran_identitas) }}"
                                        id="lampiran_identitas_existing">
                                @endif
                                <button class="input-group-text d-none border-secondary" type="button"
                                    data-id-target="avatar">
                                    <i class=" fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <span class="keterangan" style="font-size: 12px;">Foto Bebas, Max 500kb</span>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="row mb-2">
                        <div class="col d-flex justify-content-end">
                            <button type="submit" class="btn border-0 bg-linear-gradient-primary" name="action"
                                value="update">Konfirmasi Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
