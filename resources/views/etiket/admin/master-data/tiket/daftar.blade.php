<table class="w-full rounded">
    <thead>
        <tr>
            @foreach (["Destinasi", "Nama", "Tipe", "Kategori", "Keterangan", "HTM", "Kuota", "Aksi"] as $h)
                <th class="p-3 gk-bg-base-white font-bold col">{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr class="tiket-row">
                <td class="p-3 font-medium col">{{$d->paket_tiket->destinasi->nama}}</td>
                <td class="p-3 font-medium col-1">{{$d->paket_tiket->nama}}</td>
                <td class="p-3 font-medium col">{{$d->kategori_hari == "wd" ? "Weekday" : "Weekend"}}</td>
                <td class="p-3 font-medium col">{{$d->kategori_pendaki == "wna" ? "Mancanegara" : "Nusantara"}}</td>
                <td class="p-3 font-medium col">{{$d->paket_tiket->keterangan}}</td>
                <td class="p-3 font-medium col">Rp {{number_format($d->harga_masuk)}}</td>
                {{-- <td class="p-3 font-medium col">Rp xxx</td> --}}
                <td class="p-3 font-medium col text-center">{!! $d->paket_tiket->min_pendaki == null ? '<span class="text-2xl">&infin;</span>' : $d->paket_tiket->min_pendaki !!}</td>

                <td class="p-3 d-flex gap-1 col">
                        <a  href="{{route('admin.tiket.edit', ['id' => $d->id])}}" class="cursor-pointer shadow-sm"><img width="25" src="{{asset('assets/img/logo/edit.png')}}"/></a>
                        <div onclick="confirmDelete(event, '{{json_encode($d)}}',  `{{ route('admin.tiket.hapus', ['id' => $d->id])}}`)"  class="cursor-pointer shadow-sm"><img width="25" src="{{asset('assets/img/logo/delete.png')}}"/></div>
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
