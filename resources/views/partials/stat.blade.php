<?php

declare(strict_types=1);

?>
@php
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Facades\FilamentView;

    $chartColor = $getChartColor() ?? 'gray';
    $descriptionColor = $getDescriptionColor() ?? 'gray';
    $descriptionIcon = $getDescriptionIcon();
    $descriptionIconPosition = $getDescriptionIconPosition();
    $url = $getUrl();
    $tag = $url ? 'a' : 'div';
    $dataChecksum = $generateDataChecksum();

    $descriptionIconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-wi-stats-overview-stat-description-icon h-5 w-5',
        match ($descriptionColor) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-500',
        },
    ]);

    $descriptionIconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $descriptionColor,
            shades: [500],
            alias: 'widgets::stats-overview-widget.stat.description.icon',
        ) => $descriptionColor !== 'gray',
    ]);
@endphp

<{!! $tag !!}
@if ($url)
    {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab()) }}
@endif
{{
    $getExtraAttributeBag()
        ->class([
            'fi-wi-stats-overview-stat group relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
        ])
}}
>
<div class="flex flex-col md:flex-row md:justify-between md:items-end flex-wrap gap-4">

    <div class="grid">
        @if(filled($getLabel()))
            <div
                @class([
                    'flex items-center gap-x-2 mb-5 ring-1 ring-inset rounded-full pr-4 whitespace-nowrap py-1.5 pl-3 w-min dark:bg-gray-800 dark:ring-white/10 dark:text-slate-100',
                    match ($descriptionColor) {
                        'red' => 'bg-red-50 text-red-700 ring-red-600/10',
                        'blue' => 'bg-blue-50 text-blue-700 ring-blue-600/10',
                        'purple' => 'bg-purple-50 text-purple-700 ring-purple-600/10',
                        'green' => 'bg-green-50 text-green-700 ring-green-600/10',
                        'orange' => 'bg-orange-50 text-orange-700 ring-orange-600/10',
                        'amber' => 'bg-amber-50 text-amber-700 ring-amber-600/10',
//                        'gray' => '',
                        default => 'bg-gray-50 text-gray-600 ring-gray-500/10',
                    },
                ])
                {{--                class="flex items-center gap-x-2 border rounded-full pr-4 whitespace-nowrap py-1.5 pl-3 w-min"--}}
        >
            @if ($icon = $getIcon())
                <x-filament::icon
                        :icon="$icon"
                        class="fi-wi-stats-overview-stat-icon h-5 w-5 text-gray-400dark:text-gray-500"
                />
            @endif

            <span
                    class="fi-wi-stats-overview-stat-label text-sm font-medium text-gray-500dark:text-gray-400"
            >
                {{ $getLabel() }}
            </span>
        </div>
        @endif

        <div
                class="fi-wi-stats-overview-stat-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white"
        >
            {{ $getValue() }}
        </div>

        @if ($description = $getDescription())
            <div class="flex mt-2 items-center gap-x-1">
                @if ($descriptionIcon && in_array($descriptionIconPosition, [IconPosition::Before, 'before']))
                    <x-filament::icon
                            :icon="$descriptionIcon"
                            :class="$descriptionIconClasses"
                            :style="$descriptionIconStyles"
                    />
                @endif

                <span
                    @class([
                        'fi-wi-stats-overview-stat-description text-sm lg:max-w-xs xl:max-w-none',
                        match ($descriptionColor) {
                            'gray' => 'text-gray-500 dark:text-gray-400',
                            default => 'fi-color-custom text-custom-600 dark:text-custom-400',
                        },
                        is_string($descriptionColor) ? "fi-color-{$descriptionColor}" : null,
                    ])
                        @style([
                            \Filament\Support\get_color_css_variables(
                                $descriptionColor,
                                shades: [400, 600],
                                alias: 'widgets::stats-overview-widget.stat.description',
                            ) => $descriptionColor !== 'gray',
                        ])
                >
                    {{ $description }}
                </span>

                @if ($descriptionIcon && in_array($descriptionIconPosition, [IconPosition::After, 'after']))
                    <x-filament::icon
                            :icon="$descriptionIcon"
                            :class="$descriptionIconClasses"
                            :style="$descriptionIconStyles"
                    />
                @endif
            </div>
        @endif
    </div>

    <div class="p-3 rounded-full bg-slate-50 ring-1 ring-gray-950/10 dark:bg-gray-800 dark:ring-white/10 hidden lg:flex">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
             class="-rotate-45 size-6 group-hover:rotate-0 group-focus:rotate-0 transition-all duration-500"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
        </svg>
    </div>
</div>


@if ($chart = $getChart())
    {{-- An empty function to initialize the Alpine component with until it's loaded with `x-load`. This removes the need for `x-ignore`, allowing the chart to be updated via Livewire polling. --}}
    <div x-data="{ statsOverviewStatChart: function () {} }">
        <div
                @if (FilamentView::hasSpaMode())
                    x-load="visible"
                @else
                    x-load
                @endif
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('stats-overview/stat/chart', 'filament/widgets') }}"
                x-data="statsOverviewStatChart({
                            dataChecksum: @js($dataChecksum),
                            labels: @js(array_keys($chart)),
                            values: @js(array_values($chart)),
                        })"
                @class([
                    'fi-wi-stats-overview-stat-chart absolute inset-x-0 bottom-0 overflow-hidden rounded-b-xl',
                    match ($chartColor) {
                        'gray' => null,
                        default => 'fi-color-custom',
                    },
                    is_string($chartColor) ? "fi-color-{$chartColor}" : null,
                ])
                @style([
                    \Filament\Support\get_color_css_variables(
                        $chartColor,
                        shades: [50, 400, 500],
                        alias: 'widgets::stats-overview-widget.stat.chart',
                    ) => $chartColor !== 'gray',
                ])
        >
            <canvas x-ref="canvas" class="h-6"></canvas>

            <span
                    x-ref="backgroundColorElement"
                    @class([
                        match ($chartColor) {
                            'gray' => 'text-gray-100 dark:text-gray-800',
                            default => 'text-custom-50 dark:text-custom-400/10',
                        },
                    ])
                ></span>

            <span
                    x-ref="borderColorElement"
                    @class([
                        match ($chartColor) {
                            'gray' => 'text-gray-400',
                            default => 'text-custom-500 dark:text-custom-400',
                        },
                    ])
                ></span>
        </div>
    </div>
@endif
</{!! $tag !!}>
<?php 
