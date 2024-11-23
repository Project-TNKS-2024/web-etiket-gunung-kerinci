<script>
    function cariTelepon(e) {
        const teleponItem = document.querySelectorAll("#telepon-item .dropdown-item");
        teleponItem.forEach((item) => {
            const textContent = item.textContent.toLowerCase();
            const searchValue = e.target.value.toLowerCase();
            item.style.display = textContent.includes(searchValue) ? "block" : "none";
        });
    }

    inputDropdownCountry = document.getElementById('telp_country');
    btnDropdownCountry = document.getElementById('dropdown-country');

    function selectCountry(img, code) {

        // Set country code value in the hidden input
        inputDropdownCountry.value = code;

        // Update dropdown button with the selected flag
        btnDropdownCountry.textContent = "";
        btnDropdownCountry.appendChild(img.cloneNode(true));
        btnDropdownCountry.appendChild(document.createTextNode(code));
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('cariTelepon').addEventListener('input', cariTelepon);
        fetch('https://restcountries.com/v3.1/all')
            .then(response => response.json())
            .then(countriesData => {
                // ambil nilai asli dari input dropdown
                const defaultCountryCode = inputDropdownCountry.value;

                // ambil div itemm
                const teleponItem = document.querySelector('#telepon-item');
                countriesData.forEach((country, index) => {
                    if (country.idd.root) {
                        teleponItem.appendChild(createCountryButton(country));

                        if (country.idd.root + country.idd.suffixes[0] === defaultCountryCode) {
                            console.log(country.idd);

                            const img = document.createElement('img');
                            img.src = country.flags.png;
                            img.width = 20;
                            img.style.marginRight = '5px';

                            selectCountry(img, defaultCountryCode);
                        }
                    }
                });



            })
            .catch(error => console.error('Error fetching countries:', error));

        function createCountryButton(country) {
            const button = document.createElement('a');
            button.classList.add('dropdown-item');
            button.classList.add('w-100');

            const img = document.createElement('img');
            img.src = country.flags.png;
            img.width = 20;
            img.style.marginRight = '5px';
            button.appendChild(img);
            button.href = "#";

            // Get country code and name
            const code = country.idd.suffixes ? `${country.idd.root}${country.idd.suffixes[0]}` : country.idd.root;
            const text = document.createTextNode(` ${country.cca2} (${code})`);
            button.appendChild(text);

            // Add event listener for selecting country
            button.addEventListener('click', () => selectCountry(img, code));
            return button;
        }

    });
</script>