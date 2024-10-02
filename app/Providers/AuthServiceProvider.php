<?php

namespace App\Providers;

use App\Models\BlockType;
use App\Models\CategoryPost;
use App\Models\Menu;
use App\Models\Module;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Block as BlockModel;
use App\Models\Role;
use App\Models\System;
use App\Models\Tag;
use App\Models\User;
use App\Policies\Admin\Blocks\BlockPolicy;
use App\Policies\Admin\Blocks\BlockTypePolicy;
use App\Policies\Admin\CategoryPosts\CategoryPostPolicy;
use App\Policies\Admin\Dashboards\DashboardPolicy;
use App\Policies\Admin\Menus\MenuPolicy;
use App\Policies\Admin\Modules\ModulePolicy;
use App\Policies\Admin\Pages\PagePolicy;
use App\Policies\Admin\Permissions\PermissionPolicy;
use App\Policies\Admin\Posts\PostPolicy;
use App\Policies\Admin\Roles\RolePolicy;
use App\Policies\Admin\Systems\SystemPolicy;
use App\Policies\Admin\Tags\TagPolicy;
use App\Policies\Admin\Users\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use PhpParser\Node\Stmt\Block;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'Dashboard' => DashboardPolicy::class,
        System::class => SystemPolicy::class,
        Menu::class => MenuPolicy::class,
        Page::class => PagePolicy::class,
        BlockModel::class => BlockPolicy::class,
        BlockType::class => BlockTypePolicy::class,
        CategoryPost::class => CategoryPostPolicy::class,
        Post::class => PostPolicy::class,
        Tag::class => TagPolicy::class,
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Module::class => ModulePolicy::class,
        Permission::class => PermissionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
