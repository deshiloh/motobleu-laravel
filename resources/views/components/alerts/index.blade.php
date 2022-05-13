@if ($message = Session::get('success'))
    <x-alerts.item>
        <x-slot name="title">{{ $message }}</x-slot>
    </x-alerts.item>
@endif

@if ($message = Session::get('error'))
    <x-alerts.item type="danger">
        <x-slot name="title">{{ $message }}</x-slot>
    </x-alerts.item>
@endif

@if ($errors->any())
    <x-alerts.item type="danger">
        <x-slot name="title">Please check the form below for errors</x-slot>
    </x-alerts.item>
@endif
