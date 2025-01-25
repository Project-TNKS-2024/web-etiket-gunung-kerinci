<div class="d-none d-md-block" style="margin: 100px 0;">
    <div class="guide-container guide-card-border-b mx-auto">
        @for ($i = 0; $i < count($panduan); $i++)
            @if ($i % 2 == 0)
                <div class="d-flex flex-column align-items-center justify-content-end guide-right" style="">
                    <div class="guide-card card shadow-sm" style="max-width: 400px;">
                        <header class="fw-bold"><span>#{{ $i + 1 }}</span> {{ $panduan[$i]['title'] }}</header>
                        <main>
                            <div>{{ $panduan[$i]['caption'] }}</div>
                            <script>console.log('{{$panduan[$i]["title"]}}', @json($panduan[$i]['details']))</script>
                            <div>
                                @foreach ($panduan[$i]['details'] as $det)
                                {{$det}}
                                @endforeach
                            </div>
                        </main>
                    </div>
                    <div class="guide-card-tail py-4" style=""></div>
                    <div class="guide-card-div"></div>
                </div>
            @endif
        @endfor
    </div>

    <div class="guide-container guide-card-border-t mx-auto" style="margin-left: 0px;">
        @for ($i = 0; $i < count($panduan); $i++)
            @if ($i % 2 == 0)
                <div class="d-flex flex-column align-items-center guide-left">
                    <div class="guide-card-head"></div>
                    <div class="guide-card card shadow-sm" style="max-width: 400px;">
                        <header class="fw-bold"><span>#{{ $i + 2 }}</span> {{ $panduan[$i + 1]['title'] }}
                        </header>
                        <main>
                            <div>{{ $panduan[$i+1]['caption'] }}</div>
                            <script>console.log('{{$panduan[$i+1]["title"]}}', @json($panduan[$i+1]['details']))</script>
                            <ul>
                                @foreach ($panduan[$i+1]['details'] as $det)
                                <li>{{$det}}</li>
                                @endforeach
                            </ul>
                        </main>
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
