<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">

            @can('viewAny', 'Dashboard')
                <li>
                    <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                        <i class="material-icons">dashboard</i>
                        <span class="nav-text">{{ __('language.admin.dashboards.name') }}</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('admin.dashboard') }}">{{ __('language.admin.dashboards.name') }}</a></li>
                    </ul>
                </li>
            @endcan

            @can('viewAny', App\Models\System::class)
                <li>
                    <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                        <i class="material-icons">settings</i>
                        <span class="nav-text">{{ __('language.admin.systems.title') }}</span>
                    </a>
                    <ul aria-expanded="false">
                        @can('viewAny', App\Models\System::class)
                            <li>
                                <a class="has-arrow" href="javascript:void(0);"
                                   aria-expanded="false">{{ __('language.admin.systems.name') }}</a>
                                <ul aria-expanded="false">
                                    @can('viewAny', App\Models\System::class)
                                        <li>
                                            <a href="{{ route('admin.systems.index') }}">{{ __('language.admin.systems.listSidebar') }}</a>
                                        </li>
                                        @can('create', App\Models\System::class)
                                            <li>
                                                <a href="{{ route('admin.systems.create') }}">{{ __('language.admin.systems.createSidebar') }}</a>
                                            </li>
                                        @endcan
                                    @endcan
                                </ul>
                            </li>

                            @foreach($systemsByType0 as $key => $system)
                                <li>
                                    <a class="has-arrow" href="javascript:void(0);"
                                       aria-expanded="false">{{ $system->name ?? null }}</a>
                                    <ul aria-expanded="false">
                                        @can('viewAny', App\Models\System::class)
                                            <li>
                                                <a href="{{ route('admin.systems.index') . '?system_id=' . $system->id }}">{{ __('language.admin.systems.listSidebar') }}</a>
                                            </li>
                                            @can('create', App\Models\System::class)
                                                <li>
                                                    <a href="{{ route('admin.systems.create') . '?system_id=' . $system->id }}">{{ __('language.admin.systems.createSidebar') }}</a>
                                                </li>
                                            @endcan
                                        @endcan
                                    </ul>
                                </li>
                            @endforeach
                        @endcan
                    </ul>
                </li>
            @endcan

            @if(auth()->user()->can('viewAny', App\Models\Menu::class) || auth()->user()->can('viewAny', App\Models\Page::class) || auth()->user()->can('viewAny', App\Models\Block::class))
                <li>
                    <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                        <i class="material-icons">extension</i>
                        <span class="nav-text">{{ __('language.admin.interfaces.title') }}</span>
                    </a>
                    <ul aria-expanded="false">
                        @can('viewAny', App\Models\Menu::class)
                            <li>
                                <a href="{{ route('admin.menus.index') }}"
                                   aria-expanded="false">{{ __('language.admin.interfaces.menus.title') }}</a>
                            </li>
                        @endcan
                        @can('viewAny', App\Models\Page::class)
                            <li>
                                <a href="{{ route('admin.pages.index') }}"
                                   aria-expanded="false">{{ __('language.admin.interfaces.pages.title') }}</a>
                            </li>
                        @endcan
                        @can('viewAny', App\Models\Block::class)
                            <li>
                                <a href="{{ route('admin.blocks.index') }}"
                                   aria-expanded="false">{{ __('language.admin.interfaces.blocks.title') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @if(auth()->user()->can('viewAny', App\Models\CategoryPost::class) || auth()->user()->can('viewAny', App\Models\Post::class) || auth()->user()->can('viewAny', App\Models\Tag::class))
                <li>
                    <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                        <i class="material-icons">article</i>
                        <span class="nav-text">{{ __('language.admin.posts.title') }}</span>
                    </a>
                    <ul aria-expanded="false">
                        @can('viewAny', App\Models\CategoryPost::class)
                            <li>
                                <a href="{{ route('admin.categoryPosts.index') }}"
                                   aria-expanded="false">{{ __('language.admin.categories.title') }}</a>
                            </li>
                        @endcan
                        @can('viewAny', App\Models\Post::class)
                            <li>
                                <a href="{{ route('admin.posts.index') }}"
                                   aria-expanded="false">{{ __('language.admin.posts.title') }}</a>
                            </li>
                        @endcan
                        @can('viewAny', App\Models\Tag::class)
                            <li>
                                <a href="{{ route('admin.tags.index') }}"
                                   aria-expanded="false">{{ __('language.admin.tags.title') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            <li>
                <a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
                    <i class="material-icons">movie</i>
                    <span class="nav-text">Quản lý phòng chiếu</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('admin.rooms.index') }}" aria-expanded="false">Phòng chiếu</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.seat-layouts.index') }}" aria-expanded="false">Sơ đồ ghế</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.seat-types.index') }}" aria-expanded="false">Loại ghế</a>
                    </li>
                </ul>
            </li>

            @if(auth()->user()->can('viewAny', App\Models\User::class) || auth()->user()->can('viewAny', App\Models\Role::class) || auth()->user()->can('viewAny', App\Models\Module::class) || auth()->user()->can('viewAny', App\Models\Permission::class))
                <li>
                    <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                        <i class="material-icons">person</i>
                        <span class="nav-text">{{ __('language.admin.members.title') }}</span>
                    </a>
                    <ul aria-expanded="false">
                        @can('viewAny', App\Models\User::class)
                            <li>
                                <a href="{{ route('admin.users.index') }}"
                                   aria-expanded="false">{{ __('language.admin.members.users.title') }}</a>
                            </li>
                        @endcan
                        @can('viewAny', App\Models\Role::class)
                            <li>
                                <a href="{{ route('admin.roles.index') }}"
                                   aria-expanded="false">{{ __('language.admin.members.roles.title') }}</a>
                            </li>
                        @endcan
                        @can('viewAny', App\Models\Module::class)
                            <li>
                                <a href="{{ route('admin.modules.index') }}"
                                   aria-expanded="false">{{ __('language.admin.members.modules.title') }}</a>
                            </li>
                        @endcan
                        @can('viewAny', App\Models\Permission::class)
                            <li>
                                <a href="{{ route('admin.permissions.index') }}"
                                   aria-expanded="false">{{ __('language.admin.members.permissions.title') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</div>
