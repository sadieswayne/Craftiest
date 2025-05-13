<?php

use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Models\Common\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('menus')) {
            if (app(ExtensionRepositoryInterface::class)->appVersion() == 7.3) {
                Menu::query()
                    ->where('parent_id', null)
                    ->whereHas('children')
                    ->get()->map(function ($item) {
                        $item->children()
                            ->where('parent_id', $item['id'])
                            ->where('custom_menu', false)
                            ->update([
                                'icon' => null,
                            ]);
                    });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
