<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $masterDataEntities = ['Category', 'Product', 'Unit', 'Supplier', 'Customer'];
        $inventoryEntities = ['PurchaseOrder', 'GoodsReceipt', 'StockTransfer', 'StockAdjustment', 'StockMovement'];

        $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

        $ownerPermissions = [];
        $staffPermissions = [];

        foreach ($masterDataEntities as $entity) {
            $ownerPermissions = array_merge($ownerPermissions, [
                "ViewAny:{$entity}",
                "View:{$entity}",
                "Create:{$entity}",
                "Update:{$entity}",
                "Delete:{$entity}",
                "DeleteAny:{$entity}",
            ]);

            $staffPermissions = array_merge($staffPermissions, [
                "ViewAny:{$entity}",
                "View:{$entity}",
            ]);
        }

        foreach ($inventoryEntities as $entity) {
            $ownerPermissions = array_merge($ownerPermissions, [
                "ViewAny:{$entity}",
                "View:{$entity}",
                "Create:{$entity}",
                "Update:{$entity}",
                "Delete:{$entity}",
                "DeleteAny:{$entity}",
            ]);

            $staffPermissions = array_merge($staffPermissions, [
                "ViewAny:{$entity}",
                "View:{$entity}",
            ]);
        }

        $ownerRole->syncPermissions(Permission::whereIn('name', $ownerPermissions)->get());
        $staffRole->syncPermissions(Permission::whereIn('name', $staffPermissions)->get());

        $this->command->info('Permissions assigned:');
        $this->command->info('  owner: '.$ownerRole->permissions()->count().' permissions');
        $this->command->info('  staff: '.$staffRole->permissions()->count().' permissions');
    }
}
