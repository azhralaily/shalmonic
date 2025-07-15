<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Controls - ShalMonic</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite('resources/js/app.js')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
        body{background:linear-gradient(135deg,#f8fafc,#e0e7ff,#c7f9cc,#fceabb);background-size:400% 400%;animation:gradient-animation 10s ease infinite;font-family:'Figtree',sans-serif}.glass-widget{background:rgba(255,255,255,.75);backdrop-filter:blur(16px) saturate(180%);-webkit-backdrop-filter:blur(16px) saturate(180%);border-radius:1.5rem;border:1px solid rgba(229,229,234,.8);box-shadow:0 8px 32px rgba(0,0,0,.07)}.sensor-label{font-size:.95rem;font-weight:500;color:#64748b}.status-indicator.online{background-color:#22c55e;animation:pulse 2s infinite}.status-indicator.offline{background-color:#ef4444}input[type=range]{-webkit-appearance:none;appearance:none;width:100%;height:8px;background:rgba(120,120,128,.16);border-radius:4px;outline:0}input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;width:28px;height:28px;background:#fff;cursor:pointer;border-radius:50%;border:.5px solid rgba(0,0,0,.04);box-shadow:0 3px 8px rgba(0,0,0,.15),0 1px 1px rgba(0,0,0,.08)}.action-btn{transition:all .2s ease-in-out;background-color:rgba(120,120,128,.12);color:#0f172a}.action-btn.active{background-color:#0d9488;color:#fff;font-weight:600}.toggle-switch-label{cursor:pointer;display:flex;align-items:center;justify-content:space-between;padding:.75rem;background-color:rgba(120,120,128,.08);border-radius:.75rem}.toggle-switch-track{position:relative;width:51px;height:31px;background-color:#e9e9ea;border-radius:9999px;transition:background-color .2s ease-in-out}.toggle-switch-thumb{position:absolute;top:2px;left:2px;width:27px;height:27px;background-color:#fff;border-radius:9999px;box-shadow:0 1px 3px rgba(0,0,0,.1);transition:transform .2s ease-in-out}input[type=checkbox]:checked+.toggle-switch-track{background-color:#22c55e}input[type=checkbox]:checked+.toggle-switch-track>.toggle-switch-thumb{transform:translateX(20px)}@keyframes gradient-animation{0%{background-position:0 50%}50%{background-position:100% 50%}100%{background-position:0 50%}}@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,.7)}50%{box-shadow:0 0 0 6px rgba(34,197,94,0)}}
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center"><a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-75 transition-opacity"><svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/><path d="M12 2L3 7L12 12L21 7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/><path d="M12 22V12" stroke="teal" stroke-width="2" stroke-linejoin="round"/></svg><span class="text-2xl font-bold">ShalMonic</span></a></div>
                <div class="hidden sm:ml-1 sm:flex sm:items-center">
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 text-gray-500 hover:text-gray-900">Dashboard</a>

                    <a href="{{ route('controls') }}" class="px-5 py-2 text-teal-600 border-b-2 border-teal-600">Controls</a>

                    <a href="{{ route('dataoverview') }}" class="px-5 py-2 text-gray-500 hover:text-gray-900">Data Overview</a>
                    
                    @if(auth()->user()->isAdmin())
                        {{-- <a href="{{ route('settings') }}" class="px-5 py-2 text-gray-500 hover:text-gray-900">Settings</a> --}}
                    @endif
                </div>
                <div class="flex items-center"><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full text-sm bg-teal-600 text-white rounded-full hover:bg-teal-700 px-4 py-2">Logout</button></form></div>
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full max-w-7xl mx-auto py-6 px-4 sm:px-8 lg:px-16">
        <div class="flex justify-between items-start sm:items-center mb-6">
            <div><h2 class="text-teal-800 text-3xl sm:text-4xl font-bold mt-1 mb-2">System Controls</h2><p class="text-gray-500">Manage device settings and cultivation parameters.</p></div>
            <div class="text-right">
                <div id="header-time" class="text-xl font-bold text-gray-700"></div>
                <div id="header-date" class="text-gray-500"></div>
                <div class="flex items-center justify-end gap-2 mt-2 text-xs">
                    <div class="status-indicator w-2 h-2 rounded-full"></div>
                    <span id="device-status" class="font-semibold text-gray-700"></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <div class="glass-widget p-5 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Device Controls</h3>
                    <div class="space-y-5">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="sensor-label font-semibold">Light Intensity</label>
                                <div class="px-3 py-1 rounded-full bg-teal-100 text-teal-700 font-bold text-sm"><span id="dimmer-value-display">{{ $initial_intensity }}%</span></div>
                            </div>
                            <input type="range" min="0" max="100" value="{{ $initial_intensity }}" class="w-full" id="dimmer-slider" step="10">
                        </div>
                        <div>
                            <label class="sensor-label font-semibold mb-2 block">Schedule</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center" id="schedule-container">
                                <button data-schedule="0" class="action-btn py-3 px-2 rounded-lg font-medium text-sm @if($initial_schedule == 0) active @endif">Manual</button>
                                <button data-schedule="1" class="action-btn py-3 px-2 rounded-lg font-medium text-sm @if($initial_schedule == 1) active @endif">12 Jam</button>
                                <button data-schedule="2" class="action-btn py-3 px-2 rounded-lg font-medium text-sm @if($initial_schedule == 2) active @endif">16 Jam</button>
                                <button data-schedule="3" class="action-btn py-3 px-2 rounded-lg font-medium text-sm @if($initial_schedule == 3) active @endif">18 Jam</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="manual-controls-section" class="glass-widget p-5 @if($initial_schedule != 0) hidden @endif">
                     <h3 class="text-lg font-semibold text-gray-800 mb-4">Manual Override</h3>
                     <label for="light-toggle-checkbox" class="toggle-switch-label"><span class="font-semibold text-gray-800">Light Status</span><div><input type="checkbox" id="light-toggle-checkbox" class="hidden" @if($initial_light_status == 1) checked @endif><div class="toggle-switch-track"><div class="toggle-switch-thumb"></div></div></div></label>
                </div>
            </div>
            <div>
                <div class="glass-widget p-5 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Parameter Input</h3>
                    <div class="space-y-4">
                        <div>
                           <label for="ppm-input" class="block text-sm font-medium text-gray-700 mb-2">TDS / PPM (ppm)</label>
                           <div class="flex gap-2"><input type="number" id="ppm-input" min="0" step="0.1" class="flex-1 rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" placeholder="Enter PPM value"><button onclick="updateManualValue('ppm')" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors"><i class="fas fa-save"></i></button></div>
                        </div>
                    </div>
                </div>
                <div class="glass-widget p-5">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Plant Growth Settings</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="seeding-date" class="block text-sm font-medium text-gray-700 mb-2">Seeding Date</label>
                            <input type="date" id="seeding-date" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500">
                        </div>
                        <div>
                            <label for="harvest-days" class="block text-sm font-medium text-gray-700 mb-2">Total Harvest Days</label>
                            <input type="number" id="harvest-days" value="60" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500">
                        </div>
                         <div class="text-right">
                             <button class="px-5 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">Save Timeline</button>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        function updateDateTime(){const e=new Date,t=document.getElementById("header-time"),a=document.getElementById("header-date");t&&(t.textContent=e.toLocaleTimeString("id-ID",{hour12:!1})),a&&(a.textContent=e.toLocaleDateString("id-ID",{weekday:"long",month:"long",day:"numeric",year:"numeric"}))}setInterval(updateDateTime,1e3),updateDateTime();const statusIndicators=document.querySelectorAll(".status-indicator"),statusTextElements=document.querySelectorAll("#device-status");function updateStatusUI(e){statusIndicators.forEach(t=>{t.classList.toggle("online",e),t.classList.toggle("offline",!e)}),statusTextElements.forEach(t=>{t.textContent=e?"Connected":"Disconnected"})}document.addEventListener("DOMContentLoaded",()=>{updateStatusUI(!0);const e=document.getElementById("dimmer-slider"),t=document.getElementById("dimmer-value-display"),a=document.getElementById("schedule-container"),n=document.getElementById("manual-controls-section"),o=document.getElementById("light-toggle-checkbox");let l={light_intensity:parseInt(e.value),schedule:parseInt(a.querySelector(".active").dataset.schedule),light_status:o.checked?1:0};let c;const d=async e=>{updateStatusUI(!0);try{const t=await fetch('{{ route("controls.store") }}',{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content")},body:JSON.stringify(e)});if(!t.ok)throw new Error(`Failed to save. Server responded with ${t.status}`);const a=await t.json();console.log("Save successful:",a),updateStatusUI(!0)}catch(e){console.error("Error saving control state:",e),updateStatusUI(!1)}};e.addEventListener("input",a=>{const n=parseInt(a.target.value);l.light_intensity=n,t.textContent=`${n}%`,clearTimeout(c),c=setTimeout(()=>{d(l)},500)}),a.addEventListener("click",e=>{const t=e.target.closest("[data-schedule]");if(!t)return;const o=parseInt(t.dataset.schedule);n.classList.toggle("hidden",0!==o),a.querySelector(".active").classList.remove("active"),t.classList.add("active"),l.schedule=o,d(l)}),o.addEventListener("change",e=>{l.light_status=e.target.checked?1:0,d(l)})});async function updateManualValue(e){const t=document.getElementById(e+"-input"),a=parseFloat(t.value);if(isNaN(a)||a<0)return void alert("Please enter a valid positive number");try{const n=await fetch('{{ route("update.manual.values") }}',{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content")},body:JSON.stringify({ppm:"ppm"===e?a:parseFloat(document.getElementById("ppm-input").value)||0,vpd:"vpd"===e?a:parseFloat(document.getElementById("vpd-input").value)||0})});if(n.ok){const e=t.nextElementSibling;e.innerHTML='<i class="fas fa-check"></i>',e.classList.remove("bg-teal-600"),e.classList.add("bg-green-500"),setTimeout(()=>{e.innerHTML='<i class="fas fa-save"></i>',e.classList.add("bg-teal-600"),e.classList.remove("bg-green-500")},1500),updateStatusUI(!0)}else throw new Error("Failed to update value")}catch(e){console.error("Error updating manual value:",e),updateStatusUI(!1),alert("Failed to update value. Please try again.")}}
    </script>
</body>
</html>