@php
    use App\Enums\LeadStatus;

    $statusMeta = [
        LeadStatus::NEW->value => ['tone' => 'info', 'label' => __('New')],
        LeadStatus::CONTACTED->value => ['tone' => 'neutral', 'label' => __('Contacted')],
        LeadStatus::QUALIFIED->value => ['tone' => 'warning', 'label' => __('Qualified')],
        LeadStatus::CLOSED->value => ['tone' => 'success', 'label' => __('Closed')],
        LeadStatus::LOST->value => ['tone' => 'danger', 'label' => __('Lost')],
    ];

    $statusOptions = [
        LeadStatus::CONTACTED->value => __('Contacted'),
        LeadStatus::QUALIFIED->value => __('Qualified'),
        LeadStatus::CLOSED->value => __('Closed'),
        LeadStatus::LOST->value => __('Lost'),
    ];
@endphp

<x-app-layout>
    <x-slot name="header">{{ __('Leads') }}</x-slot>

    <div class="flex flex-col gap-4">
        @if (session('status'))
            <x-ui.alert tone="success">
                <x-lucide-circle-check />
                <x-ui.alert-title>{{ session('status') === 'lead-reassigned' ? __('Lead reassigned.') : __('Lead updated.') }}</x-ui.alert-title>
            </x-ui.alert>
        @endif

        <p class="text-muted-foreground text-sm">{{ __('Leads assigned to you.') }}</p>

        <x-ui.table variant="card">
            <x-ui.table-header>
                <x-ui.table-row>
                    <x-ui.table-head>{{ __('Name') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Contact') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Interested in') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Notes') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Budget') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Update status') }}</x-ui.table-head>
                </x-ui.table-row>
            </x-ui.table-header>
            <x-ui.table-body>
                @forelse ($leads as $lead)
                    <x-ui.table-row>
                        <x-ui.table-cell>
                            <span class="font-medium">{{ $lead->first_name }} {{ $lead->last_name }}</span>
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <span class="text-muted-foreground block text-sm">{{ $lead->email }}</span>
                            <span class="text-muted-foreground block text-xs">{{ $lead->phone ?? '—' }}</span>
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <span class="line-clamp-1 max-w-56" title="{{ $lead->interested_in }}">{{ $lead->interested_in }}</span>
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <span class="text-muted-foreground line-clamp-1 max-w-56" title="{{ $lead->notes }}">{{ $lead->notes ?? '—' }}</span>
                        </x-ui.table-cell>
                        <x-ui.table-cell class="tabular-nums">
                            {{ $lead->budget ? '$'.number_format((float) $lead->budget) : '—' }}
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <x-ui.badge :tone="$statusMeta[$lead->status->value]['tone']">
                                {{ $statusMeta[$lead->status->value]['label'] }}
                            </x-ui.badge>
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('leads.status', $lead) }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-ui.select
                                        :native="true"
                                        name="status"
                                        :value="''"
                                        :placeholder="__('Change status…')"
                                        :options="$statusOptions"
                                        class="h-8 w-40 text-xs"
                                        @change="$el.form.submit()"
                                    />
                                </form>
                                <x-ui.button :href="route('leads.edit', $lead)" variant="outline" size="sm">{{ __('Edit') }}</x-ui.button>
                            </div>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @empty
                    <x-ui.table-row>
                        <x-ui.table-cell colspan="7" class="text-muted-foreground py-6 text-center">
                            {{ __('No leads assigned to you yet.') }}
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>

        <x-paginator :paginator="$leads" />
    </div>
</x-app-layout>
