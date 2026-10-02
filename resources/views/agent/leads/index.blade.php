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

        @if ($errors->any())
            <x-ui.alert tone="danger">
                <x-ui.alert-title>{{ __('Please check your selections.') }}</x-ui.alert-title>
                <x-ui.alert-description>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert-description>
            </x-ui.alert>
        @endif

        <x-ui.table variant="card">
            <x-ui.table-header>
                <x-ui.table-row>
                    <x-ui.table-head>{{ __('Name') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Contact') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Interested in') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Notes') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Budget (USD)') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Submitted') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Actions') }}</x-ui.table-head>
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
                            <span class="block max-w-56 whitespace-normal">{{ $lead->interested_in }}</span>
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <x-lead-notes :notes="$lead->notes" />
                        </x-ui.table-cell>
                        <x-ui.table-cell class="tabular-nums">
                            {{ $lead->budget !== null ? '$'.number_format((float) $lead->budget) : '—' }}
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <x-ui.badge :tone="$statusMeta[$lead->status->value]['tone']">
                                {{ $statusMeta[$lead->status->value]['label'] }}
                            </x-ui.badge>
                        </x-ui.table-cell>
                        <x-ui.table-cell>{{ $lead->created_at?->format('Y-m-d H:i') ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <form method="POST" action="{{ route('leads.status', $lead) }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-ui.label for="lead-status-{{ $lead->id }}" class="sr-only">
                                        {{ __('Change status for :name', ['name' => $lead->first_name.' '.$lead->last_name]) }}
                                    </x-ui.label>
                                    <x-ui.select
                                        :native="true"
                                        id="lead-status-{{ $lead->id }}"
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
                        <x-ui.table-cell colspan="8" class="text-muted-foreground py-6 text-center">
                            {{ __('No leads assigned to you yet.') }}
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>

        <x-paginator :paginator="$leads" />
    </div>
</x-app-layout>
