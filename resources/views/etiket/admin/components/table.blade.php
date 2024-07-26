<table>
    <thead>
        <tr>
            @foreach ($headers as $h)
                <th class="p-3 gk-bg-base-white font-bold">{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr>
                <td class="p-3 gk-bg-base-white text-black">
                    {{ $d['destinasi'] }}
                </td>
                <td class="p-3 gk-bg-base-white text-black">
                    {{ $d['namaTiket'] }}
                </td>
                <td class="p-3 gk-bg-base-white text-black">
                    {{ $d['gateMasuk'] }}
                </td>
                <td class="p-3 gk-bg-base-white text-black">
                    {{ $d['jenisTiket'] }}
                </td>
                <td class="p-3 gk-bg-base-white text-black">
                    {{ $d['hargaTiket'] }}
                </td>
                <td class="p-3 gk-bg-base-white text-black">
                    <div class="d-flex gap-2">
                        <img width="25" src="{{asset('assets/img/logo/edit.png')}}" />
                        <img width="25" src="{{asset('assets/img/logo/delete.png')}}" />
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
