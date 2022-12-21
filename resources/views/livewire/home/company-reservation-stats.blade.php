<div wire:poll="reloadEntreprise">

    @if($isLast)
        <div class="inline-flex items-baseline px-2.5 py-0.5 rounded-lg text-sm font-medium bg-red-100 text-red-800 md:mt-2 lg:mt-0">

            <svg class="-ml-1 mr-0.5 h-5 w-5 flex-shrink-0 self-center text-red-500" x-description="Heroicon name: mini/arrow-down" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd"></path>
            </svg>
            <a href="{!! route('admin.entreprises.show', ['entreprise' => $entreprise->id]) !!}">{{ $entreprise->nom }}</a>
        </div>
    @else
        <div class="inline-flex items-baseline px-2.5 py-0.5 rounded-lg text-sm font-medium bg-green-100 text-green-800 md:mt-2 lg:mt-0">

            <svg class="-ml-1 mr-0.5 h-5 w-5 flex-shrink-0 self-center text-green-500" x-description="Heroicon name: mini/arrow-up" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z" clip-rule="evenodd"></path>
            </svg>

            <a href="{!! route('admin.entreprises.show', ['entreprise' => $entreprise->id]) !!}">{{ $entreprise->nom }}</a>

        </div>
    @endif
</div>