@foreach ($navlist as $nav)
    <li class="nav-item   ">
        <a href="{{ $nav['link'] }}" id="navigasi{{ $loop->index }}" class="nav-link text-center text-white rounded-lg py-2 py-md-0 px-2 px-md-2 px-sm-1  mx-2 "
            style="cursor: pointer; font-size: 14px;" aria-current="page" onclick="navigate(event,`{{ $nav['link'] }}`, `navigasi{{ $loop->index }}`)">
            {{ $nav['name'] }}
        </a>
    </li>
@endforeach

<script>
    function navigate(e, link, id) {}

    const navItems = document.querySelectorAll(".nav-link");
    for (let i = 0; i < navItems.length; i++) {
        if (window.location.href !== navItems[i].href) {
            navItems[i].classList.remove('gk-bg-base-white');
            navItems[i].classList.remove('gk-text-primary700');
            navItems[i].classList.remove('font-semibold');
            navItems[i].classList.add('text-white');
        } else {
            navItems[i].classList.add('gk-bg-base-white');
            navItems[i].classList.add('gk-text-primary700');
            navItems[i].classList.add('font-semibold');
            navItems[i].classList.remove('text-white');
        }
    }
</script>
