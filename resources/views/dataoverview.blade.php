<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>Data Log - TSS Controls</title>
  <meta name="description" content="Data log history for TSS Controls.">
  <link rel="stylesheet" href="{{ asset('assets/tailwind.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
  <style>
    /* ... CSS yang ada sebelumnya ... */
    @font-face {
      font-family: 'Inter';
      src: url("{{ asset('assets/inter.ttf') }}") format('truetype');
      font-weight: 100 900;
      font-style: normal;
    }

    :root {
      --ios-blue: #007AFF;
      --ios-green: #34C759;
      --ios-red: #FF3B30;
      --text-primary: #1c1c1e;
      --text-secondary: #8e8e93;
      --glass-background: rgba(255, 255, 255, 0.75);
      --glass-border-color: rgba(229, 229, 234, 0.8);
      --glass-blur: 20px;
      --glass-saturation: 180%;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      color: var(--text-primary);
      min-height: 100vh;
      padding-bottom: 2rem;
      background: linear-gradient(135deg, #c8e0c8, #e8d8b5, #a8c8d8, #d8c0a0);
      background-size: 400% 400%;
      animation: gradient-animation 10s ease infinite;
    }

    .glass-widget {
      background: var(--glass-background);
      backdrop-filter: blur(var(--glass-blur)) saturate(var(--glass-saturation));
      -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(var(--glass-saturation));
      border-radius: 1.25rem;
      border: 1px solid var(--glass-border-color);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.07);
    }

    .action-btn {
      transition: all 0.2s ease-in-out;
      border: 2px solid transparent;
      background-color: var(--ios-blue);
      color: white;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
    }

    .action-btn:hover {
      box-shadow: 0 4px 15px rgba(0, 122, 255, 0.2);
    }

    .tab-nav {
      background-color: rgba(120, 120, 128, 0.12);
      padding: 0.5rem;
      border-radius: 0.75rem;
      display: flex;
      justify-content: space-between;
    }

    .tab-link {
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      font-weight: 600;
      font-size: 0.875rem;
      color: var(--text-secondary);
      transition: all 0.3s ease;
      cursor: pointer;
      white-space: nowrap;
      flex: 1;
      text-align: center;
      text-decoration: none;
    }

    .tab-link.active {
      background-color: var(--glass-background);
      color: var(--text-primary);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .data-table {
      width: 100%;
      border-collapse: collapse;
    }

    .data-table thead th {
      padding: 0.75rem 1rem;
      background-color: rgba(120, 120, 128, 0.08);
      font-weight: 600;
      text-align: left;
      font-size: 0.875rem;
      color: var(--text-primary);
      text-transform: capitalize;
    }

    .data-table tbody tr {
      border-bottom: 1px solid var(--glass-border-color);
    }

    .data-table tbody tr:last-child {
      border-bottom: none;
    }

    .data-table tbody td {
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .data-table tbody td:first-child {
      font-weight: 600;
      color: var(--text-primary);
    }

    @keyframes gradient-animation {
      0% {
        background-position: 0% 50%;
      }

      50% {
        background-position: 100% 50%;
      }

      100% {
        background-position: 0% 50%;
      }
    }

    .status-indicator.online {
      background-color: var(--ios-green);
      animation: pulse 2s infinite;
    }

    .status-indicator.offline {
      background-color: var(--ios-red);
      animation: none;
    }

    @keyframes pulse {

      0%,
      100% {
        box-shadow: 0 0 0 0 rgba(52, 199, 89, 0.4);
      }

      50% {
        box-shadow: 0 0 0 6px rgba(52, 199, 89, 0);
      }
    }
  </style>
</head>

<body class="max-w-screen-lg mx-auto px-3 sm:px-4 md:p-6 pb-8 pt-6">

  <header class="flex justify-between items-start mb-6 sm:mb-8 sm:items-center">
    <div class="flex items-center gap-3 sm:gap-4">
      <div class="w-11 h-11 sm:w-12 sm:h-12 flex items-center justify-center rounded-xl" style="background-color: rgba(0, 122, 255, 0.1);"><i class="fas fa-solar-panel text-xl sm:text-2xl" style="color: var(--ios-blue);"></i></div>
      <div>
        <h1 class="font-bold text-lg sm:text-2xl text-gray-800">TSS Controls</h1>
        <div class="flex items-center gap-2 mt-2 text-xs sm:hidden">
          <div id="dots-mobile" class="status-indicator w-2 h-2 rounded-full"></div><span id="device-status-mobile" class="font-semibold text-gray-700"></span><span class="text-gray-500">·</span><span id="update-interval-mobile" class="text-gray-500"></span>
        </div>
      </div>
    </div>
    <div class="text-right hidden sm:block">
      <p id="header-time" class="font-semibold text-xl"></p>
      <p id="header-date" class="text-sm text-gray-500"></p>
      <div class="flex items-center justify-end gap-2 mt-1 text-xs">
        <div id="dots" class="status-indicator w-2 h-2 rounded-full"></div><span id="device-status" class="font-semibold text-gray-700"></span><span class="text-gray-500">·</span><span id="update-interval" class="text-gray-500"></span>
      </div>
    </div>
  </header>
  <nav class="mb-6">
    <div class="tab-nav">
        <a href="{{ route('dashboard') }}" class="tab-link">Dashboard</a>
        <a href="{{ route('controls') }}" class="tab-link">Controls</a>
        <a href="{{ route('dataoverview') }}" class="tab-link active">Data Log</a>
    </div>
  </nav>

  <main>
    <div class="glass-widget p-4 sm:p-5">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
        <div>
          <h2 class="text-lg sm:text-xl font-semibold text-gray-700">Data Log History</h2>
          <p class="text-sm text-gray-500 mt-1">Showing latest sensor readings from the device.</p>
        </div>
        <div class="flex items-center gap-4 mt-3 sm:mt-0">
          <div class="flex items-center gap-2"><label for="limit-select" class="text-sm font-medium text-gray-600">Show:</label><select id="limit-select" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="10">10</option>
              <option value="25" selected>25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select></div>
          <a href="{{ route('dataoverview.export') }}" id="export-csv-btn" class="action-btn py-2 px-4 rounded-lg font-medium text-sm flex items-center gap-2"><i class="fas fa-file-csv"></i>Export All to CSV</a>
        </div>
      </div>
      <div class="overflow-x-auto rounded-lg" style="background-color: rgba(120, 120, 128, 0.05);">
        <table class="data-table" id="sensor-data-table">
          <thead id="data-table-head"></thead>
          <tbody id="data-table-body"></tbody>
          <tfoot id="table-status-footer">
            <tr>
              <td colspan="100%" class="text-center py-8"><i class="fas fa-spinner fa-spin mr-2"></i> Initializing...</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </main>

  <script>
    function updateDateTime() {
      const now = new Date();
      const timeEl = document.getElementById('header-time');
      const dateEl = document.getElementById('header-date');
      if (timeEl) timeEl.textContent = now.toLocaleTimeString('id-ID', {
        hour12: false
      });
      if (dateEl) dateEl.textContent = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    const tableHead = document.getElementById('data-table-head');
    const tableBody = document.getElementById('data-table-body');
    const tableStatusFooter = document.getElementById('table-status-footer');
    const limitSelect = document.getElementById('limit-select');
    const statusIndicators = document.querySelectorAll('.status-indicator');
    const statusTextElements = document.querySelectorAll('#device-status, #device-status-mobile');
    const updateIntervalElements = document.querySelectorAll('#update-interval, #update-interval-mobile');
    const REFRESH_INTERVAL = 5000;
    let autoRefreshInterval;

    function updateStatusUI(isOnline, message) {
      statusIndicators.forEach(dot => {
        dot.classList.toggle('online', isOnline);
        dot.classList.toggle('offline', !isOnline);
      });
      statusTextElements.forEach(el => el.textContent = isOnline ? 'Online' : 'Offline');
      updateIntervalElements.forEach(el => el.textContent = message);
    }

    // BARU: Fungsi parser timestamp yang robust, disamakan dengan dashboard.php
    function parseTimestamp(tsString) {
      if (!tsString) return null;
      // Mencoba handle format "23 Jun 2025 - 14:35:18 WIB" dan "2025-06-23 15:10:00"
      const cleanString = tsString.replace(' - ', ' ').replace(' WIB', '');
      const date = new Date(cleanString);
      return isNaN(date.getTime()) ? null : date;
    }

    async function populateTable() {
      const limit = limitSelect.value;
      showLoadingState();
      try {
        const response = await fetch(`{{ route('dataoverview.fetch') }}?limit=${limit}`);
        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        updateTableUI(data);

        // MODIFIKASI: Menggunakan logika timeout yang sama dengan dashboard
        if (data.length > 0 && data[0].timestamp) {
          const TIMEOUT_SECONDS = 10;
          // Gunakan parser baru yang lebih robust
          const lastUpdate = parseTimestamp(data[0].timestamp);

          if (lastUpdate) {
            const now = new Date();
            const diffSeconds = (now.getTime() - lastUpdate.getTime()) / 1000;

            if (diffSeconds > TIMEOUT_SECONDS) {
              updateStatusUI(false, 'Device timeout');
            } else {
              updateStatusUI(true, `Last update ${Math.round(diffSeconds)}s ago`);
            }
          } else {
            // Jika parsing gagal, beri tahu di status
            updateStatusUI(false, 'Invalid timestamp');
            console.error("Invalid timestamp format received:", data[0].timestamp);
          }
        } else if (data.length > 0) {
          updateStatusUI(true, 'Timestamp missing');
          console.warn("Timestamp field is missing from the newest data row.");
        }
      } catch (error) {
        console.error("Fetch error:", error);
        let errorMessage = error.message;
        if (error instanceof SyntaxError) {
          errorMessage = "Received an invalid response from server.";
        }
        updateTableUI([], `Error: ${errorMessage}`);
        updateStatusUI(false, 'Connection failed');
      }
    }

    function updateTableUI(data, errorMessage = '') {
      tableHead.innerHTML = '';
      tableBody.innerHTML = '';
      if (errorMessage) {
        showFooterMessage(`<i class="fas fa-exclamation-triangle mr-2 text-red-500"></i> ${errorMessage}`);
        return;
      }
      if (!data || data.length === 0) {
        showFooterMessage('No data available from the device.');
        updateStatusUI(false, 'No data');
        return;
      }
      hideFooter();
      const headers = Object.keys(data[0]);
      const headerRow = document.createElement('tr');
      headerRow.innerHTML = headers.map(key => `<th>${key.replace(/_/g, ' ')}</th>`).join('');
      tableHead.appendChild(headerRow);
      data.forEach(record => {
        const row = document.createElement('tr');
        row.innerHTML = headers.map(header => `<td>${formatCell(record[header], header)}</td>`).join('');
        tableBody.appendChild(row);
      });
    }

    // MODIFIKASI: Menggunakan parser baru di dalam formatCell
    function formatCell(value, key) {
      if (value === null) return '';
      if (key === 'timestamp' || key.includes('_at')) {
        const date = parseTimestamp(value); // Gunakan parser baru
        return !date ? value : date.toLocaleString('id-ID', {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit'
        });
      }
      if (typeof value === 'string' && !isNaN(parseFloat(value)) && value.includes('.')) {
        return parseFloat(value).toFixed(2);
      }
      if (typeof value === 'number' && !Number.isInteger(value)) {
        return value.toFixed(2);
      }
      return value;
    }

    function showLoadingState() {
      showFooterMessage('<i class="fas fa-spinner fa-spin mr-2"></i> Loading new data...');
    }

    function hideFooter() {
      tableStatusFooter.classList.add('hidden');
    }

    function showFooterMessage(htmlContent) {
      tableStatusFooter.classList.remove('hidden');
      const cell = tableStatusFooter.querySelector('td');
      if (cell) {
        cell.innerHTML = htmlContent;
        const colSpan = tableHead.querySelector('tr')?.cells.length || 12;
        cell.setAttribute('colspan', colSpan);
      }
    }

    function startAutoRefresh() {
      clearInterval(autoRefreshInterval);
      autoRefreshInterval = setInterval(populateTable, REFRESH_INTERVAL);
    }
    document.addEventListener('DOMContentLoaded', () => {
      populateTable();
      startAutoRefresh();
    });
    limitSelect.addEventListener('change', () => {
      populateTable();
      startAutoRefresh();
    });
  </script>
</body>

</html>