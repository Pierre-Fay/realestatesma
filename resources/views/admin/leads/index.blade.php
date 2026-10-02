@php
    use App\Enums\LeadStatus;

    $statusMeta = [
        LeadStatus::NEW->value => ['tone' => 'info', 'label' => __('New')],
        LeadStatus::CONTACTED->value => ['tone' => 'neutral', 'label' => __('Contacted')],
        LeadStatus::QUALIFIED->value => ['tone' => 'warning', 'label' => __('Qualified')],
        LeadStatus::CLOSED->value => ['tone' => 'success', 'label' => __('Closed')],
        LeadStatus::LOST->value => ['tone' => 'danger', 'label' => __('Lost')],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">{{ __('All leads') }}</x-slot>

    <div class="flex flex-col gap-4">
        @if (session('status') === 'lead-assignment-updated')
            <x-ui.alert tone="success">
                <x-lucide-circle-check />
                <x-ui.alert-title>{{ __('Lead assignment updated.') }}</x-ui.alert-title>
            </x-ui.alert>
        @endif

        <p class="text-muted-foreground text-sm">{{ __('Review leads from contact requests and property inquiries across all agents.') }}</p>

        @if ($assignableAgents->isEmpty())
            <p class="text-muted-foreground text-sm">{{ __('No enabled agent accounts are available. Assigned leads can still be unassigned.') }}</p>
        @endif

        <form method="GET" action="{{ route('admin.leads.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex flex-col gap-2">
                <x-ui.label for="assignment-filter">{{ __('Assignment') }}</x-ui.label>
                <x-ui.select :native="true" id="assignment-filter" name="assignment" class="w-40">
                    <option value="">{{ __('All leads') }}</option>
                    <option value="unassigned" @selected(($filters['assignment'] ?? null) === 'unassigned')>{{ __('Unassigned') }}</option>
                    <option value="assigned" @selected(($filters['assignment'] ?? null) === 'assigned')>{{ __('Assigned') }}</option>
                </x-ui.select>
            </div>
            <div class="flex flex-col gap-2">
                <x-ui.label for="agent-filter">{{ __('Agent') }}</x-ui.label>
                <x-ui.select :native="true" id="agent-filter" name="agent_id" class="w-48">
                    <option value="">{{ __('All agents') }}</option>
                    @foreach ($agents as $agent)
                        <option value="{{ $agent->id }}" @selected((string) ($filters['agent_id'] ?? '') === (string) $agent->id)>{{ $agent->name }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="flex flex-col gap-2">
                <x-ui.label for="status-filter">{{ __('Status') }}</x-ui.label>
                <x-ui.select :native="true" id="status-filter" name="status" class="w-40">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach ($statusMeta as $value => $meta)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? null) === $value)>{{ $meta['label'] }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <x-ui.button type="submit">{{ __('Filter') }}</x-ui.button>
            <x-ui.button :href="route('admin.leads.index')" variant="ghost">{{ __('Reset') }}</x-ui.button>
        </form>

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
                    <x-ui.table-head>{{ __('Assigned agent') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Submitted') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Assignment actions') }}</x-ui.table-head>
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
                            <x-ui.badge :tone="$statusMeta[$lead->status->value]['tone']">{{ $statusMeta[$lead->status->value]['label'] }}</x-ui.badge>
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            @if ($lead->agent)
                                {{ $lead->agent->name }}
                            @else
                                <x-ui.badge tone="warning">{{ __('Unassigned') }}</x-ui.badge>
                            @endif
                        </x-ui.table-cell>
                        <x-ui.table-cell>{{ $lead->created_at?->format('Y-m-d H:i') ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            <form method="POST" action="{{ route('admin.leads.assign', $lead) }}" class="flex flex-col gap-2">
                                @csrf
                                @method('PATCH')
                                <x-ui.label for="lead-agent-{{ $lead->id }}" class="sr-only">
                                    {{ __('Assigned agent for :name', ['name' => $lead->first_name.' '.$lead->last_name]) }}
                                </x-ui.label>
                                <x-ui.select :native="true" id="lead-agent-{{ $lead->id }}" name="agent_id" class="w-48">
                                    <option value="" @selected($lead->agent_id === null)>{{ __('Unassigned') }}</option>
                                    @if ($lead->agent && ! $assignableAgents->contains('id', $lead->agent_id))
                                        <option value="{{ $lead->agent_id }}" selected disabled>{{ $lead->agent->name }} — {{ __('Unavailable') }}</option>
                                    @endif
                                    @foreach ($assignableAgents as $agent)
                                        <option value="{{ $agent->id }}" @selected($lead->agent_id === $agent->id)>{{ $agent->name }}</option>
                                    @endforeach
                                </x-ui.select>
                                <x-ui.button type="submit" variant="outline" size="sm">{{ __('Save assignment') }}</x-ui.button>
                            </form>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @empty
                    <x-ui.table-row>
                        <x-ui.table-cell colspan="9" class="text-muted-foreground py-6 text-center">{{ __('No leads found.') }}</x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>

        <x-paginator :paginator="$leads" />
    </div>
</x-app-layout>
