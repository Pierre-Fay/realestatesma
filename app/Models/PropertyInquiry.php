<?php

namespace App\Models;

use Database\Factories\PropertyInquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'property_id',
    'name',
    'email',
    'phone',
    'message',
])]
class PropertyInquiry extends Model
{
    /** @use HasFactory<PropertyInquiryFactory> */
    use HasFactory;

    /**
     * Property this inquiry is about
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
