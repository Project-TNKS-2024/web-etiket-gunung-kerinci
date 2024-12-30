<style>
   /* modal preview file */
   .modal-dialog {
      height: calc(100vh - 50px);
      /* Kurangi sedikit untuk margin */
      margin: 15px auto;
      /* Margin atas dan bawah */
   }

   .modal-content {
      height: 100%;
   }

   .modal-body {
      height: calc(100% - 60px);
      /* Kurangi tinggi header dan footer modal */
      overflow: hidden;
      /* Agar tidak ada scroll di modal-body */
   }

   #filePreview {
      height: 100%;
      width: 100%;
      border: none;
   }
</style>

<!-- 

<div class="form-group ">
   <label for="lampiran_identitas" class="w-100 fw-bold mandatory">Lampiran </label>
   <div class="input-group flex-nowrap">
      <input class="form-control border-secondary" type="file" name="lampiran_identitas" id="lampiran_identitas">
      </input>
      <input type="hidden" value="lampiran_identitas" id="lampiran_identitas_existing">
      <button class="input-group-text d-none border-secondary" type="button" data-id-target="lampiran_identitas">
         <i class=" fa-regular fa-eye"></i>
      </button>
   </div>
</div>

 -->

<!-- Modal -->
<div class="modal fade" id="ModalShowFile" tabindex="-1">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5 my-0" id="exampleModalLabel">File Preview</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <embed id="filePreview" src="" type="application/pdf" style="width: 100%;" />
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>


<!-- script show upload file -->
<script>
   // Pilih modal berdasarkan ID
   const modalElement = document.getElementById('ModalShowFile');
   // Buat instance modal Bootstrap
   const modalInstance = new bootstrap.Modal(modalElement);

   // Pilih semua input file
   const inputFiles = document.querySelectorAll('input[type="file"]');

   document.addEventListener('DOMContentLoaded', function() {
      inputFiles.forEach(function(input) {
         // ambil id
         const idInput = input.getAttribute('id');
         const buttonShow = document.querySelector(`button[data-id-target="${idInput}"]`);
         const fileExist = document.querySelector(`input[id="${idInput}_existing"]`);
         const filePreview = document.getElementById('filePreview');

         // cek file ada atau tidak
         if (fileExist && fileExist.value) {
            buttonShow.classList.remove('d-none');
         }

         // beri event change
         input.addEventListener('change', function() {
            const file = input.files[0];
            const maxSize = 1 * 1024 * 1024; // 1MB

            if (file) {
               // Cek tipe file
               const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
               if (!validTypes.includes(file.type)) {
                  // error = 'Hanya file gambar (JPEG, PNG, GIF) dan PDF yang diizinkan.';
                  // panggil  notif error
                  console.log('Hanya file gambar (JPEG, PNG, GIF) dan PDF yang diizinkan.');
                  input.value = ''; // Reset input file
                  return;
               } else if (file.size > maxSize) {
                  // error = 'Ukuran file tidak boleh lebih dari 1MB.';
                  // panggil  notif error
                  console.log('Ukuran file tidak boleh lebih dari 1MB.');
                  input.value = ''; // Reset input file
                  return;
               } else {
                  console.log('File valid.');
                  buttonShow.classList.remove('d-none');
                  if (fileExist) {
                     fileExist.value = null;
                  }
               }
            }
         });

         buttonShow.addEventListener('click', function() {
            // tampilkan modal
            let fileURL = '';

            if (input.files.length > 0) {
               // Preview file yang baru diunggah
               const file = input.files[0];
               if (file.type === 'application/pdf' || file.type.startsWith('image/')) {
                  fileURL = URL.createObjectURL(file);
               } else {
                  alert('Jenis file tidak didukung untuk pratinjau.');
                  return;
               }
            } else if (fileExist.value) {
               // Preview file yang ada di server
               fileURL = fileExist.value; // Pastikan ini adalah URL file yang valid
            }

            if (fileURL) {
               filePreview.src = fileURL;
               modalInstance.show();
            }

         });
      });


   })
</script>