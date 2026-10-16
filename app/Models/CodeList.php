<?php

namespace App\Models;

use Database\Factories\CodeListFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $key
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['key', 'name'])]
class CodeList extends Model
{
    /** @use HasFactory<CodeListFactory> */
    use HasFactory;

    /**
     * @return HasMany<CodeListItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CodeListItem::class);
    }

    /**
     * @return HasMany<CodeListItem, $this>
     */
    public function codeListItems(): HasMany
    {
        return $this->items();
    }
}
