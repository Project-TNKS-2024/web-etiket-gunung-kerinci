@extends('etiket.user.template.index')

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
<div class="card shadow">
    <div class="card-body">
        <div class="container-fluid">
            <h4 class="font-semibold">Buat Kata Sandi Baru</h4>
            <p class="gk-text-neutrals400 text-sm">Kata sandi baru Anda harus unik dari yang digunakan sebelumnya.</p>


            <form class="form" method="POST" id="resetForm" action="{{ route('user.dashboard.reset-password-action') }}">
                @csrf

                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="text-start form-group">
                            <label class="font-semibold form-label" for="new_password">Password Baru</label>

                            <div class="input-group">
                                <input type="password" class="form-control @error('password_baru') is-invalid @enderror" name="password_baru" id="password_baru" type="password" placeholder="Password Baru"
                                    required autofocus autocomplete="current-password">
                                <span class="input-group-text btn-visibility" data-target='password_baru'><i class="fa-solid fa-eye"></i> </i></span>
                            </div>

                            <label class="text-start text-sm gk-text-neutrals400">
                                Password <span id="passwordStrength"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="text-start form-group">

                            <label class="font-semibold form-label" for="password_baru">Konfirmasi Password Baru</label>

                            <div class="input-group">
                                <input class="form-control" name="password_baru_confirmation" id="password_baru_confirmation" type="password" placeholder="Password Baru" required />
                                <span class="input-group-text btn-visibility" data-target='password_baru_confirmation'><i class="fa-solid fa-eye"></i> </i></span>
                            </div>

                            <label class="text-start text-sm gk-text-neutrals400" id="passwordConfirmed"></label>
                        </div>
                        <div class="text-start form-group mt-2">
                            <button type=" submit" class="btn btn-gl-primary ">Atur Ulang Kata Sandi</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnVisibilityPassword = document.querySelectorAll(".btn-visibility");
        btnVisibilityPassword.forEach(btn => {
            btn.addEventListener("click", function() {
                const ipt = document.getElementById(btn.dataset.target);
                if (ipt.type === "password") {
                    ipt.type = "text";
                    btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
                } else {
                    ipt.type = "password";
                    btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
                }
            });
        });
    });
</script>

<script>
    document.getElementById('password_baru').addEventListener('input', checkPasswordStrength);

    document.getElementById('password_baru').addEventListener('input', checkPasswords);
    document.getElementById('password_baru_confirmation').addEventListener('input', checkPasswords);

    function checkPasswords() {
        const password = document.getElementById('password_baru').value;
        const confirmPassword = document.getElementById('password_baru_confirmation').value;
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
</script>
@endsection