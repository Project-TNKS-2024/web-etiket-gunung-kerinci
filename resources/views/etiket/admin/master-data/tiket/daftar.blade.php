<table class="w-full rounded">
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
            <td class="p-3 font-bold">{{$d->nama}}</td>
            <td class="p-3 font-bold">{{$d->kategori->nama}}</td>
            <td class="p-3 font-bold">{{$d->golongan->nama}}</td>
            <td class="p-3 font-bold">{{$d->tipe}}</td>
            <td class="p-3 font-bold">{{$d->destinasi->nama}}</td>
            <td class="p-3 font-bold">{{$d->gk_gate->nama}}</td>
            <td class="p-3 font-bold">{{$d->keterangan}}</td>
            <td class="p-3 font-bold">Rp {{number_format($d->harga)}}</td>
            <td class="p-3 d-flex gap-1">
                <a href="{{route('admin.tiket.edit', ['id' => $d->id])}}" class="cursor-pointer shadow-sm"><img width="25" src="{{asset('assets/img/logo/edit.png')}}" /></a>
                <div onclick="confirmDelete(event, '{{json_encode($d)}}',  `{{ route('admin.tiket.hapus', ['id' => $d->id])}}`)" class="cursor-pointer shadow-sm"><img width="25" src="{{asset('assets/img/logo/delete.png')}}" /></div>
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
        modalBody.innerHTML = "Konfirmasi hapus destinasi pada id " + data['id'];

        const modalTarget = document.getElementById('modal-confirmation-target');
        modalTarget.classList.remove("bg-primary");
        modalTarget.classList.add("bg-danger");

        const modalForm = document.getElementById('modal-confirmation-form');
        modalForm.action = rute;
    }
</script>