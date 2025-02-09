<script>
    function addVisibilityToggle(id) {
        const element = document.getElementById(id);

        if (!element) {
            console.error(`Element with ID "${id}" not found.`);
            return;
        }

        const parentElement = document.createElement('div');
        parentElement.classList.add('position-relative', 'w-100');
        element.parentNode.insertBefore(parentElement, element);
        parentElement.appendChild(element);

        const iconContainer = document.createElement('div');
        iconContainer.classList.add('position-absolute', 'align-items-center', 'd-flex', 'px-2');
        iconContainer.style.width = 'fit-content';
        iconContainer.style.cursor = 'pointer';
        iconContainer.style.height = '100%';
        iconContainer.style.background = 'transparent';
        iconContainer.style.top = '0';
        iconContainer.style.right = '0';


        iconContainer.innerHTML = `
<div class="position-relative d-flex align-items-center justify-content-center">
    <img src="{{ asset('assets/icon/tnks/eye.svg') }}" />
    <div id='${id}-toggler' class='position-absolute'
        style='width: 100%; height: 90%; border-right: 2px solid rgba(152, 162, 179, 1); transform: rotate(-45deg) scale(0); top: 35%; left: -30%; transition: height 0.3s ease, transform 0.3s ease;'>
    </div>
</div>
`;


        iconContainer.addEventListener('click', function(e) {

            const toggler = document.getElementById(id + '-toggler');
            console.log(element.type);
            // element.type = element.type == 'password' ? 'text' : 'password';
            if (element.type == 'password') {
                element.type = 'text';
                toggler.style.transform = 'rotate(-45deg) scale(1)';
            } else {
                element.type = 'password'
                toggler.style.transform = 'rotate(-45deg) scale(0)';
            }
        });
        parentElement.appendChild(iconContainer);
    }
</script>