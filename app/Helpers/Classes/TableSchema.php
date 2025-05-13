<?php

namespace App\Helpers\Classes;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class TableSchema
{
    public static function hasTable(string $table, array $tables): bool
    {
        return in_array($table, $tables, true);
    }

    public function allTables(): array
    {
        return once(static function () {
            return Arr::pluck(Schema::getTables(), 'name');
        });
    }
}
