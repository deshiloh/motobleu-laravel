@props([
    'type' => 'success',
    'message' => ''
])
<div class="container mx-auto mt-3">
    <div @class([
        'alert flex flex-row items-center p-5 rounded',
        'bg-red-300' => ($type == 'danger'),
        'bg-green-300' => ($type == 'success'),
        ])>
        <div @class([
            'alert-icon flex items-center border-2 justify-center h-10 w-10 flex-shrink-0 rounded-full',
            'bg-red-100 border-red-500' => ($type == 'danger'),
            'bg-green-100 border-green-500' => ($type == 'success'),
        ])>
				<span @class([
                      'text-red-500' => ($type == 'danger'),
                      'text-green-500' => ($type == 'success')
                ])>
                    @if($type == 'success')
                        <svg fill="currentColor"
                             viewBox="0 0 20 20"
                             class="h-6 w-6">
						<path fill-rule="evenodd"
                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                              clip-rule="evenodd"></path>
					    </svg>
                    @endif
                    @if($type == 'danger')
                        <svg fill="currentColor"
                             viewBox="0 0 20 20"
                             class="h-6 w-6">
                            <path fill-rule="evenodd"
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                  clip-rule="evenodd"></path>
                        </svg>
                    @endif
				</span>
        </div>
        <div class="alert-content ml-4">
            <div @class([
                 'alert-title font-semibold text-lg',
                 'text-red-800' => ($type == 'danger'),
                 'text-green-900' => ($type == 'success')
            ])>
                {{ $title }}
            </div>
            <div @class([
                'alert-description text-sm',
                'text-red-600' => ($type == 'danger'),
                'text-green-800' => ($type == 'success')
            ])>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
