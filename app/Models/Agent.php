<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\AgentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'slug',
    'photo',
    'email',
    'phone',
    'bio',
    'bio_es',
    'agent_order',
    'is_active',
    'user_id',
])]
class Agent extends Model
{
    /** @use HasFactory<AgentFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'agent_order' => 'integer',
        ];
    }

    /**
     * User account linked to this agent
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Leads assigned to this agent
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Only agents with an enabled agent-role login can receive new lead assignments.
     */
    #[Scope]
    protected function eligibleForLeadAssignment(Builder $query): void
    {
        $query->whereHas('user', fn (Builder $users) => $users
            ->where('role', UserRole::AGENT)
            ->where('is_enabled', true));
    }

    /**
     * Properties linked to this agent
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class);
    }

    /**
     * Build a unique slug from the given name, appending a numeric suffix on collision.
     */
    public static function uniqueSlugFor(string $name): string
    {
        $base = Str::slug($name) ?: 'agent';
        $slug = $base;
        $suffix = 2;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
