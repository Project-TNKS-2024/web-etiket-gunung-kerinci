<script>
    let prov = [];
    let kab = [];
    let kec = [];
    let kel = [];

    $(document).ready(function() {
        $.getJSON('/assets/json/provinsi.json', function(data) {
            const daftarProvinsi = document.getElementById("daftar-provinsi");
            data.forEach((prov, id) => {
                const button = document.createElement("button");
                button.classList.add("dropdown-item", "w-100", "text-nowrap", "cursor-pointer");
                const text = document.createTextNode(`${prov.name}`);
                button.appendChild(text);
                button.addEventListener("click", function(event) {
                    const parent = document.querySelector("#dropdown-prov");
                    parent.textContent = prov.name;
                    pilihKabupaten(event, prov.id);
                });
                daftarProvinsi.appendChild(button);
            });
        });

        function pilihKabupaten(event, id) {
            event.preventDefault();
            $.getJSON('/assets/json/kabupaten.json', function(data) { // Adjusted path to kabupaten.json
                const daftarKab = document.getElementById("daftar-kab");
                daftarKab.innerHTML = ''; // Clear existing options
                const kabupaten = data.filter(o => o.provinsi_id === id);
                kabupaten.forEach(kab => {
                    const button = document.createElement("button");
                    button.classList.add("dropdown-item", "w-100", "text-nowrap",
                        "cursor-pointer");
                    const text = document.createTextNode(`${kab.name}`);
                    button.addEventListener("click", function(event) {
                        const parent = document.querySelector("#dropdown-kab");
                        parent.textContent = kab.name;
                        pilihKecamatan(event, kab.id);
                    });
                    button.appendChild(text);
                    daftarKab.appendChild(button);
                });
            });
        }

        function pilihKecamatan(event, id) {
            event.preventDefault();
            $.getJSON('/assets/json/kecamatan.json', function(data) { // Adjusted path to kabupaten.json
                const daftarKec = document.getElementById("daftar-kec");
                daftarKec.innerHTML = ''; // Clear existing options
                const kecamatan = data.filter(o => o.kabupaten_id === id);
                kecamatan.forEach(kec => {
                    const button = document.createElement("button");
                    button.classList.add("dropdown-item", "w-100", "text-nowrap",
                        "cursor-pointer");
                    const text = document.createTextNode(`${kec.name}`);
                    button.appendChild(text);
                    button.addEventListener("click", function(event) {
                        const parent = document.querySelector("#dropdown-kec");
                        parent.textContent = kec.name;
                        pilihKelurahan(event, kec.id);
                    });
                    daftarKec.appendChild(button);
                });
            });
        }

        function pilihKelurahan(event, id) {
            event.preventDefault();
            $.getJSON('/assets/json/kelurahan.json', function(data) { // Adjusted path to kabupaten.json
                const daftarKel = document.getElementById("daftar-kel");
                daftarKel.innerHTML = ''; // Clear existing options
                const kelurahan = data.filter(o => o.kecamatan_id === id);
                kelurahan.forEach(kec => {
                    const button = document.createElement("button");
                    button.classList.add("dropdown-item", "w-100", "text-nowrap",
                        "cursor-pointer");
                    const text = document.createTextNode(`${kec.name}`);
                    button.appendChild(text);
                    button.addEventListener("click", function(event) {
                        event.preventDefault();
                        const parent = document.querySelector("#dropdown-kel");
                        parent.textContent = kec.name;
                    });
                    daftarKel.appendChild(button);
                });
            });
        }
    });


    function cariItem(target, element) {
        const nodes = document.querySelector(`#${element}`).querySelectorAll("button");
        const value = document.querySelector(`#${target}`).value.toLowerCase();
        console.log(value);
        nodes.forEach(o => {
            if (!(o.outerText.toLowerCase()).includes(value) && value !== "") {
                o.style.display = "none";
            } else {
                o.style.display = "block";
            }
        });
    }
</script>
