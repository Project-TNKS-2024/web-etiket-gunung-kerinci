@foreach ($navlist as $nav)
    <li class="nav-item">
        <a href="{{ $nav['link'] }}" id="navigasi{{ $loop->index }}" class="nav-link text-white py-0 rounded-lg"
            style="cursor: pointer; font-size: 14px;" aria-current="page"
            onclick="navigate(event,`{{ $nav['link'] }}`, `navigasi{{ $loop->index }}`)">{{ $nav['name'] }}</a>
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
