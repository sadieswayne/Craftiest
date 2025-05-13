<?php

namespace App\Http\Middleware;

use App\Enums\Roles;
use App\Services\Common\MenuService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->isSuperAdmin()) {
            return $next($request);
        }

        if (! Auth::user()?->isAdmin()) {
            return redirect()->route('index');
        }

        $role = Role::findByName(Roles::ADMIN->value);
        $approvedArray = collect($role->getAllPermissions())->pluck('name')->merge(['admin_dashboard']);

        $service = new MenuService;
        $menus = collect($service->generate())->where('is_admin', true);

        $accessibleRoutes = collect();

        foreach ($menus as $menu) {
            if ($approvedArray->contains($menu['key'])) {
                if (is_array($menu['active_condition'])) {
                    $accessibleRoutes = $accessibleRoutes->concat($menu['active_condition']);
                } else {
                    foreach ($menu['children'] as $child) {
                        $accessibleRoutes->push($child['active_condition'] ?? $child['route']);
                    }
                }
            }
        }

        if ($accessibleRoutes->contains(fn ($pattern) => Str::is($pattern, $request->route()->getName()))) {
            return $next($request);
        }

        abort(401);
    }
}
