<script>
    function cariTelepon(e) {
        const teleponItem = document.querySelector("#telepon-item").querySelectorAll("button");
        teleponItem.forEach((o, i) => {
            if (!(teleponItem[i].textContent.toLowerCase()).includes(e.target.value.toLowerCase()) && e.target
                .value !== "") {
                teleponItem[i].style.display = "none";
            } else {
                teleponItem[i].style.display = "block";
            }
        })
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('cariTelepon').addEventListener('input', cariTelepon);
        fetch('https://restcountries.com/v3.1/all')
            .then(response => response.json())
            .then(data => {
                countriesData = data;
                const teleponItem = document.querySelector('#telepon-item');

                data.forEach((country, index) => {
                    if (country.idd.root) {
                        teleponItem.appendChild(createCountryButton(country));
                    }
                });
            })
            .catch(error => console.error('Error fetching countries:', error));

        function createCountryButton(country) {
            const button = document.createElement('button');
            button.classList.add('dropdown-item');
            button.classList.add('w-100');

            const img = document.createElement('img');
            img.src = country.flags.png;
            img.width = 20;
            button.appendChild(img);
            button.href = "3";

            const code = country.idd.suffixes ? country.idd.suffixes[0] : ""
            const text = document.createTextNode(
                ` ${country.cca2} (${country.idd.root}${code})`
            );
            button.appendChild(text);

            return button;
        }

    });
</script>