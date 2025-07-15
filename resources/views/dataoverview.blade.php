<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Overview - ShalMonic</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    @vite('resources/js/app.js')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        body{background:linear-gradient(135deg,#f8fafc,#e0e7ff,#c7f9cc,#fceabb);background-size:400% 400%;animation:gradient-animation 10s ease infinite;font-family:'Figtree',sans-serif}.glass-widget{background:rgba(255,255,255,.75);backdrop-filter:blur(16px) saturate(180%);-webkit-backdrop-filter:blur(16px) saturate(180%);border-radius:1.5rem;border:1px solid rgba(229,229,234,.8);box-shadow:0 8px 32px rgba(0,0,0,.07)}.status-indicator.online{background-color:#22c55e;animation:pulse 2s infinite}.status-indicator.offline{background-color:#ef4444}.data-table{width:100%;border-collapse:collapse}.data-table thead th{padding:.75rem 1rem;background-color:rgba(120,120,128,.08);font-weight:600;text-align:left;font-size:.875rem;color:#1c1c1e;text-transform:capitalize}.data-table tbody tr{border-bottom:1px solid rgba(229,229,234,.8)}.data-table tbody tr:last-child{border-bottom:none}.data-table tbody td{padding:.75rem 1rem;font-size:.875rem;color:#64748b;font-weight:500}.data-table tbody td:first-child{font-weight:600;color:#1c1c1e}@keyframes gradient-animation{0%{background-position:0 50%}50%{background-position:100% 50%}100%{background-position:0 50%}}@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,.7)}50%{box-shadow:0 0 0 6px rgba(34,197,94,0)}}
        .view-btn.active { background-color: #0d9488; color: white; }
        .view-btn { background-color: #e5e7eb; color: #374151; }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center"><a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-75 transition-opacity"><svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/><path d="M12 2L3 7L12 12L21 7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/><path d="M12 22V12" stroke="teal" stroke-width="2" stroke-linejoin="round"/></svg><span class="text-2xl font-bold">ShalMonic</span></a></div>
                <div class="hidden sm:ml-1 sm:flex sm:items-center">
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 text-gray-500 hover:text-gray-900">Dashboard</a>
                    <a href="{{ route('controls') }}" class="px-5 py-2 text-gray-500 hover:text-gray-900">Controls</a>
                    <a href="{{ route('dataoverview') }}" class="px-5 py-2 text-teal-600 border-b-2 border-teal-600">Data Overview</a>
                </div>
                <div class="flex items-center"><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full text-sm bg-teal-600 text-white rounded-full hover:bg-teal-700 px-4 py-2">Logout</button></form></div>
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full max-w-7xl mx-auto py-6 px-4 sm:px-8 lg:px-16">
        <div class="flex justify-between items-start sm:items-center mb-6">
            <div><h2 class="text-teal-800 text-3xl sm:text-4xl font-bold mt-1 mb-2">Data Overview</h2><p class="text-gray-500">View historical sensor readings from the device.</p></div>
            <div class="text-right flex-shrink-0"><div id="header-time" class="text-xl font-bold text-gray-700"></div><div id="header-date" class="text-gray-500"></div><div class="flex items-center justify-end gap-2 mt-2 text-xs"><div class="status-indicator w-2 h-2 rounded-full"></div><span id="device-status" class="font-semibold text-gray-700"></span><span class="text-gray-400">·</span><span id="update-interval" class="text-gray-500"></span></div></div>
        </div>

        <div class="glass-widget p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                <div><h2 class="text-lg sm:text-xl font-semibold text-gray-700">Data Log History</h2><p class="text-sm text-gray-500 mt-1">Filter and view data in graph or table format.</p></div>
                <div class="flex items-center gap-4 mt-3 sm:mt-0">
                    <div class="flex items-center gap-2">
                        <label for="daterange" class="text-sm font-medium text-gray-600">Date Range:</label>
                        <input type="text" id="daterange" class="rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500" />
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="limit-select" class="text-sm font-medium text-gray-600">Show:</label>
                        <select id="limit-select" class="rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500 py-2">
                            <option>25</option><option>50</option><option>100</option>
                        </select>
                    </div>
                    <div class="flex items-center p-1 bg-gray-200 rounded-lg">
                        <button id="view-graph-btn" class="view-btn active px-3 py-1 rounded-md text-sm font-semibold transition-colors">Graph</button>
                        <button id="view-table-btn" class="view-btn px-3 py-1 rounded-md text-sm font-semibold transition-colors">Table</button>
                    </div>
                    <a href="#" id="export-csv-btn" class="px-4 py-2 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 transition-colors text-sm flex items-center gap-2"><i class="fas fa-file-csv"></i>Export</a>
                </div>
            </div>

            <div id="graph-view">
                <div class="h-[450px] w-full relative">
                    <div id="chart-loader" class="absolute inset-0 bg-white/70 items-center justify-center z-10 hidden"><i class="fas fa-spinner fa-spin text-3xl text-teal-600"></i></div>
                    <canvas id="data-chart"></canvas>
                </div>
            </div>
            <div id="table-view" class="hidden">
                <div class="overflow-x-auto rounded-lg" style="background-color: rgba(249,250,251, 0.8);">
                    <table class="data-table" id="sensor-data-table">
                        <thead id="data-table-head"></thead>
                        <tbody id="data-table-body"></tbody>
                    </table>
                </div>
                <div class="flex justify-between items-center mt-4" id="pagination-controls">
                    <div id="pagination-info" class="text-sm text-gray-600"></div>
                    <div id="pagination-buttons" class="flex gap-2"></div>
                </div>
                 <div id="table-status-footer" class="text-center py-8"><i class="fas fa-spinner fa-spin mr-2"></i> Initializing...</div>
            </div>
        </div>
    </main>
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.1/dist/chartjs-adapter-moment.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    // --------------------------------------------------------------------
    // BAGIAN 1: UI & ELEMEN (Tidak banyak berubah)
    // --------------------------------------------------------------------
    const ui = {
        tableHead: document.getElementById('data-table-head'),
        tableBody: document.getElementById('data-table-body'),
        tableStatusFooter: document.getElementById('table-status-footer'),
        paginationControls: document.getElementById('pagination-controls'),
        paginationInfo: document.getElementById('pagination-info'),
        paginationButtons: document.getElementById('pagination-buttons'),
        limitSelect: document.getElementById('limit-select'),
        statusIndicators: document.querySelectorAll('.status-indicator'), // Untuk status dot
        statusText: document.getElementById('device-status'), // Untuk teks Online/Offline
        updateInterval: document.getElementById('update-interval'), // Untuk teks "last update..."
        viewTableBtn: document.getElementById('view-table-btn'),
        viewGraphBtn: document.getElementById('view-graph-btn'),
        tableView: document.getElementById('table-view'),
        graphView: document.getElementById('graph-view'),
        dateRangePicker: $('#daterange'),
        exportBtn: document.getElementById('export-csv-btn'),
        chartLoader: document.getElementById('chart-loader'),
    };
    let dataChart = null;
    let fetchedData = [];
    let currentPage = 1;

    // --------------------------------------------------------------------
    // BAGIAN 2: FUNGSI STATUS REAL-TIME (Dikutip dari dashboard.blade.php)
    // --------------------------------------------------------------------

    // [FIX] Fungsi update UI Status yang disempurnakan dari dashboard
    function updateStatusUI(isOnline, message) {
        ui.statusIndicators.forEach(dot => {
            dot.classList.toggle('online', isOnline);
            dot.classList.toggle('offline', !isOnline);
        });
        if (ui.statusText) ui.statusText.textContent = isOnline ? 'Online' : 'Offline';
        if (ui.updateInterval) ui.updateInterval.textContent = message;
    }

    // [FIX] Fungsi parser timestamp yang andal dari dashboard
    function parseTimestamp(tsString) {
        if (!tsString) return null;
        const date = new Date(tsString);
        return isNaN(date.getTime()) ? null : date;
    }

    // [BARU] Fungsi untuk mengambil data terbaru & update status (seperti di dashboard)
    async function updateLiveStatus() {
        try {
            const response = await fetch("{{ route('api.datastream') }}");
            if (!response.ok) throw new Error(`API error! status: ${response.status}`);
            
            const data = await response.json();

            if (data && data.timestamp) {
                const TIMEOUT_SECONDS = 15; // Samakan timeout dengan dashboard
                const lastUpdate = parseTimestamp(data.timestamp);
                if (lastUpdate) {
                    const diffSeconds = (new Date().getTime() - lastUpdate.getTime()) / 1000;
                    const isOnline = diffSeconds <= TIMEOUT_SECONDS;
                    const message = isOnline ? `Last update ${Math.round(diffSeconds)}s ago` : 'Device timeout';
                    updateStatusUI(isOnline, message);
                } else {
                    updateStatusUI(false, 'Invalid timestamp');
                }
            } else {
                updateStatusUI(false, 'No data');
            }
        } catch (error) {
            console.error('Live status fetch error:', error);
            updateStatusUI(false, 'Connection failed');
        }
    }


    // --------------------------------------------------------------------
    // BAGIAN 3: FUNGSI UNTUK GRAFIK & TABEL HISTORIS (Kode lama yang dimodifikasi)
    // --------------------------------------------------------------------
    
    // Fungsi tanggal & waktu untuk header
    function updateDateTime() {
        const now = new Date();
        const timeEl = document.getElementById('header-time');
        const dateEl = document.getElementById('header-date');
        if (timeEl) timeEl.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
        if (dateEl) dateEl.textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }

    // Fungsi utama untuk mengambil data historis (HANYA untuk chart & tabel)
    async function fetchDataAndRender() {
        ui.chartLoader.style.display = 'flex';
        const dateRange = ui.dateRangePicker.data('daterangepicker');
        if (!dateRange) { 
            ui.chartLoader.style.display = 'none';
            return; 
        }
        
        const startDate = dateRange.startDate.format('YYYY-MM-DD');
        const endDate = dateRange.endDate.format('YYYY-MM-DD');
        
        try {
            const response = await fetch(`{{ route('dataoverview.fetch') }}?start_date=${startDate}&end_date=${endDate}`);
            if (!response.ok) { throw new Error(`HTTP error! status: ${response.status}`); }
            
            fetchedData = await response.json();
            if (!Array.isArray(fetchedData)) { throw new Error('Invalid data format received'); }
            
            currentPage = 1;
            updateTableUI();
            updateChartUI();
            
            // [DIHAPUS] Panggilan ke updateDeviceStatus(fetchedData) dihapus dari sini
            // karena status sekarang ditangani oleh updateLiveStatus() secara terpisah.

        } catch (error) {
            console.error("Fetch error:", error);
            updateTableUI(true, `Error: ${error.message}`);
        } finally {
            ui.chartLoader.style.display = 'none';
        }
    }

    // --- (Sisanya adalah fungsi-fungsi pembantu untuk tabel, chart, dll. yang tidak perlu diubah) ---
    // Fungsi-fungsi seperti initializeChart, updateChartUI, updateTableUI, renderPaginationControls, dll.
    // bisa dibiarkan seperti sebelumnya. Kita hanya perlu memastikan pemanggilan `updateDeviceStatus` dihapus.

    function formatCell(value, key) { if (value === null || value === undefined) return ''; if (key.toLowerCase() === 'timestamp') { const date = parseTimestamp(value); return !date ? value : date.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g,':'); } if (typeof value === 'number' && !Number.isInteger(value)) { return value.toFixed(2); } return value;}
    function updateTableUI(isError = false, errorMessage = '') { ui.tableHead.innerHTML = ''; ui.tableBody.innerHTML = ''; if (isError) { ui.tableStatusFooter.innerHTML = `<i class="fas fa-exclamation-triangle text-red-500"></i> ${errorMessage}`; ui.paginationControls.classList.add('hidden'); return; } if (fetchedData.length === 0) { ui.tableStatusFooter.innerHTML = 'No data available for the selected range.'; ui.paginationControls.classList.add('hidden'); return; } ui.tableStatusFooter.classList.add('hidden'); ui.paginationControls.classList.remove('hidden'); const itemsPerPage = parseInt(ui.limitSelect.value, 10); const paginatedData = fetchedData.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage); if (paginatedData.length === 0) { ui.tableStatusFooter.innerHTML = 'No data to display on this page.'; renderPaginationControls(); return; } const headers = Object.keys(paginatedData[0]); const headerRow = document.createElement('tr'); headerRow.innerHTML = headers.map(key => `<th>${key.replace(/_/g, ' ')}</th>`).join(''); ui.tableHead.appendChild(headerRow); paginatedData.forEach(record => { const row = document.createElement('tr'); row.innerHTML = headers.map(header => `<td>${formatCell(record[header], header)}</td>`).join(''); ui.tableBody.appendChild(row); }); renderPaginationControls(); }
    function renderPaginationControls() { const totalItems = fetchedData.length; if (totalItems === 0) { ui.paginationControls.classList.add('hidden'); return; } const itemsPerPage = parseInt(ui.limitSelect.value, 10); const totalPages = Math.ceil(totalItems / itemsPerPage) || 1; const startItem = (currentPage - 1) * itemsPerPage + 1; const endItem = Math.min(currentPage * itemsPerPage, totalItems); ui.paginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${totalItems} entries`; ui.paginationButtons.innerHTML = `<button id="prev-page" class="px-3 py-1 border rounded-md hover:bg-gray-50 disabled:opacity-50" ${currentPage === 1 ? 'disabled' : ''}>Prev</button><span class="px-3 py-1 text-sm">Page ${currentPage} of ${totalPages}</span><button id="next-page" class="px-3 py-1 border rounded-md hover:bg-gray-50 disabled:opacity-50" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>`; }
    function averageDataPerMinute(rawData) { if (!rawData || rawData.length === 0) return []; const groupedByMinute = new Map(); rawData.forEach(d => { const ts = parseTimestamp(d.timestamp); if (!ts) return; ts.setSeconds(0, 0); const minuteKey = ts.toISOString(); if (!groupedByMinute.has(minuteKey)) { groupedByMinute.set(minuteKey, { temp: [], humid: [], light_intensity: [] }); } const group = groupedByMinute.get(minuteKey); if (typeof d.temp === 'number') group.temp.push(d.temp); if (typeof d.humid === 'number') group.humid.push(d.humid); if (typeof d.light_intensity === 'number') group.light_intensity.push(d.light_intensity); }); const averagedData = []; for (const [key, group] of groupedByMinute.entries()) { const sum = (arr) => arr.reduce((a, b) => a + b, 0); const avg = (arr) => arr.length > 0 ? sum(arr) / arr.length : null; averagedData.push({ timestamp: key, temp: avg(group.temp), humid: avg(group.humid), light_intensity: avg(group.light_intensity) }); } return averagedData; }
    function initializeChart() { const ctx = document.getElementById('data-chart').getContext('2d'); if (dataChart) dataChart.destroy(); dataChart = new Chart(ctx, { type: 'line', data: { labels: [], datasets: [ { label: 'Temp', yAxisID: 'yTemp', data: [], borderColor: '#ef4444', backgroundColor: '#ef444420', tension: 0.2, fill: true, borderWidth: 2 }, { label: 'Humid', yAxisID: 'yHumid', data: [], borderColor: '#3b82f6', backgroundColor: '#3b82f620', tension: 0.2, fill: true, borderWidth: 2 }, { label: 'Light', yAxisID: 'yLight', data: [], borderColor: '#f59e0b', backgroundColor: '#f59e0b20', tension: 0.2, fill: true, borderWidth: 2 } ]}, options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, padding: 20 } } }, scales: { x: { type: 'time', time: { unit: 'hour', tooltipFormat: 'HH:mm' }, title: { display: true, text: 'Time' } }, yTemp:  { type: 'linear', position: 'left', title: { display: true, text: 'Temp (°C)', color: '#ef4444' } }, yHumid: { type: 'linear', position: 'left', title: { display: true, text: 'Humid (%)', color: '#3b82f6' }, grid: { drawOnChartArea: false } }, yLight: { type: 'linear', position: 'left', title: { display: true, text: 'Light (Lux)', color: '#f59e0b' }, grid: { drawOnChartArea: false }, suggestedMin: 0 } } } }); }
    function updateChartUI(data = fetchedData) { if (!dataChart) return; const averagedData = averageDataPerMinute(data); if (averagedData.length === 0) { dataChart.data.labels = []; dataChart.data.datasets.forEach(ds => ds.data = []); dataChart.update('none'); return; } const sortedData = averagedData.sort((a, b) => parseTimestamp(a.timestamp) - parseTimestamp(b.timestamp)); dataChart.data.labels = sortedData.map(d => parseTimestamp(d.timestamp)); dataChart.data.datasets[0].data = sortedData.map(d => d.temp); dataChart.data.datasets[1].data = sortedData.map(d => d.humid); dataChart.data.datasets[2].data = sortedData.map(d => d.light_intensity); dataChart.update('none'); }
    function setView(view) { const isGraph = view === 'graph'; ui.graphView.classList.toggle('hidden', !isGraph); ui.tableView.classList.toggle('hidden', isGraph); ui.viewGraphBtn.classList.toggle('active', isGraph); ui.viewTableBtn.classList.toggle('active', !isGraph); if(isGraph && dataChart) dataChart.resize(); }

    // --------------------------------------------------------------------
    // BAGIAN 4: INISIALISASI & EVENT LISTENERS
    // --------------------------------------------------------------------
    
    // Event listeners untuk UI
    ui.viewGraphBtn.addEventListener('click', () => setView('graph'));
    ui.viewTableBtn.addEventListener('click', () => setView('table'));
    ui.limitSelect.addEventListener('change', () => { currentPage = 1; updateTableUI(); });
    ui.paginationButtons.addEventListener('click', (event) => { const totalPages = Math.ceil(fetchedData.length / parseInt(ui.limitSelect.value, 10)); if (event.target.id === 'prev-page' && currentPage > 1) { currentPage--; updateTableUI(); } if (event.target.id === 'next-page' && currentPage < totalPages) { currentPage++; updateTableUI(); } });
    ui.exportBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (fetchedData.length === 0) {
            alert('No data to export.');
            return;
        }
        
        const headers = Object.keys(fetchedData[0]);

        // [FIX] Ubah delimiter dari koma (,) menjadi semicolon (;) untuk header
        let csvContent = "data:text/csv;charset=utf-8," + headers.map(h => h.replace(/_/g, ' ')).join(';') + '\n';

        csvContent += fetchedData.map(row => {
            return headers.map(header => {
                // Ambil nilai sel, pastikan tidak null/undefined
                let cellValue = row[header] === null || row[header] === undefined ? '' : String(row[header]);

                // Jika sel adalah timestamp, format dengan benar
                if (header.toLowerCase() === 'timestamp') {
                    cellValue = new Date(cellValue).toLocaleString('id-ID', {
                        year: 'numeric', month: '2-digit', day: '2-digit',
                        hour: '2-digit', minute: '2-digit', second: '2-digit'
                    }).replace(/\./g,':');
                } else {
                    // [PENTING] Ganti titik desimal menjadi koma untuk angka
                    cellValue = cellValue.replace(/\./g, ',');
                }
                
                // Bungkus semua sel dengan tanda kutip untuk keamanan
                return `"${cellValue.replace(/"/g, '""')}"`;
            }).join(';'); // [FIX] Ubah delimiter dari koma (,) menjadi semicolon (;) untuk baris data
        }).join('\n');

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        // Beri nama file yang lebih deskriptif
        link.setAttribute("download", `shalmonic_data_export_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
    
    // Inisialisasi Date Range Picker
    ui.dateRangePicker.daterangepicker({
        startDate: moment(), endDate: moment(),
        ranges: { 'Today': [moment(), moment()], 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')], 'Last 7 Days': [moment().subtract(6, 'days'), moment()], 'Last 30 Days': [moment().subtract(29, 'days'), moment()], 'This Month': [moment().startOf('month'), moment().endOf('month')], 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')] }
    });
    ui.dateRangePicker.on('apply.daterangepicker', fetchDataAndRender);

    // INISIALISASI HALAMAN
    initializeChart();
    setView('graph');
    fetchDataAndRender(); // 1. Muat data historis untuk chart/tabel
    updateDateTime();     // 2. Tampilkan tanggal & waktu
    setInterval(updateDateTime, 1000); // 3. Update waktu setiap detik
    
    updateLiveStatus();   // 4. Panggil status real-time sekali saat muat
    setInterval(updateLiveStatus, 5000); // 5. [FIX] Update status real-time setiap 5 detik
});
</script>
</body>
</html>