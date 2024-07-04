<div class="d-flex flex-wrap flex-column justify-content-center align-items-center" style="height: 450px;">
    @foreach ($panduan as $index => $o)
        @if (($index + 1) % 2 != 0)
            <div class="col-md-3 card row-md-2 mb-3 p-3 " style="min-height: 150px">
                <div style="font-size: 16px" class="fw-bold py-1"><span
                        class="gk-text-primary700">#{{ $index + 1 }}</span>
                    {{ $o['title'] }}
                </div>
                <div style="font-size: 13px" class="">{{ $o['caption'] }}</div>
            </div>
        @else
            <div class="col-md-3 card row-md-2 px-2 mb-3 ml-2 p-2" style="margin-left: 50px;height: 150px">
                <div style="font-size: 16px" class="fw-bold"><span
                        class="gk-text-primary700">#{{ $index + 1 }}</span>
                    {{ $o['title'] }}
                </div>
                <div style="font-size: 13px"> {{ $o['caption'] }}</div>
            </div>
        @endif
    @endforeach
</div>
