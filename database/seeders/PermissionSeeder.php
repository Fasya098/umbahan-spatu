<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role_has_permissions')->delete();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $menuMaster = ['master', 'master-user', 'master-users', 'master-role', 'master-datapesanan', 'master-datalayanan', 'master-referensi-layanan', 'master-toko', 'master-terima', 'master-terima-layanan'];
        $menuMitra = ['mitra', 'mitra-pesanan', 'mitra-layanan', 'mitra-request-layanan',];
        $menuWebsite = ['website', 'setting'];

        $permissionsByRole = [
            'admin' => ['dashboard', ...$menuMaster, ...$menuWebsite],
            'mitra' => ['dashboard', ...$menuMitra],
        ];

        $insertPermissions = fn($role) => collect($permissionsByRole[$role])
            ->map(function ($name) {
                $check = Permission::whereName($name)->first();

                if (!$check) {
                    return Permission::create([
                        'name' => $name,
                        'guard_name' => 'api',
                    ])->id;
                }

                return $check->id;
            })
            ->toArray();

        $permissionIdsByRole = [
            'admin' => $insertPermissions('admin'),
            'mitra' => $insertPermissions('mitra')
        ];

        foreach ($permissionIdsByRole as $role => $permissionIds) {
            $role = Role::whereName($role)->first();

            DB::table('role_has_permissions')
                ->insert(
                    collect($permissionIds)->map(fn($id) => [
                        'role_id' => $role->id,
                        'permission_id' => $id
                    ])->toArray()
                );
        }
    }
}
