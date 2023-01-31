@props(['pagination' => false, 'perPage' => 0])

@if($pagination->total() > $perPage)
    <div class="mt-4">
        {{ $pagination->links() }}
    </div>
@endif


