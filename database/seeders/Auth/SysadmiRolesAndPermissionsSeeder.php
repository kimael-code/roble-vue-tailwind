<?php

namespace Database\Seeders\Auth;

use App\Models\Security\Permission;
use App\Models\Security\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SysadmiRolesAndPermissionsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reiniciar caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // roles básicos
        Role::create([
            'id' => 0,
            'name' => __('Superuser'),
            'description' => __('has access to any system route and can perform any action that does not violate system stability'),
        ]);
        $sysadminRole = Role::create([
            'id' => 1,
            'name' => __('Systems Administrator'),
            'description' => __('manages data related to system security and monitoring'),
        ]);

        // Permisos para gestionar los entes (organizaciones o compañías)
        Permission::create(['name' => 'create new organizations', 'description' => __('create new organizations')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read any organization', 'description' => __('read any organization'), 'set_menu' => true,])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read organization', 'description' => __('read organization')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'update organizations', 'description' => __('update organizations')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'delete organizations', 'description' => __('delete organizations')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'export organizations', 'description' => __('export organizations')])->assignRole([$sysadminRole,]);
        // Permisos para gestionar las unidades administrativas del ente
        Permission::create(['name' => 'create new organizational units', 'description' => __('create new organizational units')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read any organizational unit', 'description' => __('read any organizational unit'), 'set_menu' => true])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read organizational unit', 'description' => __('read organizational unit')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'update organizational units', 'description' => __('update organizational units')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'delete organizational units', 'description' => __('delete organizational units')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'export organizational units', 'description' => __('export organizational units')])->assignRole([$sysadminRole,]);
        // permisos para gestionar los roles
        Permission::create(['name' => 'create new roles', 'description' => __('create new roles')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read any role', 'description' => __('read any role'), 'set_menu' => true])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read role', 'description' => __('read role')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'update roles', 'description' => __('update roles')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'delete roles', 'description' => __('eliminar cualquier rol')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'export roles', 'description' => __('exportar datos de roles a archivo')])->assignRole([$sysadminRole,]);
        // permisos para gestionar los permisos
        Permission::create(['name' => 'create new permissions', 'description' => __('crear nuevos permisos')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read any permission', 'description' => __('read any permission'), 'set_menu' => true])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read permission', 'description' => __('read permission')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'update permissions', 'description' => __('update permissions')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'delete permissions', 'description' => __('delete permissions')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'export permissions', 'description' => __('export permissions')])->assignRole([$sysadminRole,]);
        // permisos para gestionar los usuarios
        Permission::create(['name' => 'create new users', 'description' => __('create new users')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read any user', 'description' => __('read any user'), 'set_menu' => true])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read user', 'description' => __('read user')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'update users', 'description' => __('update users')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'delete users', 'description' => __('delete users')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'force delete users', 'description' => __('force delete users')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'export users', 'description' => __('export users')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'restore users', 'description' => __('restore users')])->assignRole([$sysadminRole,]);
        // permisos para gestionar los logs del sistema
        Permission::create(['name' => 'read any system log', 'description' => __('read any system log'), 'set_menu' => true])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read system log', 'description' => __('read system log')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'delete system logs', 'description' => __('delete system logs')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'export system logs', 'description' => __('export system logs')])->assignRole([$sysadminRole,]);
        // permisos para gestionar las trazas de los usuarios
        Permission::create(['name' => 'read any activity trace', 'description' => __('read any activity trace'), 'set_menu' => true])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'read activity trace', 'description' => __('read activity trace')])->assignRole([$sysadminRole,]);
        Permission::create(['name' => 'export activity traces', 'description' => __('export activity traces')])->assignRole([$sysadminRole,]);
        // permisos para gestionar los 'dashboards' del sistema
        Permission::create(['name' => 'read sysadmin dashboard', 'description' => __('read sysadmin dashboard')])->assignRole([$sysadminRole,]);
    }
}
