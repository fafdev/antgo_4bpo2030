<?php

namespace App\Services;

use App\Models\CodeList;
use Illuminate\Support\Facades\Cache;

class CodeListService
{
    public function get(string $key): array
    {
        return Cache::rememberForever("codelist:$key", function () use ($key) {
            return CodeList::where('key', $key)->firstOrFail()
                ->items()->where('active', true)
                ->pluck('label', 'code')
                ->toArray();
        });
    }
}
