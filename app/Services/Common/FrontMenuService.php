<?php

namespace App\Services\Common;

use App\Extensions\MegaMenu\System\Models\MegaMenu;
use App\Helpers\Classes\MarketplaceHelper;
use App\Models\Setting;
use Illuminate\Support\Arr;

class FrontMenuService
{
    public function generate(): array
    {

        $menus = $this->menuSetting();

        if (MarketplaceHelper::isRegistered('mega-menu')) {
            $megaMenus = $this->getMegaMenu($menus);

            foreach ($menus as $key => $menu) {

                if (isset($menu['mega_menu_id']) && $menu['mega_menu_id']) {
                    $megaMenu = Arr::first($megaMenus, function ($value) use ($menu) {
                        return $value['id'] == $menu['mega_menu_id'];
                    });

                    if ($megaMenu) {
                        $menus[$key]['mega_menu'] = [
                            'id'                => $megaMenu['id'],
                            'name'              => $megaMenu['name'],
                            'number_of_columns' => $megaMenu['number_of_columns'],
                            'items'             => $megaMenu['active_items'],
                        ];
                    }
                }
            }
        }

        return $menus;
    }

    public function getMegaMenu(array $menus)
    {
        $megaMenus = array_filter($menus, function ($menu) {
            return isset($menu['mega_menu_id']) && (bool) $menu['mega_menu_id'];
        });

        $pluckMegaMenuIds = Arr::pluck($megaMenus, 'mega_menu_id');

        return MegaMenu::query()
            ->select('id', 'name', 'number_of_columns')
            ->where('status', 1)
            ->with([
                'activeItems:id,mega_menu_id,parent_id,label,description,type,icon,link,space',
                'activeItems.activeChildren:id,mega_menu_id,parent_id,label,description,type,icon,link,space',

            ])

            ->whereIn('id', $pluckMegaMenuIds)
            ->get()->toArray();
    }

    public function menuSetting(): array
    {
        $menus = Setting::getCache()->menu_options;

        $frontMenus = $menus ?: '[{"title": "Home","url": "#banner","target": false, "mega_menu_id": ""},{"title": "Features","url": "#features","target": false, "mega_menu_id": ""},{"title": "How it Works","url": "#how-it-works","target": false, "mega_menu_id": ""},{"title": "Testimonials","url": "#testimonials","target": false, "mega_menu_id": ""},{"title": "Pricing","url": "#pricing","target": false, "mega_menu_id": ""},{"title": "FAQ","url": "#faq","target": false, "mega_menu_id": ""}]';

        return json_decode($frontMenus, true, 512, JSON_THROW_ON_ERROR);
    }
}
