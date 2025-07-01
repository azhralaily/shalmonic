<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>TSS Controls</title>
  <meta name="description" content="Modern, professional IoT Gateway Dashboard inspired by iOS.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('assets/tailwind.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* ... CSS Anda tetap sama seperti sebelumnya, tidak ada perubahan ... */
    @font-face {
      font-family: 'Inter';
      src: url('{{ asset('assets/inter.ttf') }}') format('truetype');
      font-weight: 100 900;
      font-style: normal;
    }

    :root {
      --ios-blue: #007AFF;
      --ios-green: #34C759;
      --ios-red: #FF3B30;
      --ios-yellow: #FFCC00;
      --ios-orange: #FF9500;
      --ios-purple: #AF52DE;
      --ios-teal: #5AC8FA;
      --ios-indigo: #5856D6;
      --text-primary: #1c1c1e;
      --text-secondary: #8e8e93;
      --text-tertiary: #c7c7cc;
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
      transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .sensor-label {
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--text-secondary);
    }

    .sensor-unit {
      font-size: 1rem;
      font-weight: 500;
      color: var(--text-secondary);
    }

    .sensor-value {
      font-size: 1.75rem;
      line-height: 1.1;
      font-weight: 700;
      color: var(--text-primary);
    }

    @media (min-width: 640px) {
      .sensor-value {
        font-size: 2.1rem;
      }
    }

    #battery-bar {
      transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1), background 0.5s ease;
      background: var(--ios-green);
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
        box-shadow: 0 0 0 0 rgba(52, 199, 89, 0.7);
      }

      50% {
        box-shadow: 0 0 0 6px rgba(52, 199, 89, 0);
      }
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

    .electrical-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.75rem;
      background-color: rgba(120, 120, 128, 0.08);
      border-radius: 0.75rem;
    }

    .tab-nav {
      background-color: rgba(120, 120, 128, 0.12);
      padding: 0.5rem;
      border-radius: 0.75rem;
      display: flex;
      justify-content: space-between;
      max-width: 100%;
      overflow-x: auto;
      scrollbar-width: none;
    }

    .tab-nav::-webkit-scrollbar {
      display: none;
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
      margin: 0 0.1rem;
    }

    .tab-link.active {
      background-color: var(--glass-background);
      color: var(--text-primary);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .chart-container {
      position: relative;
      height: 250px;
      width: 100%;
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
        <a href="{{ route('dashboard') }}" class="tab-link active">Dashboard</a>
        <a href="{{ route('controls') }}" class="tab-link">Controls</a>
        <a href="{{ route('dataoverview') }}" class="tab-link">Data Log</a>
    </div>
  </nav>

  <main class="space-y-6">
    <section class="tab-content" id="dashboard-content">
      <h2 class="text-lg sm:text-xl font-semibold text-gray-700 mb-4 px-2">Sensor Readings</h2>
      <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-5">
        <div class="glass-widget p-4 flex flex-col justify-between text-left">
          <div class="w-10 h-10 flex items-center justify-center rounded-lg" style="background-color: rgba(255, 149, 0, 0.15);"><i class="fas fa-temperature-half text-xl" style="color: var(--ios-orange);"></i></div>
          <div class="mt-4">
            <p class="sensor-label">Temperature</p>
            <div class="flex items-baseline gap-1">
              <p class="sensor-value" id="sensor1">-</p>
              <p class="sensor-unit">°C</p>
            </div>
          </div>
        </div>
        <div class="glass-widget p-4 flex flex-col justify-between text-left">
          <div class="w-10 h-10 flex items-center justify-center rounded-lg" style="background-color: rgba(90, 200, 250, 0.15);"><i class="fas fa-tint text-xl" style="color: var(--ios-teal);"></i></div>
          <div class="mt-4">
            <p class="sensor-label">Humidity</p>
            <div class="flex items-baseline gap-1">
              <p class="sensor-value" id="sensor2">-</p>
              <p class="sensor-unit">%</p>
            </div>
          </div>
        </div>
        <div class="glass-widget p-4 flex flex-col justify-between text-left">
          <div class="w-10 h-10 flex items-center justify-center rounded-lg" style="background-color: rgba(255, 204, 0, 0.15);"><i class="fas fa-sun text-xl" style="color: var(--ios-yellow);"></i></div>
          <div class="mt-4">
            <p class="sensor-label">Light Intensity</p>
            <div class="flex items-baseline gap-1">
              <p class="sensor-value" id="sensor3">-</p>
              <p class="sensor-unit">Lx</p>
            </div>
          </div>
        </div>
        <div class="glass-widget p-4 flex flex-col justify-between text-left">
          <div class="w-10 h-10 flex items-center justify-center rounded-lg" style="background-color: rgba(88, 86, 214, 0.15);"><i class="fas fa-water text-xl" style="color: var(--ios-indigo);"></i></div>
          <div class="mt-4">
            <p class="sensor-label">TDS / PPM</p>
            <div class="flex items-baseline gap-1">
              <p class="sensor-value" id="sensor4">-</p>
              <p class="sensor-unit">ppm</p>
            </div>
          </div>
        </div>
        <div class="glass-widget p-4 flex flex-col justify-between text-left">
          <div class="w-10 h-10 flex items-center justify-center rounded-lg" style="background-color: rgba(52, 199, 89, 0.15);"><i class="fas fa-leaf text-xl" style="color: var(--ios-green);"></i></div>
          <div class="mt-4">
            <p class="sensor-label">VPD</p>
            <div class="flex items-baseline gap-1">
              <p class="sensor-value" id="sensor12">-</p>
              <p class="sensor-unit">kPa</p>
            </div>
          </div>
        </div>
      </div>

      <h2 class="text-lg sm:text-xl font-semibold text-gray-700 mb-4 px-2 mt-6">System Power</h2>
      <div class="glass-widget p-4 sm:p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="electrical-item sm:col-span-2">
            <div class="flex items-center gap-4">
              <i class="fas fa-clock fa-fw text-lg w-6 text-center" style="color: var(--ios-blue)"></i>
              <span class="sensor-label font-semibold">Last Data Update</span>
            </div>
            <div class="font-semibold text-sm text-right text-gray-500">
              <span id="api-timestamp">-</span>
            </div>
          </div>
          <div class="electrical-item">
            <div class="flex items-center gap-4"><i class="fas fa-bolt fa-fw text-lg w-6 text-center" style="color: var(--ios-red)"></i><span class="sensor-label font-semibold">Current</span></div>
            <div class="font-semibold text-md text-right"><span id="sensor6">-</span> <span class="font-medium text-gray-400">A</span></div>
          </div>
          <div class="electrical-item">
            <div class="flex items-center gap-4"><i class="fas fa-plug fa-fw text-lg w-6 text-center" style="color: var(--ios-orange)"></i><span class="sensor-label font-semibold">Voltage</span></div>
            <div class="font-semibold text-md text-right"><span id="sensor7">-</span> <span class="font-medium text-gray-400">V</span></div>
          </div>
          <div class="electrical-item">
            <div class="flex items-center gap-4"><i class="fas fa-wave-square fa-fw text-lg w-6 text-center" style="color: var(--ios-blue)"></i><span class="sensor-label font-semibold">Power</span></div>
            <div class="font-semibold text-md text-right"><span id="sensor8">-</span> <span class="font-medium text-gray-400">W</span></div>
          </div>
          <div class="electrical-item">
            <div class="flex items-center gap-4"><i class="fas fa-chart-line fa-fw text-lg w-6 text-center" style="color: var(--ios-indigo)"></i><span class="sensor-label font-semibold">Power Factor</span></div>
            <div class="font-semibold text-md text-right"><span id="sensor9">-</span> <span class="font-medium text-gray-400"></span></div>
          </div>
          <div class="electrical-item">
            <div class="flex items-center gap-4"><i class="fas fa-tachometer-alt fa-fw text-lg w-6 text-center" style="color: var(--ios-teal)"></i><span class="sensor-label font-semibold">Frequency</span></div>
            <div class="font-semibold text-md text-right"><span id="sensor10">-</span> <span class="font-medium text-gray-400">Hz</span></div>
          </div>
          <div class="electrical-item">
            <div class="flex items-center gap-4"><i class="fas fa-burn fa-fw text-lg w-6 text-center" style="color: var(--ios-purple)"></i><span class="sensor-label font-semibold">Energy</span></div>
            <div class="font-semibold text-md text-right"><span id="sensor11">-</span> <span class="font-medium text-gray-400">kWh</span></div>
          </div>
        </div>
      </div>
      
      <h2 class="text-lg sm:text-xl font-semibold text-gray-700 mb-4 px-2 mt-6">Manual Input Values</h2>
      <div class="glass-widget p-4 sm:p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="ppm-input" class="block text-sm font-medium text-gray-700 mb-2">TDS / PPM (ppm)</label>
            <div class="flex gap-2">
              <input type="number" id="ppm-input" min="0" step="0.1" class="flex-1 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter PPM value">
              <button onclick="updateManualValue('ppm')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fas fa-save"></i>
              </button>
            </div>
          </div>
          <div>
            <label for="vpd-input" class="block text-sm font-medium text-gray-700 mb-2">VPD (kPa)</label>
            <div class="flex gap-2">
              <input type="number" id="vpd-input" min="0" step="0.01" class="flex-1 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter VPD value">
              <button onclick="updateManualValue('vpd')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fas fa-save"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
          <i class="fas fa-info-circle mr-1"></i>
          These values are manually set and will be displayed in the sensor readings above.
        </div>
      </div>
      
      <h2 class="text-lg sm:text-xl font-semibold text-gray-700 mb-4 px-2 mt-6">Charts</h2>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5">
        <div class="glass-widget p-4">
          <p class="sensor-label font-semibold mb-2">Temperature (°C)</p>
          <div class="chart-container"><canvas id="tempChart"></canvas></div>
        </div>
        <div class="glass-widget p-4">
          <p class="sensor-label font-semibold mb-2">Humidity (%)</p>
          <div class="chart-container"><canvas id="humidChart"></canvas></div>
        </div>
        <div class="glass-widget p-4">
          <p class="sensor-label font-semibold mb-2">Light Intensity (Lx)</p>
          <div class="chart-container"><canvas id="lightChart"></canvas></div>
        </div>
      </div>
    </section>
  </main>

  <script>
    // Script tidak berubah sampai `resetSensorDisplays`
    function updateDateTime() {
      const now = new Date();
      document.getElementById('header-time').textContent = now.toLocaleTimeString('id-ID', {
        hour12: false
      });
      document.getElementById('header-date').textContent = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric'
      });
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
    const statusIndicators = document.querySelectorAll('.status-indicator');
    const statusTextElements = document.querySelectorAll('#device-status, #device-status-mobile');
    const updateIntervalElements = document.querySelectorAll('#update-interval, #update-interval-mobile');

    function updateStatusUI(isOnline, message) {
      statusIndicators.forEach(dot => {
        dot.classList.toggle('online', isOnline);
        dot.classList.toggle('offline', !isOnline);
      });
      statusTextElements.forEach(el => el.textContent = isOnline ? 'Online' : 'Offline');
      updateIntervalElements.forEach(el => el.textContent = message);
    }

    // MODIFIKASI: Fungsi reset diperbarui untuk elemen timestamp
    function resetSensorDisplays() {
      for (let i = 1; i <= 12; i++) {
        const el = document.getElementById(`sensor${i}`);
        if (el) el.textContent = '-';
      }
      const batteryBar = document.getElementById('battery-bar');
      if (batteryBar) {
        batteryBar.style.width = '0%';
        batteryBar.style.backgroundColor = 'var(--text-tertiary)';
      }
      const batteryIcon = document.getElementById('battery-icon');
      if (batteryIcon) {
        batteryIcon.className = 'fas fa-battery-slash text-2xl';
        batteryIcon.style.color = 'var(--text-tertiary)';
      }
      const batteryTextColor = document.getElementById('battery-text-color');
      if (batteryTextColor) {
        batteryTextColor.style.color = 'var(--text-tertiary)';
      }
      // BARU: Reset elemen timestamp
      const timestampEl = document.getElementById('api-timestamp');
      if (timestampEl) timestampEl.textContent = '-';
    }

    // Sisa script tidak berubah sampai `load_datastream`
    const getChartOptions = (title) => ({
      responsive: true,
      maintainAspectRatio: false,
      animation: {
        duration: 0,
        easing: 'easeInOutQuad'
      },
      scales: {
        x: {
          ticks: {
            color: '#333333'
          },
          grid: {
            color: 'rgba(100, 100, 100, 0.2)'
          }
        },
        y: {
          ticks: {
            color: '#333333'
          },
          grid: {
            color: 'rgba(100, 100, 100, 0.2)'
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.9)',
          titleColor: '#ffffff',
          bodyColor: '#ffffff',
          borderColor: 'rgba(255,255,255,0.2)',
          borderWidth: 1
        }
      },
      interaction: {
        intersect: false,
        mode: 'index',
      },
      elements: {
        point: {
          radius: 2,
          hoverRadius: 4
        },
        line: {
          tension: 0.2
        }
      }
    });

    function createOptimizedChart(elementId, color) {
      const ctx = document.getElementById(elementId).getContext('2d');
      return new Chart(ctx, {
        type: 'line',
        data: {
          labels: [],
          datasets: [{
            label: elementId.replace('Chart', ''),
            data: [],
            borderColor: color,
            backgroundColor: 'rgba(0, 0, 0, 0)',
            borderWidth: 3,
            pointRadius: 2,
            tension: 0.2,
            fill: false
          }]
        },
        options: getChartOptions(elementId.replace('Chart', ''))
      });
    }
    const tempChart = createOptimizedChart('tempChart', '#FF5A36');
    const humidChart = createOptimizedChart('humidChart', '#00A5FF');
    const lightChart = createOptimizedChart('lightChart', '#FFD700');

    function updateChart(chart, newData) {
      const timeLabel = new Date().toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      chart.data.labels.push(timeLabel);
      chart.data.datasets[0].data.push(newData);
      const maxDataPoints = 50;
      if (chart.data.labels.length > maxDataPoints) {
        chart.data.labels.shift();
        chart.data.datasets[0].data.shift();
      }
      chart.update('quiet');
    }

    function parseCustomTimestamp(tsString) {
      if (!tsString) return null;
      const cleanString = tsString.replace(' - ', ' ').replace(' WIB', '');
      const date = new Date(cleanString);
      return isNaN(date.getTime()) ? null : date;
    }
    let lastTemp = null,
      lastHumid = null,
      lastLight = null;
    const refresh_time = 1000;

    // Function untuk update nilai manual
    async function updateManualValue(type) {
      const input = document.getElementById(type + '-input');
      const value = parseFloat(input.value);
      
      if (isNaN(value) || value < 0) {
        alert('Please enter a valid positive number');
        return;
      }

      try {
        const response = await fetch('{{ route("update.manual.values") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            ppm: type === 'ppm' ? value : parseFloat(document.getElementById('ppm-input').value) || 0,
            vpd: type === 'vpd' ? value : parseFloat(document.getElementById('vpd-input').value) || 0
          })
        });

        if (response.ok) {
          const result = await response.json();
          console.log('Manual value updated:', result);
          
          // Update display immediately
          if (type === 'ppm') {
            document.getElementById('sensor4').textContent = value;
          } else if (type === 'vpd') {
            document.getElementById('sensor12').textContent = value;
          }
          
          // Show success message
          const button = input.nextElementSibling;
          const originalText = button.innerHTML;
          button.innerHTML = '<i class="fas fa-check"></i>';
          button.classList.add('bg-green-500');
          setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-500');
          }, 1000);
        } else {
          throw new Error('Failed to update value');
        }
      } catch (error) {
        console.error('Error updating manual value:', error);
        alert('Failed to update value. Please try again.');
      }
    }

    // Helper: Format time to HH:mm
    function formatTime(dateString) {
      const date = new Date(dateString);
      return date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
    }

    // Fetch and update chart data for the last 10 minutes
    async function updateChartsWithHistory() {
      try {
        const response = await fetch('/api/datastream/history?minutes=10');
        if (!response.ok) throw new Error('Failed to fetch history');
        const history = await response.json();

        // Jika history kosong, JANGAN reset chart, biarkan data lama tetap tampil
        if (!history || history.length === 0) {
          // Bisa tampilkan pesan "No new data" di luar chart jika mau
          return;
        }

        // Ambil data dan label
        const labels = history.map(d => {
          const date = new Date(d.timestamp);
          return date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
        });
        const tempData = history.map(d => d.temp);
        const humidData = history.map(d => d.humid);
        const lightData = history.map(d => d.light_intensity);

        // Update chart
        tempChart.data.labels = labels;
        tempChart.data.datasets[0].data = tempData;
        tempChart.data.datasets[0].label = 'Temperature (°C)';
        tempChart.options.scales.y.title = { display: true, text: '°C' };
        tempChart.options.scales.y.min = 21;
        tempChart.options.scales.y.max = 23;
        tempChart.options.scales.y.ticks = {
          stepSize: 1, 
          callback: function(value) { return value + '°C'; }
        };
        tempChart.update();

        humidChart.data.labels = labels;
        humidChart.data.datasets[0].data = humidData;
        humidChart.data.datasets[0].label = 'Air Humidity';
        humidChart.options.scales.y.title = { display: true, text: '%' };
        humidChart.options.plugins.legend.display = true;
        humidChart.update();

        lightChart.data.labels = labels;
        lightChart.data.datasets[0].data = lightData;
        lightChart.data.datasets[0].label = 'Light Intensity (Lx)';
        lightChart.options.scales.y.title = { display: true, text: 'Lx' };
        lightChart.data.datasets[0].backgroundColor = '#9575CD';
        lightChart.data.datasets[0].borderColor = '#9575CD';
        lightChart.data.datasets[0].borderWidth = 1;
        lightChart.data.datasets[0].type = 'bar';
        lightChart.update();
      } catch (error) {
        console.error('Error updating charts:', error);
      }
    }

    // Update setiap 1 menit
    setInterval(updateChartsWithHistory, 60000);
    document.addEventListener('DOMContentLoaded', updateChartsWithHistory);

    async function load_datastream() {
      try {
        const response = await fetch("{{ route('api.datastream') }}");
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();

        // Logika timeout tidak berubah
        const TIMEOUT_SECONDS = 10;
        if (data.timestamp) {
          const lastUpdate = parseCustomTimestamp(data.timestamp);
          if (lastUpdate) {
            const now = new Date();
            const diffSeconds = (now.getTime() - lastUpdate.getTime()) / 1000;
            if (diffSeconds > TIMEOUT_SECONDS) {
              updateStatusUI(false, 'Device timeout');
            } else {
              updateStatusUI(true, `Last update ${Math.round(diffSeconds)}s ago`);
            }
          } else {
            updateStatusUI(false, 'Invalid timestamp');
            console.error("Invalid timestamp format received:", data.timestamp);
          }
        } else {
          updateStatusUI(true, 'Timestamp missing');
          console.warn("Timestamp field is missing from API response.");
        }

        // BARU: Update elemen timestamp
        const timestampEl = document.getElementById('api-timestamp');
        if (timestampEl && data.timestamp) {
          timestampEl.textContent = data.timestamp;
        }

        // BARU: Update input fields dengan nilai yang ada
        if (data.ppm !== undefined) {
          document.getElementById('ppm-input').value = data.ppm;
        }
        if (data.vpd !== undefined) {
          document.getElementById('vpd-input').value = data.vpd;
        }

        // Mapping sensor tidak berubah
        const mapping = {
          sensor1: data.temp,
          sensor2: data.humid,
          sensor3: data.light_intensity,
          sensor4: data.ppm,
          sensor5: data.batt,
          sensor6: data.current,
          sensor7: data.voltage,
          sensor8: data.power,
          sensor9: data.pf,
          sensor10: data.freq,
          sensor11: data.energy,
          sensor12: data.vpd
        };
        Object.entries(mapping).forEach(([id, value]) => {
          const element = document.getElementById(id);
          if (element) element.textContent = value ?? '-';
        });

        // Sisa fungsi (battery, chart) tidak ada perubahan
        // const batteryPercentage = parseFloat(data.batt);
        // const batteryBar = document.getElementById('battery-bar');
        // const batteryIcon = document.getElementById('battery-icon');
        // const batteryTextColor = document.getElementById('battery-text-color');
        // if (!isNaN(batteryPercentage) && batteryBar && batteryIcon && batteryTextColor) {
        //   batteryBar.style.width = `${batteryPercentage}%`;
        //   let color, iconClass;
        //   if (batteryPercentage <= 20) {
        //     color = 'var(--ios-red)';
        //     iconClass = 'fas fa-battery-quarter text-2xl';
        //   } else if (batteryPercentage <= 50) {
        //     color = 'var(--ios-orange)';
        //     iconClass = 'fas fa-battery-half text-2xl';
        //   } else {
        //     color = 'var(--ios-green)';
        //     iconClass = 'fas fa-battery-full text-2xl';
        //   }
        //   batteryBar.style.backgroundColor = color;
        //   batteryIcon.style.color = color;
        //   batteryTextColor.style.color = color;
        //   batteryIcon.className = iconClass;
        // }
        if (data.temp !== undefined && data.temp !== lastTemp) {
          updateChart(tempChart, data.temp);
          lastTemp = data.temp;
        }
        if (data.humid !== undefined && data.humid !== lastHumid) {
          updateChart(humidChart, data.humid);
          lastHumid = data.humid;
        }
        if (data.light_intensity !== undefined && data.light_intensity !== lastLight) {
          updateChart(lightChart, data.light_intensity);
          lastLight = data.light_intensity;
        }
      } catch (error) {
        console.error('Error fetching data:', error);
        updateStatusUI(false, 'Connection failed');
        resetSensorDisplays();
      }
    }
    document.addEventListener("DOMContentLoaded", () => {
      load_datastream();
      setInterval(load_datastream, refresh_time);
    });
  </script>
</body>

</html>

