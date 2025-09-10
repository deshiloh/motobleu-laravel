@props(['pagination' => false, 'perPage' => 0])

@if($pagination->total() > $perPage)
    <div class="mt-4" wire:key="{{ 'pagination-' . $pagination->currentPage() }}">
        {{ $pagination->links(data: ['scrollTo' => false]) }}
    </div>
@endif


