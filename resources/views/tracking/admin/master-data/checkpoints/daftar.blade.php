<table class="w-full">
    <thead>
        <tr>
            @foreach ($headers as $header)
            <th class="p-3 gk-bg-base-white font-bold text-center">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($checkpoints as $checkpoint)
        <tr class="tiket-row">
            <td class="p-3 text-center">{{$checkpoint->nama}}</td>
            <td class="p-3 text-center">{{ $checkpoint->deskripsi_naik}}</td>
            <td class="p-3 text-center">{{ $checkpoint->deskripsi_turun}}</td>
            <td class="p-3 text-center">{{$checkpoint->longitude}}</td>
            <td class="p-3 text-center">{{$checkpoint->latitude}}</td>
            <td class="p-3 align-items-center">
                <div id="qrcode-{{ $checkpoint->id }}"></div>
            </td>
            <td class="p-3 text-center">{{$checkpoint->urutan}}</td>
            <td class="p-3 d-flex gap-1 bg-transparent align-items-center justify-content-center">
                <a href="{{route('admin.checkpoint.edit', ['id' => $checkpoint->id])}}" class="bg-transparent rounded gk-bg-primary100 cursor-pointer shadow" style="background-color: transparent;"><img width="25" src="{{asset('assets/icon/tnks-pen.svg')}}" class="bg-transparent" /></a>
                <a onclick="confirmDelete(event, '{{json_encode($checkpoint)}}',  `{{ route('admin.checkpoint.hapus', ['id' => $checkpoint->id])}}`)" href="#" class="rounded cursor-pointer shadow-sm"><img width="25" src="{{asset('assets/icon/tnks-bin.svg')}}" /></a>
            </td>                                  
        </tr>
        @endforeach
    </tbody>
</table>

<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
    
    function confirmDelete(event, data, rute) {
        const el = document.getElementById('modal-confirmation-container');
        document.getElementById('modal-confirmation-title').textContent = "Konfirmasi Hapus Tiket"
        const modalBody = document.getElementById('modal-confirmation-body');
        el.classList.remove("d-none");
        el.classList.add("d-flex")
        data = JSON.parse(data);
        console.log(rute)
        modalBody.innerHTML = "Konfirmasi hapus " + data['nama'] + "?";

        const modalTarget = document.getElementById('modal-confirmation-target');
        modalTarget.classList.remove("bg-primary");
        modalTarget.classList.add("bg-danger");

        const modalForm = document.getElementById('modal-confirmation-form');
        modalForm.action = rute;
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        @foreach ($checkpoints as $checkpoint)
        // Membuat QR Code dengan ID unik berdasarkan checkpoint ID
        new QRCode(document.getElementById("qrcode-{{ $checkpoint->id }}"), {
            text: "Nama: {{ $checkpoint->nama }}, Longitude: {{ $checkpoint->longitude }}, Latitude: {{ $checkpoint->latitude }}",
            width: 128, // Ukuran QR Code
            height: 128, // Ukuran QR Code
        });
        @endforeach
    });
</script>