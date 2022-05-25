<li>
    <span {{ $attributes->merge(['class' => 'block py-2 pr-4 pl-3 text-white rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-white']) }} aria-current="page">
        {{ $slot }}
    </span>
</li>
