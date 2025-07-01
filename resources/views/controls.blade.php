<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>Controls - TSS Controls</title>
  <meta name="description" content="Modern, professional IoT Gateway Dashboard inspired by iOS.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('assets/tailwind.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
  <style>
    /* ... CSS yang ada sebelumnya, tidak ada perubahan ... */
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
      backdrop-filter: blur(20px);
      border-radius: 1.25rem;
      border: 1px solid var(--glass-border-color);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.07);
    }

    .sensor-label {
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--text-secondary);
    }

    input[type="range"] {
      -webkit-appearance: none;
      appearance: none;
      width: 100%;
      height: 8px;
      background: rgba(120, 120, 128, 0.16);
      border-radius: 4px;
      outline: none;
    }

    input[type="range"]::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 28px;
      height: 28px;
      background: #fff;
      cursor: pointer;
      border-radius: 50%;
      border: 0.5px solid rgba(0, 0, 0, 0.04);
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15), 0 1px 1px rgba(0, 0, 0, 0.08);
    }

    .action-btn {
      transition: all 0.2s ease-in-out;
      background-color: rgba(120, 120, 128, 0.12);
      color: var(--text-primary);
    }

    .action-btn.active {
      background-color: var(--ios-blue);
      color: white;
      font-weight: 600;
    }

    .tab-nav {
      background-color: rgba(120, 120, 128, 0.12);
      padding: 0.5rem;
      border-radius: 0.75rem;
      display: flex;
    }

    .tab-link {
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      font-weight: 600;
      font-size: 0.875rem;
      color: var(--text-secondary);
      transition: all 0.3s ease;
      cursor: pointer;
      flex: 1;
      text-align: center;
      text-decoration: none;
    }

    .tab-link.active {
      background-color: var(--glass-background);
      color: var(--text-primary);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .toggle-switch-label {
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.75rem;
      background-color: rgba(120, 120, 128, 0.08);
      border-radius: 0.75rem;
    }

    .toggle-switch-track {
      position: relative;
      width: 51px;
      height: 31px;
      background-color: #E9E9EA;
      border-radius: 9999px;
      transition: background-color 0.2s ease-in-out;
    }

    .toggle-switch-thumb {
      position: absolute;
      top: 2px;
      left: 2px;
      width: 27px;
      height: 27px;
      background-color: white;
      border-radius: 9999px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease-in-out;
    }

    input[type="checkbox"]:checked+.toggle-switch-track {
      background-color: var(--ios-green);
    }

    input[type="checkbox"]:checked+.toggle-switch-track>.toggle-switch-thumb {
      transform: translateX(20px);
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
        <div id="dots" class="status-indicator w-2 h-2 rounded-full"></div><span id="device-status" class="font-semibold text-gray-700"></span><span class="text-gray-500"></span><span id="update-interval" class="text-gray-500"></span>
      </div>
    </div>
  </header>
  <nav class="mb-6">
    <div class="tab-nav">
        <a href="{{ route('dashboard') }}" class="tab-link">Dashboard</a>
        <a href="{{ route('controls') }}" class="tab-link active">Controls</a>
        <a href="{{ route('dataoverview') }}" class="tab-link">Data Log</a>
    </div>
  </nav>
  <main>
    <h2 class="text-lg sm:text-xl font-semibold text-gray-700 mb-4 px-2">Device Controls</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <div class="glass-widget p-4 sm:p-5 space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="font-semibold text-gray-800">Light Intensity</h3>
          <div class="flex items-center gap-2 px-3 py-1 rounded-full" style="background-color: rgba(0, 122, 255, 0.1);"><i class="fas fa-lightbulb" style="color: var(--ios-blue)"></i><span id="dimmer-value-display" class="font-bold text-base" style="color: var(--ios-blue)">{{ $initial_intensity }}%</span></div>
        </div>
        <p class="sensor-label -mt-2">Set the output power for the main light.</p>
        <input type="range" min="0" max="100" value="{{ $initial_intensity }}" class="w-full" id="dimmer-slider" step="10">
      </div>
      <div class="glass-widget p-4 sm:p-5 space-y-4">
        <h3 class="font-semibold text-gray-800">Schedule</h3>
        <p class="sensor-label -mt-2">Select an automated operating schedule.</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center" id="schedule-container">
          <button data-schedule="0" class="action-btn py-3 px-2 rounded-lg font-medium text-sm @if($initial_schedule == 0) active @endif">Manual</button>
          <button data-schedule="1" class="action-btn py-3 px-2 rounded-lg font-medium text-sm @if($initial_schedule == 1) active @endif">12 Jam</button>
          <button data-schedule="2" class="action-btn py-3 px-2 rounded-lg font-medium text-sm @if($initial_schedule == 2) active @endif">16 Jam</button>
          <button data-schedule="3" class="action-btn py-3 px-2 rounded-lg font-medium text-sm @if($initial_schedule == 3) active @endif">18 Jam</button>
        </div>
      </div>
    </div>
    <div id="manual-controls-section" class="mt-5 @if($initial_schedule != 0) hidden @endif">
      <h2 class="text-lg sm:text-xl font-semibold text-gray-700 mb-4 px-2">Manual Controls</h2>
      <div class="glass-widget p-4 sm:p-5">
        <label for="light-toggle-checkbox" class="toggle-switch-label"><span class="font-semibold text-gray-800">Light Status</span>
          <div><input type="checkbox" id="light-toggle-checkbox" class="hidden" @if($initial_light_status == 1) checked @endif>
            <div class="toggle-switch-track">
              <div class="toggle-switch-thumb"></div>
            </div>
          </div>
        </label>
      </div>
    </div>
  </main>

  <script>
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

    // MODIFIKASI: Fungsi diubah untuk menampilkan "Connected" / "Disconnected"
    function updateStatusUI(isConnected) {
      statusIndicators.forEach(dot => {
        dot.classList.toggle('online', isConnected);
        dot.classList.toggle('offline', !isConnected);
      });
      // MODIFIKASI: Teks utama diubah
      statusTextElements.forEach(el => el.textContent = isConnected ? 'Connected' : 'Disconnected');
      // MODIFIKASI: Teks sekunder dihapus
      updateIntervalElements.forEach(el => el.textContent = '');
    }

    document.addEventListener('DOMContentLoaded', () => {
      // MODIFIKASI: Status awal diatur ke Connected
      updateStatusUI(true);

      const dimmerSlider = document.getElementById('dimmer-slider');
      const dimmerValueDisplay = document.getElementById('dimmer-value-display');
      const scheduleContainer = document.getElementById('schedule-container');
      const manualControlsSection = document.getElementById('manual-controls-section');
      const lightToggle = document.getElementById('light-toggle-checkbox');

      let currentControls = {
        light_intensity: parseInt(dimmerSlider.value),
        schedule: parseInt(scheduleContainer.querySelector('.active').dataset.schedule),
        light_status: lightToggle.checked ? 1 : 0
      };

      let debounceTimer;

      const saveControlState = async (data) => {
        // Status saat menyimpan tetap Connected (hijau)
        updateStatusUI(true);
        try {
          const response = await fetch('{{ route('controls.store') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data),
          });
          if (!response.ok) throw new Error(`Failed to save. Server responded with ${response.status}`);

          const result = await response.json();
          console.log('Save successful:', result);
          // Jika sukses, status tetap Connected
          updateStatusUI(true);

        } catch (error) {
          console.error("Error saving control state:", error);
          // MODIFIKASI: Jika gagal, status menjadi Disconnected
          updateStatusUI(false);
        }
      };

      dimmerSlider.addEventListener('input', (e) => {
        const newIntensity = parseInt(e.target.value);
        currentControls.light_intensity = newIntensity;
        dimmerValueDisplay.textContent = `${newIntensity}%`;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          saveControlState(currentControls);
        }, 500);
      });

      scheduleContainer.addEventListener('click', (e) => {
        const clickedButton = e.target.closest('[data-schedule]');
        if (!clickedButton) return;
        const newSchedule = parseInt(clickedButton.dataset.schedule);
        manualControlsSection.classList.toggle('hidden', newSchedule !== 0);
        scheduleContainer.querySelector('.active').classList.remove('active');
        clickedButton.classList.add('active');
        currentControls.schedule = newSchedule;
        saveControlState(currentControls);
      });

      lightToggle.addEventListener('change', (e) => {
        currentControls.light_status = e.target.checked ? 1 : 0;
        saveControlState(currentControls);
      });
    });
  </script>

</body>

</html>