<x-app-layout>
    <x-slot name="header">{{ __('Properties') }}</x-slot>

    <div class="flex flex-col gap-4">
        @if (session('status'))
            @php
                $statusMessages = [
                    'property-approved' => __('Property approved and published.'),
                    'property-unpublished' => __('Property unpublished.'),
                    'property-deleted' => __('Property deleted.'),
                ];
            @endphp
            <x-ui.alert tone="success">
                <x-lucide-circle-check />
                <x-ui.alert-title>{{ $statusMessages[session('status')] ?? __('Done.') }}</x-ui.alert-title>
            </x-ui.alert>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-muted-foreground text-sm">{{ __('Review and publish listings.') }}</p>

            <div class="flex flex-wrap gap-2">
                @foreach (['' => __('All'), 'pending' => __('Pending'), 'live' => __('Live'), 'sold' => __('Sold')] as $value => $label)
                    <x-ui.button
                        :href="route('admin.properties.index', $value ? ['status' => $value] : [])"
                        :variant="$status === ($value ?: null) ? 'default' : 'outline'"
                        size="sm"
                    >{{ $label }}</x-ui.button>
                @endforeach
            </div>
        </div>

        <x-ui.table variant="card">
            <x-ui.table-header>
                <x-ui.table-row>
                    <x-ui.table-head>{{ __('Listing') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Agent') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Area') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Price (USD)') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Actions') }}</x-ui.table-head>
                </x-ui.table-row>
            </x-ui.table-header>
            <x-ui.table-body>
                @forelse ($properties as $property)
                    @php
                        $cover = $property->images->firstWhere('type', \App\Enums\PropertyImageType::FEATURED_IMAGE) ?? $property->images->first();
                        $area = $property->categories->firstWhere('group_type', \App\Enums\CategoryGroupType::PROPERTY_AREA);
                    @endphp
                    <x-ui.table-row>
                        <x-ui.table-cell>
                            <div class="flex items-center gap-3">
                                @if ($cover)
                                    <img src="{{ $cover->url }}" alt="" class="size-10 rounded-md object-cover" />
                                @else
                                    <div class="bg-muted flex size-10 items-center justify-center rounded-md">
                                        <x-lucide-image class="text-muted-foreground size-4" aria-hidden="true" />
                                    </div>
                                @endif
                                <span class="font-medium">{{ $property->name }}</span>
                            </div>
                        </x-ui.table-cell>
                        <x-ui.table-cell class="text-muted-foreground">
                            {{ $property->agents->pluck('name')->join(', ') ?: '—' }}
                        </x-ui.table-cell>
                        <x-ui.table-cell>{{ $area?->name ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell class="tabular-nums">{{ number_format((float) $property->price_usd) }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            @if ($property->is_sold)
                                <x-ui.badge tone="neutral">{{ __('Sold') }}</x-ui.badge>
                            @elseif ($property->is_active)
                                <x-ui.badge tone="success">{{ __('Live') }}</x-ui.badge>
                                @if ($property->is_featured)<x-ui.badge tone="info">{{ __('Featured') }}</x-ui.badge>@endif
                            @else
                                <x-ui.badge tone="warning">{{ __('Pending') }}</x-ui.badge>
                            @endif
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <div class="flex items-center justify-end gap-2">
                                @if (! $property->is_active && ! $property->is_sold)
                                    <form method="POST" action="{{ route('admin.properties.approve', $property) }}">
                                        @csrf
                                        @method('PATCH')
                                        <x-ui.button type="submit" size="sm">{{ __('Approve') }}</x-ui.button>
                                    </form>
                                @elseif ($property->is_active)
                                    <form method="POST" action="{{ route('admin.properties.unpublish', $property) }}">
                                        @csrf
                                        @method('PATCH')
                                        <x-ui.button type="submit" variant="outline" size="sm">{{ __('Unpublish') }}</x-ui.button>
                                    </form>
                                @endif

                                <x-ui.alert-dialog>
                                    <x-ui.alert-dialog-trigger>
                                        <x-ui.button variant="destructive" size="sm" type="button">{{ __('Delete') }}</x-ui.button>
                                    </x-ui.alert-dialog-trigger>
                                    <x-ui.alert-dialog-content>
                                        <x-ui.alert-dialog-header>
                                            <x-ui.alert-dialog-title>{{ __('Delete :name?', ['name' => $property->name]) }}</x-ui.alert-dialog-title>
                                            <x-ui.alert-dialog-description>
                                                {{ __('This permanently deletes the listing and its photos.') }}
                                            </x-ui.alert-dialog-description>
                                        </x-ui.alert-dialog-header>
                                        <x-ui.alert-dialog-footer>
                                            <x-ui.alert-dialog-cancel>{{ __('Cancel') }}</x-ui.alert-dialog-cancel>
                                            <form method="POST" action="{{ route('admin.properties.destroy', $property) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.alert-dialog-action x-on:click="$el.closest('form').submit()">
                                                    {{ __('Delete') }}
                                                </x-ui.alert-dialog-action>
                                            </form>
                                        </x-ui.alert-dialog-footer>
                                    </x-ui.alert-dialog-content>
                                </x-ui.alert-dialog>
                            </div>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @empty
                    <x-ui.table-row>
                        <x-ui.table-cell colspan="6" class="text-muted-foreground py-6 text-center">
                            {{ __('No properties found.') }}
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>

        <x-paginator :paginator="$properties" />
    </div>
</x-app-layout>
