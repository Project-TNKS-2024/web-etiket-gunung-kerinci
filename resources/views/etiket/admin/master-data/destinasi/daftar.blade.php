<table class="w-full">
    <thead>
        <tr>
            @foreach ($headers as $h)
            <th class="p-3 gk-bg-base-white font-bold">{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $d)
        <tr class="tiket-row">
            <td class="p-3 ">
                @if ($d->status)
                <span class="badge rounded-pill text-bg-success">Open</span>
                @else

                <span class="badge rounded-pill gk-bg-error200">Close</span>

                @endif
            </td>
            <td class="p-3 ">{{$d->nama}}</td>
            <td class="p-3 ">{{$d->kategori}}</td>
            <td class="p-3 ">{{$d->lokasi}}</td>
            <td class="p-3 ">{{$d->detail}}</td>
            <td class="p-3 d-flex gap-1 bg-transparent align-items-center justify-content-start">
                <a href="{{route('admin.destinasi.edit', ['id' => $d->id])}}" class="cursor-pointer">
                    <img width="25" class="gk-bg-primary100 rounded shadow-sm" src="{{asset('assets/icon/tnks-pen.svg')}}" class="bg-transparent" />
                </a>
                <a onclick="confirmDelete(event, '{{json_encode($d)}}',  `{{ route('admin.destinasi.hapus', ['id' => $d->id])}}`)" href="#" class="rounded cursor-pointer shadow-sm">
                    <img width="25" class="rounded shadow-sm" src="{{asset('assets/icon/tnks-bin.svg')}}" />
                </a>
                <div onclick="openModal({{ json_encode($gambar) }}.filter(o => o.id_destinasi === {{$d->id}}))">
                    <!-- <img class="gk-bg-success100 rounded shadow-sm" width="25" src="{{asset('assets/icon/tnks-detail.svg')}}" /> -->
                    <img class="gk-bg-success100 rounded shadow-sm" width="25" src="{{asset('assets/icon/img_rol.svg')}}" />
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    const jenisTiket = ['Weekday', 'Weekend'];

    function confirmDelete(event, data, rute) {
        const el = document.getElementById('modal-confirmation-container');
        document.getElementById('modal-confirmation-title').textContent = "Konfirmasi Hapus Tiket"
        const modalBody = document.getElementById('modal-confirmation-body');
        el.classList.remove("d-none");
        el.classList.add("d-flex")
        data = JSON.parse(data);
        console.log(rute)
        modalBody.innerHTML = "Konfirmasi hapus tiket pada id " + data['id'] + "?";

        const modalTarget = document.getElementById('modal-confirmation-target');
        modalTarget.classList.remove("bg-primary");
        modalTarget.classList.add("bg-danger");

        const modalForm = document.getElementById('modal-confirmation-form');
        modalForm.action = rute;
    }
</script>