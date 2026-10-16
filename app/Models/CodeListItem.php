<?php

namespace App\Models;

use Database\Factories\CodeListItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $code_list_id
 * @property string $code
 * @property string $label
 * @property int $sort_order
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['code_list_id', 'code', 'label', 'sort_order', 'active'])]
class CodeListItem extends Model
{
    /** @use HasFactory<CodeListItemFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<CodeList, $this>
     */
    public function codeList(): BelongsTo
    {
        return $this->belongsTo(CodeList::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
