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
        <a
            href="http://e-andalan.net/"
            target="_blank"
            style="text-decoration: none; color: inherit; display: flex; align-items: center;"
        >
            <img
                src="{{ asset('sulsel.png') }}"
                alt="Logo Provinsi Sulawesi Selatan"
            />
        </a>
        <a
            href="http://e-andalan.net/"
            target="_blank"
            style="text-decoration: none; color: inherit; display: flex; align-items: center;"
        >
            <img
                src="{{ asset('logo1.png') }}"
                alt="Logo Smart School"
            />
        </a>
        <h1>Peta Sekolah</h1>
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
                .bindTooltip(`
                    <div style="font-size: 13px;">
                        <table>
                            <tr>
                                <td style="vertical-align: top;"><b>${school.name}</b></td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">NPSN</td>
                                <td style="vertical-align: top;">: ${school.npsn || '-'}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">Kepala Sekolah</td>
                                <td style="vertical-align: top;">: ${school.principal_name || '-'}</td>
                            </tr>
                        </table>
                    </div>
                    `, {
                    permanent: false,
                    direction: 'top',
                    offset: [0, -10],
                    opacity: 0.9
                })

                .bindPopup(`
                <div style="font-size: 14px; line-height: 1.4;">
                    <b>${school.name + ' (' + school.npsn + ')'}</b><br><hr>
                    <table style="width: 100%;">
                    <tr><td style="vertical-align: top;">Jumlah Siswa</td><td style="vertical-align: top;">: ${school.student_count || 0}</td></tr>
                    <tr><td style="vertical-align: top;">Jumlah Guru</td><td style="vertical-align: top;">: ${school.teacher_count || 0}</td></tr>
                    <tr><td style="vertical-align: top;">Fasilitas Praktik</td><td style="vertical-align: top;">: ${school.practice_facility || 'tidak tersedia'}</td></tr>
                    <tr><td style="vertical-align: top;">Fasilitas Olahraga</td><td style="vertical-align: top;">: ${school.sports_facility || 'tidak tersedia'}</td></tr>
                    <tr><td style="vertical-align: top;">Kurikulum</td><td style="vertical-align: top;">: ${school.curriculum || 'tidak tersedia'}</td></tr>
                    <tr><td style="vertical-align: top;">Ekstrakurikuler</td><td style="vertical-align: top;">: ${school.extracurricular || 'tidak tersedia'}</td></tr>
                    <tr><td style="vertical-align: top;">Prestasi</td><td style="vertical-align: top;">: ${school.achievements || 'tidak tersedia'}</td></tr>
                    <tr><td style="vertical-align: top;">Alamat</td><td style="vertical-align: top;">: ${school.address || 'tidak tersedia'}</td></tr>
                    </table>
                    <div style="text-align: center; margin-top: 10px;">
                        <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank"
                            style="display: none; padding: 6px 12px; background-color: #4285F4; color: white; text-decoration: none; border-radius: 4px;">
                            Lihat di Google Maps
                        </a>
                    </div>
                </div>
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
