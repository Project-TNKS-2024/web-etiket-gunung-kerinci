<div class="d-none d-md-block" style="margin: 100px 0;">
    <div class="guide-container guide-card-border-b ">
        @for ($i = 0; $i < count($panduan); $i++)
            @if ($i % 2 == 0)
                <div class="d-flex flex-column align-items-center guide-right">
                    <div class="guide-card card shadow-sm">
                        <header class="fw-bold"><span>#{{ $i + 1 }}</span> {{ $panduan[$i]['title'] }}</header>
                        <main>{{ $panduan[$i]['caption'] }}</main>
                    </div>
                    <div class="guide-card-tail"></div>
                    <div class="guide-card-div"></div>
                </div>
            @endif
        @endfor
    </div>

    <div class="guide-container guide-card-border-t" style="margin-left: 0px;">
        @for ($i = 0; $i < count($panduan); $i++)
            @if ($i % 2 == 0)
                <div class="d-flex flex-column align-items-center guide-left">
                    <div class="guide-card-head"></div>
                    <div class="guide-card card shadow-sm">
                        <header class="fw-bold"><span>#{{ $i + 2 }}</span> {{ $panduan[$i + 1]['title'] }}
                        </header>
                        <main>{{ $panduan[$i + 1]['caption'] }}</main>
                    </div>
                    <div class="guide-card-div"></div>
                </div>
            @endif
        @endfor
    </div>
</div>

<div class="d-flex d-md-none flex-column w-100 align-items-center" style="margin: 10px 0">
    <div class="guide-container guide-card-border-t" style="margin-left: 0px;">
        @for ($i = 0; $i < count($panduan); $i++)
            <div class="d-flex flex-column align-items-center ">
                <div class="guide-card-head"></div>
                <div class="guide-card card shadow-sm">
                    <header class="fw-bold"><span>#{{ $i + 1 }}</span> {{ $panduan[$i]['title'] }}
                    </header>
                    <main>{{ $panduan[$i]['caption'] }}</main>
                </div>
                <div class="guide-card-div"></div>
            </div>
        @endfor
    </div>
</div>

{{-- <div class="d-flex flex-column w-100 align-items-start mx-0" style="margin: 100px 0">
    <div class="guide-container m-0 mx-0">
        @for ($i = 0; $i < count($panduan); $i++)
            <div class="d-flex flex-column align-items-center guide-right m-0 @if (($i + 1) % 2 != 0) guide-card-border-b guide-right @else guide-left @endif"
                style="max-width: 300px">
                @if (($i + 1) % 2 == 0)
                    <div class="guide-card-head"></div>
                @endif
                <div class="guide-card card shadow-sm ">
                    <header class="fw-bold"><span>#{{ $i + 1 }}</span> {{ $panduan[$i]['title'] }}</header>
                    <main>{{ $panduan[$i]['caption'] }}</main>
                </div>
                @if (($i + 1) % 2 != 0)
                    <div class="guide-card-tail"></div>
                @endif
                {{-- <div class="guide-card-tail"></div> --}}
{{-- </div>
@endfor
</div>
</div> --}}
