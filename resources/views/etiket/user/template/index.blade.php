@extends('homepage.template.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('componen/tailwind-classes.css') }}">
    <style>
        .dashboard-sidebar.accessories {
            height: 141px;
        }

        .dashboard-sidebar {
            /* min-width: 338px; */
            min-height: 500px;
        }


        @media (max-width: 768px) {
            .dashboard-sidebar {
                min-height: 500px;
            }
        }

        label.mandatory::after {
            content: " *";
            color: red;
        }

        .custom-dropdown-item {
            width: fit-content;
            /* Set your desired width here */
        }

        .border-secondary {
            border-color: var(--neutrals500)
        }
    </style>

    @yield('sub-css')
@endsection

@section('main')
    <main class="container-fluid gk-bg-neutrals100 px-md-5" style="overflow: hidden">

        <div class="row mx-auto justify-content-center py-4" style="min-height: 500px;">

            <div class="col-12 col-sm-12 col-md-5 col-lg-3 dashboard-sidebar">
                @include('etiket.user.template.sidebar')
            </div>

            <div class="col-12 col-sm-12 col-md-7 col-lg-9 mt-4 mt-md-0">
                @yield('sub-main')
            </div>
        </div>
    </main>

    <div id="avatarPreviewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Pratinjau Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body text-center">
                    <img id="avatarPreviewImage" src="" class="img-fluid rounded" alt="Preview">
                    <!-- Hidden input to store the selected file -->
                    <input type="hidden" type="file" id="hiddenAvatarInput" accept="image/*" name="avatar">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <label for="avatar-sidebar" type="label" class="btn btn-warning" data-bs-dismiss="modal">Ubah</label>
                    <button onclick="formSubmit()" type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const avatarInput = document.getElementById("avatar-sidebar");
            const hiddenFileInput = document.getElementById("hiddenAvatarInput");

            if (avatarInput) {
                avatarInput.addEventListener("change", function(event) {
                    const file = event.target.files[0];
                    console.log('from function ', file);

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById("avatarPreviewImage").src = e.target.result;
                            new bootstrap.Modal(document.getElementById("avatarPreviewModal")).show();
                        };
                        reader.readAsDataURL(file);

                        // Clone file into hidden input
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);

                        console.log("from data transfer", dataTransfer.files[0]);
                        hiddenFileInput.file = dataTransfer.files[0];
                        console.log('transfered file', hiddenFileInput.file);
                    }
                });
                const form = document.getElementById('form-profile-sidebar');
            } else {
                console.error("Error: #avatar-sidebar file input not found in the DOM.");
            }
        });

        function formSubmit() {
            const form = document.querySelector('#form-profile-sidebar');
            console.log(form);
            if (!form) {
                console.error("Form not found!");
                return;
            }

            // Optional: Prevent submission if file input is empty
            const fileInput = document.getElementById('avatar-sidebar');
            if (fileInput && fileInput.files.length === 0) {
                alert("Please select a file before submitting.");
                return;
            }

            form.submit();
        }
    </script>
@endsection

<!-- menu active -->
<script>
    document.getElementById('avatar').addEventListener('change', function(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreviewImage').src = e.target.result;

                // Clone the selected file into the hidden input
                const hiddenFileInput = document.getElementById('hiddenAvatarInput');
                hiddenFileInput.files = fileInput.files;
                console.log(hiddenFileInput)

                // Show the modal
                new bootstrap.Modal(document.getElementById('avatarPreviewModal')).show();
            };
            reader.readAsDataURL(file);
        }
    });
</script>



@section('js')
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script>
        const qrcodes = document.querySelectorAll('.qrcode_kodebooking');
        qrcodes.forEach(e => {
            qr = e.dataset['qr'];
            new QRCode(e, {
                text: qr,
                width: 200,
                height: 200
            });
        });
    </script>
@endsection
