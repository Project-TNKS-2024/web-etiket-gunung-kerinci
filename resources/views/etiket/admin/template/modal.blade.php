<!-- ========================================= Modal Delete =========================================  -->
<!-- <a href="#" class="cursor-pointer shadow-sm"
    data-bs-toggle="modal"
    data-bs-target="#ModalDelete"
    data-bs-action="google.com"
    data-bs-input-hidden-input_hiddem="1"
    data-bs-input-text-input_text="isian text"
    data-bs-input-number-input_number="245"
    data-bs-input-date-input_date="2023-01-01"
    data-bs-title="title modal"
    data-bs-body="pesan modal">
</a> -->

<div class="modal fade" id="ModalDelete" tabindex="-1" aria-labelledby="ModalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <form id="deleteForm" method="POST" action="#">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalDeleteLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>...</p>
                    <!-- Tempat untuk menampung input yang akan ditambahkan -->
                    <div id="modalInputs"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Script untuk menampilkan modal delete
    const ModalDelete = document.getElementById('ModalDelete');
    ModalDelete.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget; // Tombol yang men-trigger modal
        const modalTitle = button.getAttribute('data-bs-title');
        const modalBody = button.getAttribute('data-bs-body');
        const modalActionUrl = button.getAttribute('data-bs-action'); // URL tujuan dari tombol Hapus
        const modal = this;

        // Mengatur konten title dan body
        modal.querySelector('.modal-title').textContent = modalTitle;
        modal.querySelector('.modal-body p').textContent = modalBody;
        modal.querySelector('#deleteForm').setAttribute('action', modalActionUrl);

        // Menghapus input lama jika ada
        const modalInputs = modal.querySelector('#modalInputs');
        modalInputs.innerHTML = ''; // Kosongkan isi sebelumnya

        // Menambahkan input dari semua atribut data-bs-input-*
        Array.from(button.attributes).forEach(attr => {
            if (attr.name.startsWith('data-bs-input-')) {
                // Mendapatkan nama input
                const inputAttr = attr.name.replace('data-bs-input-', ''); // Ambil nama input dari atribut
                const [inputType, inputName] = inputAttr.split('-');
                const inputValue = attr.value;

                // Membuat elemen input container dengan template literal
                let inputContainer;

                if (inputType === 'hidden') {
                    // Untuk tipe hidden, tidak ada label
                    inputContainer = `
                    <input type="hidden" name="${inputName}" value="${inputValue}">
                    `;
                } else {
                    // Untuk tipe lainnya, buat label dan input
                    inputContainer = `
                      <div class="mb-3">
                        <label for="mdlipt_${inputName}" class="form-label">${inputName.charAt(0).toUpperCase() + inputName.slice(1).replace(/_/g, ' ')}</label>
                        <input type="${inputType}" name="${inputName}" class="form-control" id="mdlipt_${inputName}" value="${inputValue}">
                    </div>
                    `;
                }

                // Tambahkan inputContainer ke modalInputs
                modalInputs.innerHTML += inputContainer; // Menambahkan HTML ke dalam modalInputs
            }
        });
    });
</script>