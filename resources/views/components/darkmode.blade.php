@props([
    'size' => '8'
])

<!-- This example requires Tailwind CSS v2.0+ -->
<!-- Enabled: "", Not Enabled: "bg-gray-200" -->
<button
    x-data="{
        checked : false,
        init() {
            this.checked = localStorage.theme === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
        }
    }"
    type="button"
    class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    :class="checked ? 'bg-indigo-600' : 'bg-gray-200' "
    role="switch"
    :aria-checked="checked"
    data-toggle-theme="light,dark"
    x-on:click="checked = ! checked"
>
    <span class="sr-only">Dark mode</span>
    <!-- Enabled: "", Not Enabled: "translate-x-0" -->
    <span
        class="pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
        :class="checked ? 'translate-x-5' : 'translate-x-0' "
    >
    <!-- Enabled: "opacity-0 ease-out duration-100", Not Enabled: "opacity-100 ease-in duration-200" -->
    <span
        class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
        :class="checked ? 'opacity-0 ease-out duration-100' : 'opacity-100 ease-in duration-200' "
        aria-hidden="true"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
        </svg>
    </span>

        <!-- Enabled: "opacity-100 ease-in duration-200", Not Enabled: "opacity-0 ease-out duration-100" -->
    <span
        class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
        :class="checked ? 'opacity-100 ease-in duration-200' : 'opacity-0 ease-out duration-100' "
        aria-hidden="true"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
    </span>
  </span>
</button>


{{--<div class="form-control">
    <label class="label cursor-pointer space-x-2">
        <span class="label-text"><!-- sun icon -->
            <svg class="swap-off fill-current w-{{ $size }} h-{{ $size }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/></svg>
        </span>
        <input type="checkbox" class="toggle toggle-sm darkmode toggle-primary" data-toggle-theme="light,dark"/>
        <span class="label-text"><!-- sun icon -->
            <svg class="swap-on fill-current w-{{ $size }} h-{{ $size }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/></svg>
        </span>
    </label>
</div>--}}
