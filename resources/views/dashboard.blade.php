<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - ShalMonic</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite('resources/js/app.js')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
      body{background:linear-gradient(135deg,#f8fafc,#e0e7ff,#c7f9cc,#fceabb);background-size:400% 400%;animation:gradient-animation 10s ease infinite;font-family:'Figtree',sans-serif}.glass-widget{background:rgba(255,255,255,.75);backdrop-filter:blur(16px) saturate(180%);-webkit-backdrop-filter:blur(16px) saturate(180%);border-radius:1.5rem;border:1px solid rgba(229,229,234,.8);box-shadow:0 8px 32px rgba(0,0,0,.07)}.sensor-label{font-size:.95rem;font-weight:500;color:#64748b}.sensor-value{font-size:2rem;font-weight:700;color:#0f172a}.sensor-unit{font-size:1rem;font-weight:500;color:#64748b}.status-indicator.online{background-color:#22c55e;animation:pulse 2s infinite}.status-indicator.offline{background-color:#ef4444}.chart-container{position:relative;height:250px;width:100%}.electrical-item{display:flex;align-items:center;justify-content:space-between;padding:.75rem;background-color:rgba(120,120,128,.08);border-radius:.75rem}@keyframes gradient-animation{0%{background-position:0 50%}50%{background-position:100% 50%}100%{background-position:0 50%}}@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,.7)}50%{box-shadow:0 0 0 6px rgba(34,197,94,0)}}
    .btn-gradient {
      background: linear-gradient(90deg, #38bdf8 0%, #4ade80 100%);
      color: white;
      transition: background 0.3s, transform 0.2s;
      box-shadow: 0 2px 8px rgba(56,189,248,0.15);
    }
    .btn-gradient:hover {
      background: linear-gradient(90deg, #4ade80 0%, #38bdf8 100%);
      transform: translateY(-2px) scale(1.03);
      box-shadow: 0 6px 20px rgba(56,189,248,0.18);
    }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center"><a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-75 transition-opacity"><svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/><path d="M12 2L3 7L12 12L21 7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/><path d="M12 22V12" stroke="teal" stroke-width="2" stroke-linejoin="round"/></svg><span class="text-2xl font-bold">ShalMonic</span></a></div>
                <div class="hidden sm:ml-1 sm:flex sm:items-center">
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 text-teal-600 border-b-2 border-teal-600">Dashboard</a>
                    
                    @if(auth()->user()->isAdmin() || auth()->user()->isOperator())
                        <a href="{{ route('controls') }}" class="px-5 py-2 text-gray-500 hover:text-gray-900">Controls</a>
                        <a href="{{ route('dataoverview') }}" class="px-5 py-2 text-gray-500 hover:text-gray-900">Data Overview</a>
                    @endif
                    
                    @if(auth()->user()->isAdmin())
                        {{-- <a href="{{ route('settings') }}" class="px-5 py-2 text-gray-500 hover:text-gray-900">Settings</a> --}}
                    @endif
                </div>
                <div class="flex items-center"><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full text-sm bg-teal-600 text-white rounded-full hover:bg-teal-700 px-4 py-2">Logout</button></form></div>
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full max-w-7xl mx-auto py-6 px-4 sm:px-8 lg:px-16">
        <div class="flex justify-between items-start sm:items-center mb-2">
            <div><h2 class="text-teal-800 text-3xl sm:text-4xl font-bold mt-1 mb-2">Welcome back, {{ Auth::user()->name }}!</h2><p class="text-gray-500">Here's the latest update from your monitoring system.</p></div>
            <div class="text-right flex-shrink-0"><div id="header-time" class="text-xl font-bold text-gray-700"></div><div id="header-date" class="text-gray-500"></div>
            <div class="flex items-center justify-end gap-2 mt-1 text-xs">
              <div class="status-indicator w-2 h-2 rounded-full"></div>
              <span id="device-status" class="font-semibold text-gray-700"></span>
              <span class="text-gray-400">·</span><span id="update-interval" class="text-gray-500"></span>
            </div>
          </div>
        </div>
        <div class="flex flex-col lg:flex-row items-center gap-6 mb-4">
        <div class="w-full lg:w-2/3">
            <div class="flex items-center gap-2">
                <div class="flex-shrink-0">
                    <img src="{{ asset('assets/shallots.jpg') }}" alt="True Shallot Seed" class="w-28 h-28 rounded-full object-cover border-2 border-white shadow-lg">
                </div>
                <div class="p-3 sm:p-3 w-full">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2">Cultivation Information</h3>
                    <div class="glass-widget p-4 sm:p-3 grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                        <div>
                            <p class="font-semibold text-gray-800">Plantation</p>
                            <p class="text-gray-600">True Shallot Seed (Bawang Merah)</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Node</p>
                            <p class="text-gray-600">SPARS Plant Factory, Dramaga</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Cultivation Technique</p>
                            <p class="text-gray-600">Deep Flow Technique (DFT)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/3">
            <div class="glass-widget p-4 sm:p-5">
                <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2">Filter by Date</h3>
                <form id="date-filter-form" class="flex items-center gap-2">
                    <div class="relative flex-grow">
                        <input type="date" id="date-selector" name="date" class="w-full p-2 pl-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" value="{{ date('Y-m-d') }}">
                    </div>
                    <button type="submit" class="btn-gradient font-semibold px-4 py-2 rounded-lg flex items-center justify-center">
                        <i class="fas fa-search mr-1"></i>
                        <span>Submit</span>
                    </button>
                </form>
            </div>
          </div>

    </div>
        <h2 class="text-lg sm:text-xl font-semibold text-gray-700 mb-4 px-2">Environmental Conditions</h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-5 mb-6">
            <div class="glass-widget p-4 flex flex-col justify-between text-left"><div class="w-10 h-10 flex items-center justify-center rounded-lg bg-orange-100"><i class="fas fa-temperature-half text-xl text-orange-500"></i></div><div class="mt-4"><p class="sensor-label">Temperature</p><div class="flex items-baseline gap-1"><p class="sensor-value" id="sensor1">-</p><p class="sensor-unit">°C</p></div></div></div>
            <div class="glass-widget p-4 flex flex-col justify-between text-left"><div class="w-10 h-10 flex items-center justify-center rounded-lg bg-teal-100"><i class="fas fa-tint text-xl text-teal-500"></i></div><div class="mt-4"><p class="sensor-label">Humidity</p><div class="flex items-baseline gap-1"><p class="sensor-value" id="sensor2">-</p><p class="sensor-unit">%</p></div></div></div>
            <div class="glass-widget p-4 flex flex-col justify-between text-left"><div class="w-10 h-10 flex items-center justify-center rounded-lg bg-yellow-100"><i class="fas fa-sun text-xl text-yellow-500"></i></div><div class="mt-4"><p class="sensor-label">Light Intensity</p><div class="flex items-baseline gap-1"><p class="sensor-value" id="sensor3">-</p><p class="sensor-unit">Lx</p></div></div></div>
            <div class="glass-widget p-4 flex flex-col justify-between text-left"><div class="w-10 h-10 flex items-center justify-center rounded-lg bg-indigo-100"><i class="fas fa-water text-xl text-indigo-500"></i></div><div class="mt-4"><p class="sensor-label">TDS / PPM</p><div class="flex items-baseline gap-1"><p class="sensor-value" id="sensor4">-</p><p class="sensor-unit">ppm</p></div></div></div>
            <div class="glass-widget p-4 flex flex-col justify-between text-left"><div class="w-10 h-10 flex items-center justify-center rounded-lg bg-green-100"><i class="fas fa-leaf text-xl text-green-500"></i></div><div class="mt-4"><p class="sensor-label">VPD</p><div class="flex items-baseline gap-1"><p class="sensor-value" id="sensor12">-</p><p class="sensor-unit">kPa</p></div></div></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="glass-widget p-4"><p class="sensor-label font-semibold mb-2">Temperature (°C)</p><div class="chart-container"><canvas id="tempChart"></canvas></div></div>
            <div class="glass-widget p-4"><p class="sensor-label font-semibold mb-2">Humidity (%)</p><div class="chart-container"><canvas id="humidChart"></canvas></div></div>
            <div class="glass-widget p-4 lg:col-span-2"><p class="sensor-label font-semibold mb-2">Light Intensity (Lx)</p><div class="chart-container"><canvas id="lightChart"></canvas></div></div>
    <div class="glass-widget p-4 sm:p-5">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">System Activity</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="electrical-item">
                <div class="flex items-center gap-4"><i class="fas fa-clock fa-fw text-lg w-6 text-center text-blue-500"></i><span class="sensor-label font-semibold">Last Update</span></div>
                <div class="font-semibold text-sm text-right text-gray-500"><span id="api-timestamp">-</span></div>
            </div>
            <div class="electrical-item">
                <div class="flex items-center gap-4"><i class="fas fa-bolt fa-fw text-lg w-6 text-center text-red-500"></i><span class="sensor-label font-semibold">Current</span></div>
                <div class="font-semibold text-md text-right"><span id="sensor6">-</span> <span class="font-medium text-gray-400">A</span></div>
            </div>
            <div class="electrical-item">
                <div class="flex items-center gap-4"><i class="fas fa-plug fa-fw text-lg w-6 text-center text-orange-500"></i><span class="sensor-label font-semibold">Voltage</span></div>
                <div class="font-semibold text-md text-right"><span id="sensor7">-</span> <span class="font-medium text-gray-400">V</span></div>
            </div>
            <div class="electrical-item">
                <div class="flex items-center gap-4"><i class="fas fa-wave-square fa-fw text-lg w-6 text-center text-blue-500"></i><span class="sensor-label font-semibold">Power</span></div>
                <div class="font-semibold text-md text-right"><span id="sensor8">-</span> <span class="font-medium text-gray-400">W</span></div>
            </div>
            <div class="electrical-item">
                <div class="flex items-center gap-4"><i class="fas fa-chart-line fa-fw text-lg w-6 text-center text-indigo-500"></i><span class="sensor-label font-semibold">Power Factor</span></div>
                <div class="font-semibold text-md text-right"><span id="sensor9">-</span></div>
            </div>
            <div class="electrical-item">
                <div class="flex items-center gap-4"><i class="fas fa-tachometer-alt fa-fw text-lg w-6 text-center text-teal-500"></i><span class="sensor-label font-semibold">Frequency</span></div>
                <div class="font-semibold text-md text-right"><span id="sensor10">-</span> <span class="font-medium text-gray-400">Hz</span></div>
            </div>
        </div>
    </div>

    <div class="glass-widget p-4 sm:p-5">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Plant Growth Status</h2>

        <div class="space-y-2">
            <div class="flex justify-between text-sm font-medium text-gray-500">
                <span id="progress-start-date">Day 0</span>
                <span id="progress-hst" class="font-bold text-emerald-600">Day 11</span>
                <span id="progress-end-date">Day 90</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div id="progress-bar" class="bg-emerald-500 h-2.5 rounded-full" style="width: 12%"></div>
            </div>
        </div>

        <div class="mt-6 space-y-3">
            <div class="flex justify-between items-center">
                <p class="text-gray-500">Tanggal Tanam</p>
                <p id="info-seeding-date" class="font-semibold text-gray-800">1 Juli 2025</p>
            </div>
            <div class="flex justify-between items-center">
                <p class="text-gray-500">Fase Pertumbuhan</p>
                <p id="info-current-stage" class="font-semibold text-emerald-600">Fase Semai</p>
            </div>
            <div class="flex justify-between items-center">
                <p class="text-gray-500">Perkiraan Panen</p>
                <p id="info-harvest-date" class="font-semibold text-orange-500">29 September 2025</p>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200/60">
             <button id="toggle-form-btn" class="w-full text-center text-sm text-indigo-600 font-semibold hover:text-indigo-800 transition-colors">
                Atur Tanggal Tanam
            </button>
        </div>
       
        <div id="input-form" class="bg-black/5 p-4 mt-4 rounded-lg hidden">
            <form id="seeding-form" class="space-y-4">
                <div>
                    <label for="seeding-date-input" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal Tanam</label>
                    <input type="date" id="seeding-date-input" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                     <label for="duration-input" class="block text-sm font-medium text-gray-700 mb-1">Estimasi Durasi Tanam (hari)</label>
                    <input type="number" id="duration-input" value="90" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Simpan & Update Status
                </button>
            </form>
        </div>
    </div>
    </div>
    </main>

    <script>
          document.addEventListener('DOMContentLoaded', function () {
        // Ambil semua elemen yang dibutuhkan
        const form = document.getElementById('seeding-form');
        const toggleBtn = document.getElementById('toggle-form-btn');
        const formContainer = document.getElementById('input-form');

        // Fungsi untuk toggle (buka/tutup) form
        toggleBtn.addEventListener('click', () => {
            formContainer.classList.toggle('hidden');
        });
        
        // Fungsi utama untuk kalkulasi dan update UI
        function updatePlantStatus(event) {
            if(event) event.preventDefault(); // Mencegah reload halaman jika dari form submit

            const seedingDateInput = document.getElementById('seeding-date-input').value;
            const duration = parseInt(document.getElementById('duration-input').value) || 90;
            
            // Gunakan tanggal tanam dari input, atau default ke 1 Juli 2025 jika kosong
            const seedingDate = seedingDateInput ? new Date(seedingDateInput) : new Date('2025-07-01T00:00:00');
            
            const today = new Date(); // Tanggal saat ini (12 Juli 2025)

            // 1. Kalkulasi HST (Hari Setelah Tanam)
            const timeDiff = today.getTime() - seedingDate.getTime();
            const hst = Math.max(0, Math.floor(timeDiff / (1000 * 3600 * 24)));

            // 2. Kalkulasi Perkiraan Panen
            const harvestDate = new Date(seedingDate);
            harvestDate.setDate(harvestDate.getDate() + duration);

            // 3. Tentukan Fase Pertumbuhan
            let currentStage = 'Fase Semai';
            if (hst > duration) {
                currentStage = 'Siap Panen';
            } else if (hst > duration * 0.66) {
                currentStage = 'Fase Generatif';
            } else if (hst > duration * 0.15) {
                currentStage = 'Fase Vegetatif';
            }

            // 4. Kalkulasi Progress Bar
            const progressPercentage = Math.min(100, (hst / duration) * 100);

            // Opsi format tanggal (contoh: 12 Juli 2025)
            const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };

            // 5. Update semua elemen di UI
            document.getElementById('progress-hst').textContent = `Day ${hst}`;
            document.getElementById('progress-bar').style.width = `${progressPercentage}%`;
            document.getElementById('progress-end-date').textContent = `Day ${duration}`;
            
            document.getElementById('info-seeding-date').textContent = seedingDate.toLocaleDateString('id-ID', dateOptions);
            document.getElementById('info-current-stage').textContent = currentStage;
            document.getElementById('info-harvest-date').textContent = harvestDate.toLocaleDateString('id-ID', dateOptions);
            
            // Tutup form setelah submit
            if(event) formContainer.classList.add('hidden');
        }

        // Jalankan fungsi saat form disubmit
        form.addEventListener('submit', updatePlantStatus);

        // Jalankan fungsi saat halaman pertama kali dimuat untuk menampilkan data default
        updatePlantStatus();
    });
        // --- FUNGSI DASAR & UI ---
        function updateDateTime() { const now = new Date(); const timeEl = document.getElementById('header-time'); const dateEl = document.getElementById('header-date'); if(timeEl) timeEl.textContent = now.toLocaleTimeString('id-ID', { hour12: false }); if(dateEl) dateEl.textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }); }
        setInterval(updateDateTime, 1000); updateDateTime();
        const statusIndicators = document.querySelectorAll('.status-indicator'); const statusTextElements = document.querySelectorAll('#device-status'); const updateIntervalElements = document.querySelectorAll('#update-interval');
        function updateStatusUI(isOnline, message) { statusIndicators.forEach(dot => { dot.classList.toggle('online', isOnline); dot.classList.toggle('offline', !isOnline); }); statusTextElements.forEach(el => el.textContent = isOnline ? 'Online' : 'Offline'); updateIntervalElements.forEach(el => el.textContent = message); }
        function resetSensorDisplays() { for (let i = 1; i <= 12; i++) { const el = document.getElementById(`sensor${i}`); if (el) el.textContent = '-'; } const timestampEl = document.getElementById('api-timestamp'); if (timestampEl) timestampEl.textContent = '-'; }
        
        // --- LOGIKA GRAFIK ---
        const getChartOptions = (title) => ({ responsive: true, maintainAspectRatio: false, animation: { duration: 0 }, scales: { x: { ticks: { color: '#333' }, grid: { color: 'rgba(100, 100, 100, 0.1)' } }, y: { ticks: { color: '#333' }, grid: { color: 'rgba(100, 100, 100, 0.2)' } } }, plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(0,0,0,0.9)', titleColor: '#ffffff', bodyColor: '#ffffff', intersect: false, mode: 'index' } }, elements: { point: { radius: 2, hoverRadius: 4 }, line: { tension: 0.2 } } });
        function createOptimizedChart(elementId, color) { const ctx = document.getElementById(elementId)?.getContext('2d'); if (!ctx) return null; return new Chart(ctx, { type: 'line', data: { labels: [], datasets: [{ label: elementId.replace('Chart', ''), data: [], borderColor: color, backgroundColor: 'rgba(0, 0, 0, 0)', borderWidth: 2.5, fill: false }] }, options: getChartOptions(elementId.replace('Chart', '')) }); }
        const tempChart = createOptimizedChart('tempChart', '#FF5A36'); const humidChart = createOptimizedChart('humidChart', '#00A5FF'); const lightChart = createOptimizedChart('lightChart', '#FFD700');
        function updateChart(chart, newData) { if (!chart) return; const timeLabel = new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' }); chart.data.labels.push(timeLabel); chart.data.datasets[0].data.push(newData); const maxDataPoints = 30; if (chart.data.labels.length > maxDataPoints) { chart.data.labels.shift(); chart.data.datasets[0].data.shift(); } chart.update('quiet'); }
        
        // --- [FIXED] PARSING & LOGIKA DATA ---
        // Fungsi parser disamakan dengan dataoverview.blade.php agar andal
        function parseTimestamp(tsString) {
            if (!tsString) return null;
            // Langsung konversi format dari database (YYYY-MM-DD HH:MM:SS atau ISO8601)
            const date = new Date(tsString);
            return isNaN(date.getTime()) ? null : date;
        }

        const refresh_time = 5000; // Refresh setiap 5 detik

        async function load_datastream(dateFilter = null) {
            try {
                let apiUrl = "{{ route('api.datastream') }}";
                if (dateFilter) { apiUrl += `?date=${dateFilter}`; }
                const response = await fetch(apiUrl);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();

                // [FIXED] Logika status online/offline disamakan dengan dataoverview.blade.php
                if (data && data.timestamp) {
                    const TIMEOUT_SECONDS = 15; // Toleransi waktu 15 detik
                    const lastUpdate = parseTimestamp(data.timestamp);
                    if (lastUpdate) {
                        const diffSeconds = (new Date().getTime() - lastUpdate.getTime()) / 1000;
                        const isOnline = diffSeconds <= TIMEOUT_SECONDS;
                        const message = isOnline ? `Last update ${Math.round(diffSeconds)}s ago` : 'Device timeout';
                        updateStatusUI(isOnline, message);
                    } else {
                        updateStatusUI(false, 'Invalid timestamp format');
                    }
                } else {
                    updateStatusUI(false, 'No data from API');
                }

                // Update UI
                const apiTimestampEl = document.getElementById('api-timestamp');
                if(apiTimestampEl) apiTimestampEl.textContent = data.timestamp ? new Date(data.timestamp).toLocaleTimeString('id-ID') : '-';
                
                const mapping = { sensor1: data.temp, sensor2: data.humid, sensor3: data.light_intensity, sensor4: data.ppm, sensor6: data.current, sensor7: data.voltage, sensor8: data.power, sensor9: data.pf, sensor10: data.freq, sensor12: data.vpd };
                Object.entries(mapping).forEach(([id, value]) => { const element = document.getElementById(id); if (element) element.textContent = (value !== null && value !== undefined) ? value : '-'; });

                if (!dateFilter) {
                    if (data.temp !== undefined) updateChart(tempChart, data.temp);
                    if (data.humid !== undefined) updateChart(humidChart, data.humid);
                    if (data.light_intensity !== undefined) updateChart(lightChart, data.light_intensity);
                }
            } catch (error) {
                console.error('Error fetching data:', error);
                updateStatusUI(false, 'Connection failed');
                resetSensorDisplays();
            }
        }

        // --- INISIALISASI ---
        document.addEventListener("DOMContentLoaded", () => {
            const today = new Date(); const year = today.getFullYear(); const month = String(today.getMonth() + 1).padStart(2, '0'); const day = String(today.getDate()).padStart(2, '0');
            document.getElementById('date-selector').value = `${year}-${month}-${day}`;
            document.getElementById('date-filter-form').addEventListener('submit', function(e) { e.preventDefault(); const selectedDate = document.getElementById('date-selector').value; console.log("Fitur filter tanggal belum dihubungkan ke backend. Memuat data untuk:", selectedDate); });
            load_datastream(); 
            setInterval(() => load_datastream(), refresh_time);
        });
    </script>
</body>
</html>