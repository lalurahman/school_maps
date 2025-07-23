<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    />
    <title>Peta Sekolah</title>
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
    />
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            display: flex;
            align-items: center;
            background-color: #ffffff;
            padding: 0.5em 1em;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header img {
            height: 50px;
            margin-right: 15px;
        }

        header h1 {
            font-size: 1.5em;
            color: #333;
            margin: 0;
        }

        #map {
            width: 100%;
            height: 90vh;
        }

        /* Tambahan di bagian style */
        #search-container {
            position: absolute;
            top: 80px;
            right: 20px;
            /* pindahkan ke kanan */
            z-index: 1000;
            background: white;
            padding: 10px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }


        #search-container input {
            width: 250px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #search-results {
            list-style: none;
            margin: 5px 0 0 0;
            padding: 0;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: white;
            position: absolute;
            width: 100%;
            z-index: 1001;
        }

        #search-results li {
            padding: 6px 10px;
            cursor: pointer;
        }

        #search-results li:hover {
            background-color: #f0f0f0;
        }

        #search-results .no-result {
            color: #888;
            font-style: italic;
            cursor: default;
        }

        #clear-search {
            position: absolute;
            top: 14px;
            right: 20px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #666;
        }
    </style>
</head>

<body>
    <header>
        <img
            src="{{ asset('sulsel.png') }}"
            alt="Logo Provinsi Sulawesi Selatan"
        />
        <h1>Peta Sekolah di Provinsi Sulawesi Selatan</h1>
    </header>
    @if (env('APP_DEBUG'))
        <div
            class="alert alert-warning alert-dismissible fade show mt-2"
            role="alert"
        >
            <strong>Mohon Maaf!</strong> Aplikasi ini sedang dalam pengembangan!
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    @else
        <div id="search-container">
            <input
                type="text"
                id="school-search"
                placeholder="Cari nama sekolah..."
                oninput="searchSchool()"
                autocomplete="off"
            />
            <button
                id="clear-search"
                onclick="clearSearch()"
            >×</button>
            <ul id="search-results"></ul>
        </div>
    @endif
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Get schools data from controller
        const schools = @json($schools);
        const map = L.map('map').setView([-5.135399, 119.423790], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const schoolIconSMA = L.icon({
            iconUrl: '{{ asset('school.png') }}', // pastikan ikon tersedia
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -35]
        });
        const schoolIconSMK = L.icon({
            iconUrl: '{{ asset('icon-smk.png') }}', // pastikan ikon tersedia
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -35]
        });
        // Array to hold markers    
        const markers = [];

        schools.forEach(school => {
            const lat = parseFloat(school.latitude);
            const lng = parseFloat(school.longitude);
            if (!lat || !lng) return;

            const marker = L.marker([lat, lng], {
                    icon: school.education_type === 'SMA' ? schoolIconSMA : schoolIconSMK
                })
                .addTo(map)
                .bindTooltip(
                    `<b>${school.name}</b><br>Kepala Sekolah: ${school.principal_name || ''}<br>Alamat: ${school.address || ''}`, {
                        permanent: false,
                        direction: 'top',
                        offset: [0, -10],
                        opacity: 0.9
                    })
                .bindPopup(`
            <b>${school.name + ' (' + school.npsn + ')'}</b><br>
            <b>Jumlah Siswa:</b> ${school.student_count || 0}<br>
            <b>Jumlah Guru:</b> ${school.teacher_count || 0}<br>
            <b>Fasilitas Praktik:</b> ${school.practice_facility || ''}<br>
            <b>Fasilitas Olahraga:</b> ${school.sports_facility || ''}<br>
            <b>Kurikulum:</b> ${school.curriculum || ''}<br>
            <b>Ekstrakurikuler:</b> ${school.extracurricular || ''}<br>
            <b>Prestasi:</b> ${school.achievements || ''}<br>
        `);

            marker.schoolName = school.name.toLowerCase();
            markers.push({
                marker,
                school
            });
        });

        function searchSchool() {
            const input = document.getElementById('school-search');
            const query = input.value.toLowerCase().trim();
            const resultsContainer = document.getElementById('search-results');
            resultsContainer.innerHTML = '';

            if (query === '') {
                map.setView([-5.135399, 119.423790], 13); // reset map
                return;
            }

            const filtered = markers.filter(({
                    school
                }) =>
                school.name.toLowerCase().includes(query)
            );

            if (filtered.length === 0) {
                const li = document.createElement('li');
                li.textContent = 'Sekolah tidak tersedia';
                li.className = 'no-result';
                resultsContainer.appendChild(li);
                return;
            }

            filtered.forEach(({
                school,
                marker
            }) => {
                const li = document.createElement('li');
                li.textContent = school.name;
                li.onclick = () => {
                    const lat = parseFloat(school.latitude);
                    const lng = parseFloat(school.longitude);
                    map.setView([lat, lng], 15);
                    marker.openPopup();
                    resultsContainer.innerHTML = '';
                    input.value = school.name;
                };
                resultsContainer.appendChild(li);
            });
        }

        // Fitur untuk menghapus pencarian
        function clearSearch() {
            const input = document.getElementById('school-search');
            input.value = '';
            document.getElementById('search-results').innerHTML = '';
            map.setView([-5.135399, 119.423790], 13); // kembali ke posisi default Sulawesi
        }
    </script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // show alert if APP_DEBUG is true
        if ({{ env('APP_DEBUG') ? 'true' : 'false' }}) {
            Swal.fire({
                title: 'Mohon Maaf!',
                text: 'Aplikasi ini sedang dalam pengembangan!',
                icon: 'warning',
                confirmButtonText: 'Tutup'
            });
        }
    </script> --}}
</body>

</html>
