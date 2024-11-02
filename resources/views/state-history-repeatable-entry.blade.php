@php
    $isContained = $isContained();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-in-repeatable',
                    'fi-contained' => $isContained,
                ])
        }}
    >
        @if (count($childComponentContainers = $getChildComponentContainers()))
            <ul>
                <x-filament::grid
                    :default="$getGridColumns('default')"
                    :sm="$getGridColumns('sm')"
                    :md="$getGridColumns('md')"
                    :lg="$getGridColumns('lg')"
                    :xl="$getGridColumns('xl')"
                    :two-xl="$getGridColumns('2xl')"
                    class="gap-1"
                >                 
                    @foreach ($childComponentContainers as $container)
                        <li
                            @class([
                                'fi-in-repeatable-item block',
                                'rounded px-4 py-1 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10' => $isContained,
                                'bg-gray-50' => $loop->first
                            ])
                        >
                            {{ $container }}
                        </li>
                    @endforeach
                </x-filament::grid>
            </ul>
        @elseif (($placeholder = $getPlaceholder()) !== null)
            <x-filament-infolists::entries.placeholder>
                {{ $placeholder }}
            </x-filament-infolists::entries.placeholder>
        @endif
    </div>
</x-dynamic-component>
