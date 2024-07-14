@extends('etiket.user.dashboard')

@section('sub-css')
<style>
    .reset-form {
        max-width: 540px;
        padding: 40px 0;
        width: 100%;
    }
</style>
@endsection


@section('sub-main')
<div class="col py-5 px-4 my-5 my-md-0 d-flex justify-content-center align-items-center" style="min-height: 500px; overflow-y: auto;">
    <script>
        const sidebarMenu = document.querySelectorAll(".dashboard-sidebar-btn");
        sidebarMenu.forEach((o, i) => {
            sidebarMenu[i].classList.remove("active");
        });
        const password = document.querySelector("#dashboard-password");
        password.classList.add("active");
    </script>

    <form class="form h-full d-flex flex-column justify-content-center reset-form gk-bg-base-white px-4 rounded-2xl text-center" method="POST" id="resetForm" action="{{ route('user.dashboard.reset-password-action') }}">
        @csrf
        <header class="text-xl font-bold">Buat Kata Sandi Baru</header>
        <p class="gk-text-neutrals400 text-sm">Kata sandi baru Anda harus unik dari yang digunakan sebelumnya.</p>

        <div class="px-5">
            <div class="text-start form-group my-3">
                <label class="font-semibold form-label" for="new_password">Password Baru</label>
                <input class="form-control @error('password')  is-invalid @enderror " name="password_baru" id="password_baru" type="password" placeholder="Password Baru" required autofocus />
                @error('password')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
                @enderror
                <label class="text-start text-sm gk-text-neutrals400">Password <span id="passwordStrength"></span></label>
            </div>
            <div class="text-start form-group my-3">
                <label class="font-semibold" for="password_baru">Konfirmasi Password Baru</label>
                <input class="form-control" name="konfirmasi_password_baru" id="konfirmasi_password_baru" type="password" oninput="confirmPassword" placeholder="Password Baru" required />
                <label class="text-start text-sm gk-text-neutrals400" id="passwordConfirmed"></label>
            </div>
            <div class="text-start form-group my-4">
                <button type="submit" class="btn btn-primary w-100 py-2 text-sm gk-bg-primary800 border-0">Atur Ulang
                    Kata
                    Sandi</button>
            </div>
            <div class="text-start my-4">
                <button onclick="changepage(event)" class="btn btn-outline-secondary w-100 py-2 text-sm border-0">
                    Check Halaman Success
                </button>
            </div>
        </div>
    </form>

    <div class="form h-full d-flex flex-column justify-content-center reset-form gk-bg-base-white px-4 rounded-2xl text-center d-none align-items-center" onclick="changepage(event)" id="resetSuccess">
        <img src="{{ asset('assets/img/dashboard/Successmark.png') }}" width="120" />
        <h3 class="text-xl font-bold mt-4">Atur Ulang Password Berhasil</h3>
        <p class="text-sm gk-text-neutrals400">Password anda telah sukses di ubah</p>
    </div>


</div>
<script>
    document.getElementById('password_baru').addEventListener('input', checkPasswordStrength);

    document.getElementById('password_baru').addEventListener('input', checkPasswords);
    document.getElementById('konfirmasi_password_baru').addEventListener('input', checkPasswords);

    function checkPasswords() {
        const password = document.getElementById('password_baru').value;
        const confirmPassword = document.getElementById('konfirmasi_password_baru').value;
        const passwordConfirmedLabel = document.getElementById('passwordConfirmed');
        console.log(password === confirmPassword)
        if (password && confirmPassword) {
            if (password === confirmPassword) {
                passwordConfirmedLabel.textContent = "Passwords match";
                passwordConfirmedLabel.style.color = "green";
            } else {
                passwordConfirmedLabel.textContent = "Passwords do not match";
                passwordConfirmedLabel.style.color = "red";
            }
        } else {
            passwordConfirmedLabel.textContent = "";
        }
    }

    function checkPasswordStrength() {
        var password = document.getElementById('password_baru').value;
        var strengthSpan = document.getElementById('passwordStrength');
        var strength = getPasswordStrength(password);

        strengthSpan.textContent = strength.message;
        strengthSpan.style.color = strength.color
    }

    function getPasswordStrength(password) {
        let point = 0;
        let arrayTest = [/[0-9]/, /[a-z]/, /[A-Z]/, /[^0-9a-zA-Z]/];
        arrayTest.forEach((item) => {
            if (item.test(password)) {
                point += 1;
            }
        });

        if (point > 0) {
            if (password.length < 6) {
                point--;
            }
        }

        let strength = {
            message: "",
            color: ""
        };

        switch (point) {
            case 0:
            case 1:
                strength.message = "Sangat Lemah";
                strength.color = "red";
                break;
            case 2:
                strength.message = "Lemah";
                strength.color = "orange";
                break;
            case 3:
                strength.message = "Lumayan";
                strength.color = "yellow";
                break;
            case 4:
                strength.message = "Kuat";
                strength.color = "green";
                break;
        }
        return strength;
    }

    function changepage(e) {
        e.preventDefault();
        const form = document.getElementById("resetForm");
        const successPage = document.getElementById("resetSuccess");

        if (form.classList.contains("d-flex")) {
            form.classList.remove("d-flex");
            form.classList.add("d-none");

            successPage.classList.add("d-flex");
            successPage.classList.remove("d-none");
        } else {
            successPage.classList.remove("d-flex");
            successPage.classList.add("d-none");

            form.classList.add("d-flex");
            form.classList.remove("d-none");
        }
    }
</script>
<script></script>
@endsection