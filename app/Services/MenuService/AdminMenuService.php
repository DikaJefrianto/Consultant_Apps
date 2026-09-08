<?php
namespace App\Services\MenuService;

use App\Services\MenuService\AdminMenuItem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AdminMenuService
{
    /**
     * @var AdminMenuItem[][]
     */
    protected array $groups = [];

    /**
     * Add a menu item to the admin sidebar.
     *
     * @param AdminMenuItem|array $item The menu item or configuration array
     * @param string|null $group The group to add the item to
     * @return void
     * @throws \InvalidArgumentException
     */
    public function addMenuItem(AdminMenuItem|array $item, ?string $group = null)
    {
        $group = $group ?: __('Main');
        $menuItem = $this->createAdminMenuItem($item);
        if (!isset($this->groups[$group])) {
            $this->groups[$group] = [];
        }

        if ($menuItem->userHasPermission()) {
            $this->groups[$group][] = $menuItem;
        }
    }

    protected function createAdminMenuItem(AdminMenuItem|array $data): AdminMenuItem
    {
        if ($data instanceof AdminMenuItem) {
            return $data;
        }

        $menuItem = new AdminMenuItem();

        if (isset($data['children']) && is_array($data['children'])) {
            $data['children'] = array_map(
                fn($child) => auth()->user()->hasAnyPermission($child['permissions'] ?? [])
                    ? $this->createAdminMenuItem($child)
                    : null,
                $data['children']
            );

            // Filter out null values (items without permission).
            $data['children'] = array_filter($data['children']);
        }

        return $menuItem->setAttributes($data);
    }

    public function getMenu()
    {
        // Home untuk Landing Page di sini ---
        $this->addMenuItem([
            'label' => __('Home'), // Label untuk menu item
            'icon' => 'home.svg',
            'route' => url('home/'),
            'active' => request()->is('/'), // Aktifkan jika sedang di landing page
            'id' => 'home-landing',
            'priority' => 0,
        ]);
        $this->addMenuItem([
            'label' => __('Feedback'),
            'icon' => 'feedback.svg', // Atau ikon lain yang sesuai
            'route' => route('admin.feedbacks.index'),
            'active' => Route::is('admin.feedbacks.*'),
            'id'=> 'feedback',
            'permissions' => ['role.create', 'role.view', 'role.edit', 'role.delete'],
            'priority' => 40, // Sesuaikan prioritas
        ]);
        $this->addMenuItem([
            'label' => __('Laporan'),
            'icon' => 'report.svg', // Atau ikon lain yang sesuai
            'route' => route('admin.laporan.index'),
            'active' => Route::is('admin.laporan.*'),
            'id'=> 'laporan',
            'permissions' => ['report.view', 'report.detail','report.export','report.export_detail'],
            'priority' => 41, // Sesuaikan prioritas
        ]);
        $this->addMenuItem([
            'label' => __('Dashboard'),
            'icon' => 'dashboard.svg',
            'route' => route('admin.dashboard'),
            'active' => Route::is('admin.dashboard'),
            'id' => 'dashboard',
            'priority' => 1,
            'permissions' => 'dashboard.view'
        ]);

        $this->addMenuItem([
            'label' => __('Roles & Permissions'),
            'icon' => 'key.svg',
            'id' => 'roles-submenu',
            'active' => Route::is('admin.roles.*'),
            'priority' => 10,
            'permissions' => ['role.create', 'role.view', 'role.edit', 'role.delete'],
            'children' => [
                [
                    'label' => __('Roles'),
                    'route' => route('admin.roles.index'),
                    'active' => Route::is('admin.roles.index') || Route::is('admin.roles.edit'),
                    'priority' => 10,
                    'permissions' => 'role.view'
                ],
                [
                    'label' => __('New Role'),
                    'route' => route('admin.roles.create'),
                    'active' => Route::is('admin.roles.create'),
                    'priority' => 20,
                    'permissions' => 'role.create'
                ],
                [
                    'label' => __('Permissions'),
                    'route' => route('admin.permissions.index'),
                    'active' => Route::is('admin.permissions.index') || Route::is('admin.permissions.show'),
                    'priority' => 30,
                    'permissions' => 'role.view'
                ]
            ]
        ]);

        $this->addMenuItem([
            'label' => __('User'),
            'icon' => 'user.svg',
            'id' => 'users-submenu',
            'active' => Route::is('admin.users.*'),
            'priority' => 20,
            'permissions' => ['user.create', 'user.view', 'user.edit', 'user.delete'],
            'children' => [
                [
                    'label' => __('Users'),
                    'route' => route('admin.users.index'),
                    'active' => Route::is('admin.users.index') || Route::is('admin.users.edit'),
                    'priority' => 20,
                    'permissions' => 'user.view'
                ],
                [
                    'label' => __('New User'),
                    'route' => route('admin.users.create'),
                    'active' => Route::is('admin.users.create'),
                    'priority' => 10,
                    'permissions' => 'user.create'
                ]
            ]
        ]);
        $this->addMenuItem([
            'label'       => __('Perusahaan'),
            'icon'        => 'perusahaan.svg', // sesuaikan icon jika ada
            'id'          => 'perusahaans-submenu',
            'active'      => Route::is('admin.perusahaans.*'),
            'priority'    => 25,
            'permissions' => ['perusahaan.view', 'perusahaan.create', 'perusahaan.edit', 'perusahaan.delete'],
            'children'    => [
                [
                    'label'       => __('Daftar Perusahaan'),
                    'route'       => route('admin.perusahaans.index'),
                    'active'      => Route::is('admin.perusahaans.index') || Route::is('admin.perusahaans.edit'),
                    'priority'    => 10,
                    'permissions' => 'perusahaan.view',
                ],
                [
                    'label'       => __('Tambah Perusahaan'),
                    'route'       => route('admin.perusahaans.create'),
                    'active'      => Route::is('admin.perusahaans.create'),
                    'priority'    => 20,
                    'permissions' => 'perusahaan.create',
                ],
            ],
        ]);
        $this->addMenuItem([
            'label'       => __('Karyawan'),
            'icon'        => 'employe.svg', // sesuaikan icon jika ada
            'id'          => 'karyawans-submenu',
            'active'      => Route::is('admin.karyawans.*'),
            'priority'    => 25,
            'permissions' => ['karyawan.view', 'karyawan.create', 'karyawan.edit', 'karyawan.delete'],
            'children'    => [
                [
                    'label'       => __('Daftar Karyawan'),
                    'route'       => route('admin.karyawans.index'),
                    'active'      => Route::is('admin.karyawans.index') || Route::is('admin.karyawans.edit'),
                    'priority'    => 10,
                    'permissions' => 'karyawan.view',
                ],
                [
                    'label'       => __('Tambah Karyawan'),
                    'route'       => route('admin.karyawans.create'),
                    'active'      => Route::is('admin.karyawans.create'),
                    'priority'    => 20,
                    'permissions' => 'karyawan.create',
                ],
            ],
        ]);
        $this->addMenuItem([
            'label' => __('Perhitungan'),
            'icon' => 'calculator.svg', // ganti dengan ikon bahan bakar jika tersedia
            'id' => 'HasilPerhitungan-submenu',
            'active' => Route::is('admin.perhitungan.*'),
            'priority' => 30, // bisa disesuaikan posisi menu
            'permissions' => ['perhitungan.view', 'perhitungan.create', 'perhitungan.edit', 'perhitungan.delete'],
            'children' => [
                [
                    'label' => __('Perhitungan Emisi'),
                    'route' => route('admin.perhitungan.create'),
                    'active' => Route::is('admin.perhitungan.create'),
                    'priority' => 20,
                    'permissions' => 'perhitungan.create'
                ],
                [
                    'label' => __('Daftar Perhitungan Emisi'),
                    'route' => route('admin.perhitungan.index'),
                    'active' => Route::is('admin.perhitungan.index') || Route::is('admin.perhitungan.edit'),
                    'priority' => 10,
                    'permissions' => 'perhitungan.view'
                ],
            ]
        ]);
        $this->addMenuItem([
            'label' => __('Bahan Bakar'),
            'icon' => 'gas.svg', // ganti dengan ikon bahan bakar jika tersedia
            'id' => 'bahan-bakars-submenu',
            'active' => Route::is('admin.bahan-bakar.*'),
            'priority' => 30, // bisa disesuaikan posisi menu
            'permissions' => ['bahanbakar.view', 'bahanbakar.create', 'bahanbakar.edit', 'bahanbakar.delete'],
            'children' => [
                [
                    'label' => __('Daftar Emisi Bahan Bakar'),
                    'route' => route('admin.bahan-bakar.index'),
                    'active' => Route::is('admin.bahan-bakar.index') || Route::is('admin.bahan-bakar.edit'),
                    'priority' => 10,
                    'permissions' => 'bahanbakar.view'
                ],
                [
                    'label' => __('Tambah Data Bahan Bakar'),
                    'route' => route('admin.bahan-bakar.create'),
                    'active' => Route::is('admin.bahan-bakar.create'),
                    'priority' => 20,
                    'permissions' => 'bahanbakar.create'
                ],
            ]
        ]);
        $this->addMenuItem([
            'label' => __('Transportasi'),
            'icon' => 'bus.svg', // ganti dengan ikon transportasi jika ada (misalnya: car.svg, truck.svg, dll)
            'id' => 'transportasi-submenu',
            'active' => Route::is('admin.transportasi.*'),
            'priority' => 31, // sesuaikan agar muncul di urutan yang diinginkan
            'permissions' => ['transportasi.view', 'transportasi.create', 'transportasi.edit', 'transportasi.delete'],
            'children' => [
                [
                    'label' => __('Daftar Emisi Transportasi'),
                    'route' => route('admin.transportasi.index'),
                    'active' => Route::is('admin.transportasi.index') || Route::is('admin.transportasi.edit'),
                    'priority' => 10,
                    'permissions' => 'transportasi.view'
                ],
                [
                    'label' => __('Tambah Data Transportasi'),
                    'route' => route('admin.transportasi.create'),
                    'active' => Route::is('admin.transportasi.create'),
                    'priority' => 20,
                    'permissions' => 'transportasi.create'
                ],
            ]
        ]);

        $this->addMenuItem([
            'label' => __('Biaya'),
            'icon' => 'uang.svg', // ganti dengan ikon transportasi jika ada (misalnya: car.svg, truck.svg, dll)
            'id' => 'biaya-submenu',
            'active' => Route::is('admin.biaya.*'),
            'priority' => 31, // sesuaikan agar muncul di urutan yang diinginkan
            'permissions' => ['biaya.view', 'biaya.create', 'biaya.edit', 'biaya.delete'],
            'children' => [
                [
                    'label' => __('Daftar Emisi Biaya'),
                    'route' => route('admin.biaya.index'),
                    'active' => Route::is('admin.biaya.index') || Route::is('admin.biaya.edit'),
                    'priority' => 10,
                    'permissions' => 'biaya.view'
                ],
                [
                    'label' => __('Tambah Data Emisi Biaya'),
                    'route' => route('admin.biaya.create'),
                    'active' => Route::is('admin.biaya.create'),
                    'priority' => 20,
                    'permissions' => 'biaya.create'
                ],
            ]
        ]);
        $this->addMenuItem([
            'label'       => __('Konsultasi'),
            'icon'        => 'consultation.svg', // sesuaikan icon jika ada
            'id'          => 'konsultasi-submenu',
            'active'      => Route::is('admin.konsultasi.*'),
            'priority'    => 35  ,
            'permissions' => ['konsultasi.view', 'konsultasi.create', 'konsultasi.edit', 'konsultasi.delete'],
            'children'    => [
                [
                    'label'       => __('Riwayat Konsultasi'),
                    'route'       => route('admin.konsultasis.index'),
                    'active'      => Route::is('admin.konsultasis.index') || Route::is('admin.konsultasis.edit'),
                    'priority'    => 10,
                    'permissions' => 'konsultasi.view',
                ],
                [
                    'label'       => __('Ajukan Konsultasi'),
                    'route'       => route('admin.konsultasis.create'),
                    'active'      => Route::is('admin.konsultasis.create'),
                    'priority'    => 20,
                    'permissions' => 'konsultasi.create',
                ],
            ],
        ]);
        $this->addMenuItem([
            'label'       => __('Strategi'),
            'icon'        => 'strategy.svg', // sesuaikan icon jika ada
            'id'          => 'strategis-submenu',
            'active'      => Route::is('admin.strategis.*'),
            'priority'    => 31,
            'permissions' => ['strategi.view', 'strategi.create', 'strategi.edit', 'strategi.delete'],
            'children'    => [
                [
                    'label'       => __('Daftar Strategi'),
                    'route'       => route('admin.strategis.index'),
                    'active'      => Route::is('admin.strategis.index') || Route::is('admin.strategis.edit'),
                    'priority'    => 10,
                    'permissions' => 'strategi.view',
                ],
                [
                    'label'       => __('Tambah Strategi'),
                    'route'       => route('admin.strategis.create'),
                    'active'      => Route::is('admin.strategis.create'),
                    'priority'    => 20,
                    'permissions' => 'strategi.create',
                ],
            ],
        ]);
        $this->addMenuItem([
            'label' => __('Panduan'), // Label baru untuk admin
            'icon' => 'panduan.svg', // Ikon yang lebih sesuai untuk manajemen
            'id' => 'guides-management-submenu', // ID baru untuk menu manajemen
            'active' => Route::is('admin.guides.*'), // Aktif jika di rute admin.guides.*
            'priority' => 35, // Prioritas agar muncul di dekat menu admin lainnya
            'permissions' => ['guide.create', 'guide.view', 'guide.edit', 'guide.delete'], // Asumsi permission yang relevan
            'children' => [
                [
                    'label' => __('Kelola Panduan'),
                    'route' => route('admin.guides.index'),
                    'active' => Route::is('admin.guides.index') || Route::is('admin.guides.edit'),
                    'priority' => 10,
                    'permissions' => 'guide.view'
                ],

            ]
        ]);

        $this->addMenuItem([
            'label' => __('Monitoring'),
            'icon' => 'tv.svg',
            'id' => 'monitoring-submenu',
            'active' => Route::is('admin.actionlog.*'),
            'priority' => 42,
            'permissions' => ['pulse.view', 'actionlog.view'],
            'children' => [
                [
                    'label' => __('Action Logs'),
                    'route' => route('admin.actionlog.index'),
                    'active' => Route::is('admin.actionlog.index'),
                    'priority' => 20,
                    'permissions' => 'actionlog.view'
                ],
                [
                    'label' => __('Laravel Pulse'),
                    'route' => route('pulse'),
                    'active' => false,
                    'target' => '_blank',
                    'priority' => 10,
                    'permissions' => 'pulse.view'
                ]
            ]
        ]);



        $this->addMenuItem([
            'label' => __('Settings'),
            'icon' => 'settings.svg',
            'id' => 'settings-submenu',
            'active' => Route::is('admin.settings.*') || Route::is('admin.translations.*'),
            'priority' => 1,
            'permissions' => ['settings.edit', 'translations.view'],
            'children' => [
                [
                    'label' => __('General Settings'),
                    'route' => route('admin.settings.index'),
                    'active' => Route::is('admin.settings.index'),
                    'priority' => 20,
                    'permissions' => 'settings.edit'
                ],
                [
                    'label' => __('Translations'),
                    'route' => route('admin.translations.index'),
                    'active' => Route::is('admin.translations.*'),
                    'priority' => 10,
                    'permissions' => ['translations.view', 'translations.edit']
                ]
            ]
        ], __('More'));

        $this->addMenuItem([
            'label' => __('Logout'),
            'icon' => 'logout.svg',
            'route' => route('admin.dashboard'),
            'active' => false,
            'id' => 'logout',
            'priority' => 1,
            'html' => '
                <li class="hover:menu-item-active">
                    <form method="POST" action="' . route('logout') . '">
                        ' . csrf_field() . '
                        <button type="submit" class="menu-item group w-full text-left menu-item-inactive text-black dark:text-white hover:text-black">
                            <img src="' . asset('images/icons/logout.svg') . '" alt="Logout" class="menu-item-icon dark:invert">
                            <span class="menu-item-text">' . __('Logout') . '</span>
                        </button>
                    </form>
                </li>
            '
        ], __('More'));

        $this->sortMenuItemsByPriority();
        return $this->applyFiltersToMenuItems();
    }

    protected function sortMenuItemsByPriority(): void
    {
        foreach ($this->groups as &$groupItems) {
            usort($groupItems, function ($a, $b) {
                return $a->priority <=> $b->priority;
            });
        }
    }

    protected function applyFiltersToMenuItems(): array
    {
        $result = [];
        foreach ($this->groups as $group => $items) {
            // Filter items by permission.
            $filteredItems = array_filter($items, function (AdminMenuItem $item) {
                return $item->userHasPermission();
            });

            // Apply filters that might add/modify menu items.
            $filteredItems = ld_apply_filters('sidebar_menu_' . strtolower($group), $filteredItems);

            // Only add the group if it has items after filtering.
            if (!empty($filteredItems)) {
                $result[$group] = $filteredItems;
            }
        }

        return $result;
    }

    public function shouldExpandSubmenu(AdminMenuItem $menuItem): bool
    {
        // If the parent menu item is active, expand the submenu.
        if ($menuItem->active) {
            return true;
        }

        // Check if any child menu item is active.
        foreach ($menuItem->children as $child) {
            if ($child->active) {
                return true;
            }
        }

        return false;
    }

    public function render(array $groupItems): string
    {
        $html = '';
        foreach ($groupItems as $menuItem) {
            $filterKey = $menuItem->id ?? Str::slug($menuItem->label) ?? '';
            $html .= ld_apply_filters('sidebar_menu_before_' . $filterKey, '');

            $html .= view('backend.layouts.partials.menu-item', [
                'item' => $menuItem,
            ])->render();

            $html .= ld_apply_filters('sidebar_menu_after_' . $filterKey, '');
        }

        return $html;
    }
}
