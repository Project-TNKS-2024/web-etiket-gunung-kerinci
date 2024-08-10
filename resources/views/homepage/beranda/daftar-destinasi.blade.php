<div class="row mt-3 index-kartu-1">
    @foreach ($destinasi as $item)
    <div class="col-12 col-md-6 col-lg-4 mb-3">
        <div class="card h-100">

            <?php
            // echo "<script>console.log('link " . $item['foto'] . "');</script>";
            if (isset($item['foto'])) {
                $headers = @get_headers($item['foto']);
                if ($headers && strpos($headers[0], '200')) {
                    // echo "<script>console.log('Foto ada');</script>";
                } else {
                    // echo "<script>console.log('Link foto tidak valid');</script>";
                    $item['foto'] = asset('assets/img/cover/kerinci.png');
                }
            } else {
                // echo "<script>console.log('Link foto tidak ada');</script>";
                $item['foto'] = asset('assets/img/cover/kerinci.png');
            }
            ?>

            <img src="{{$item['foto']}}" class="card-img-top" alt="Jalur Pendakian Kersik Tuo" style="object-fit: cover; max-height: fit-content; height: 100%;">
            <div class="card-body">
                <h5 class="card-title">{{ $item['nama'] }}
                    @if ($item['status']== 1)
                    <span class="ms-2 badge gk-bg-primary400">Open</span>
                    @elseif ($item['status'] == 0)
                    <span class="ms-2 badge gk-bg-error200">Close</span>
                    @endif
                </h5>
                <p class="card-text index-text" style="height: auto">
                    {{ $item['detail'] }}
                </p>
                <!-- <a href="{{ $item['id'] }}" class="btn btn-primary w-100 gk-text-base-white">Pilih Jalur
                    Pendakian</a> -->
            </div>
        </div>
    </div>
    @endforeach
</div>