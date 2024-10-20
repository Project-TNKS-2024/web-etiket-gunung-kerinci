<table class="w-full">
    <thead>
        <tr>
            @foreach ($headers as $header)
            <th class="p-3 gk-bg-base-white font-bold">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($checkpoints as $checkpoint)
        <tr class="tiket-row">
            <td class="p-3 ">{{$checkpoint->nama}}</td>
            <td class="p-3">{{ $checkpoint->deskripsi}}</td>
            <td class="p-3 ">{{$checkpoint->longitude}}</td>
            <td class="p-3 ">{{$checkpoint->latitude}}</td>
            <td class="p-3 ">berisi gambar QR</td>
            <td class="p-3 ">{{$checkpoint->urutan}}</td>
            <td class="p-3 d-flex gap-1 bg-transparent align-items-center justify-content-center">
                <a href="{{route('admin.checkpoint.edit', ['id' => $checkpoint->id])}}" class="bg-transparent rounded gk-bg-primary100 cursor-pointer shadow" style="background-color: transparent;"><img width="25" src="{{asset('assets/icon/tnks-pen.svg')}}" class="bg-transparent" /></a>
                <a onclick="confirmDelete(event, '{{json_encode($checkpoint)}}',  `{{ route('admin.checkpoint.hapus', ['id' => $checkpoint->id])}}`)" href="#" class="rounded cursor-pointer shadow-sm"><img width="25" src="{{asset('assets/icon/tnks-bin.svg')}}" /></a>
            </td>                                  
        </tr>
        @endforeach
    </tbody>
</table>
  
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
</script>