<div class="toast-container" id="toastContainer">
    @if(session('success'))
    @foreach((array) session('success') as $message)
    <div class="toast align-items-center text-bg-primary border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ $message }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endforeach
    @endif

    @if ($errors->any())
    <div class="d-block">
        @foreach ($errors->all() as $error)
        <div class="toast align-items-center text-bg-danger border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ $error }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endforeach
    </div>
    @endif


    @if(session('error'))
    @foreach((array) session('error') as $message)
    <div class="toast align-items-center text-bg-danger border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ $message }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endforeach
    @endif
</div>

<!-- Bootstrap and jQuery scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toastElements = document.querySelectorAll('.toast');
        toastElements.forEach(toastElement => {
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000 // 2000 milliseconds = 2 seconds
            });
            toast.show();
        });
    });
</script>