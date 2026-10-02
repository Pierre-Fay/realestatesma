<x-app-layout>
    <x-slot name="header">{{ __('Edit lead') }}</x-slot>

    <div class="flex max-w-2xl flex-col gap-4">
        <x-ui.card>
            <div class="flex flex-col gap-4">
                <div>
                    <h2 class="text-lg font-semibold">{{ $lead->first_name }} {{ $lead->last_name }}</h2>
                    <p class="text-muted-foreground text-sm">{{ $lead->email }}</p>
                </div>

                <form method="POST" action="{{ route('leads.update', $lead) }}" class="flex flex-col gap-4">
                    @csrf
                    @method('PUT')

                    <x-ui.field>
                        <x-ui.field-label for="interested_in">{{ __('Interested in') }}</x-ui.field-label>
                        <x-ui.textarea id="interested_in" name="interested_in" rows="2" required>{{ old('interested_in', $lead->interested_in) }}</x-ui.textarea>
                        <x-ui.field-error>{{ $errors->first('interested_in') }}</x-ui.field-error>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field-label for="budget">{{ __('Budget (USD)') }}</x-ui.field-label>
                        <x-ui.input id="budget" name="budget" type="number" min="0" step="1000" :value="old('budget', $lead->budget)" />
                        <x-ui.field-error>{{ $errors->first('budget') }}</x-ui.field-error>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field-label for="notes">{{ __('Notes') }}</x-ui.field-label>
                        <x-ui.textarea id="notes" name="notes" rows="5">{{ old('notes', $lead->notes) }}</x-ui.textarea>
                        <x-ui.field-error>{{ $errors->first('notes') }}</x-ui.field-error>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field-label for="status">{{ __('Status') }}</x-ui.field-label>
                        <x-ui.select :native="true" id="status" name="status" :value="old('status', $lead->status->value)" :options="$statusOptions" />
                        <x-ui.field-error>{{ $errors->first('status') }}</x-ui.field-error>
                    </x-ui.field>

                    <div class="flex items-center gap-2">
                        <x-ui.button type="submit">{{ __('Save') }}</x-ui.button>
                        <x-ui.button :href="route('leads.index')" variant="ghost">{{ __('Cancel') }}</x-ui.button>
                    </div>
                </form>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>{{ __('Reassign lead') }}</x-ui.card-title>
                <x-ui.card-description>{{ __('Transfer this lead to another agent. You will no longer be able to view or edit it.') }}</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                @if ($assignableAgents->isNotEmpty())
                    <form method="POST" action="{{ route('leads.assign', $lead) }}" class="flex flex-col gap-4">
                        @csrf
                        @method('PATCH')
                        <x-ui.field>
                            <x-ui.field-label for="reassign-agent">{{ __('New agent') }}</x-ui.field-label>
                            <x-ui.select :native="true" id="reassign-agent" name="agent_id" required>
                                <option value="" disabled @selected(! old('agent_id'))>{{ __('Select an agent') }}</option>
                                @foreach ($assignableAgents as $agent)
                                    <option value="{{ $agent->id }}" @selected((string) old('agent_id') === (string) $agent->id)>{{ $agent->name }}</option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.field-error>{{ $errors->first('agent_id') }}</x-ui.field-error>
                        </x-ui.field>
                        <x-ui.button type="submit" variant="outline">{{ __('Reassign lead') }}</x-ui.button>
                    </form>
                @else
                    <p class="text-muted-foreground text-sm">{{ __('No other enabled agent accounts are available.') }}</p>
                    <x-ui.field-error>{{ $errors->first('agent_id') }}</x-ui.field-error>
                @endif
            </x-ui.card-content>
        </x-ui.card>
    </div>
</x-app-layout>
