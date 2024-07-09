<div class="row mt-3 index-kartu-1">
    @foreach ($destinasi as $item)
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <img src="{{ $item['cover'] }}" class="card-img-top" alt="Jalur Pendakian Kersik Tuo"
                    style="object-fit: cover; max-height: fit-content; height: 100%;">
                <div class="card-body">
                    <h5 class="card-title">{{ $item['name'] }} <span class="ms-2 badge gk-bg-primary400">Open</span>
                    </h5>
                    <p class="card-text index-text" style="height: auto">
                        {{ $item['description'] }}
                    </p>
                    <a href="{{ $item['target'] }}" class="btn btn-primary w-100 gk-text-base-white">Pilih Jalur
                        Pendakian</a>
                </div>
            </div>
        </div>
    @endforeach
</div>
