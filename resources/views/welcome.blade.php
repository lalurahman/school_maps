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
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            background-color: #4caf50;
            color: white;
            margin: 0;
            padding: 0.5em 0;
        }

        #map {
            width: 100%;
            height: 90vh;
        }
    </style>
</head>

<body>
    <h1>Peta Sekolah Di Provinsi Sulawesi Selatan</h1>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Get schools data from controller
        const schools = @json($schools);

        // const map = L.map('map').setView([-5.135399, 119.423790], 13);
        const map = L.map('map').setView([-5.135399, 119.423790], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const schoolIcon = L.icon({
            iconUrl: '{{ asset('school.png') }}', // pastikan ikon tersedia
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -35]
        });

        // Loop through schools data from controller
        schools.forEach(school => {
            console.log(school);
            const lat = parseFloat(school.latitude);
            const lng = parseFloat(school.longitude);
            if (!lat || !lng) return;

            L.marker([lat, lng], {
                    icon: schoolIcon
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
        });
    </script>
</body>

</html>
