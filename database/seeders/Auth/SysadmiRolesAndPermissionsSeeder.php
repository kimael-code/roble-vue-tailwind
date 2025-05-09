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

        // Permisos para gestionar el ente y sus unidades administrativas
        $permissionsForOrganizationsManagement = [
            Permission::create(['name' => 'create organizations', 'description' => __('crear nuevos entes')]),
            Permission::create(['name' => 'read any organization', 'description' => __('ver listado de entes'), 'set_menu' => true,]),
            Permission::create(['name' => 'read organization'    , 'description' => __('ver detalles de un ente')]),
            Permission::create(['name' => 'update organizations', 'description' => __('editar cualquier ente')]),
            Permission::create(['name' => 'delete organizations', 'description' => __('eliminar cualquier ente')]),
            Permission::create(['name' => 'export organizations', 'description' => __('exportar datos de entes a archivo')]),
        ];
        $permissionsForOrganizationalUnitsManagement = [
            Permission::create(['name' => 'create admin units' , 'description' => __('crear nuevas unidades administrativas')]),
            Permission::create(['name' => 'read any admin unit', 'description' => __('ver listado de unidades administrativas'), 'set_menu' => true]),
            Permission::create(['name' => 'read admin unit'    , 'description' => __('ver detalles de una unidad administrativa')]),
            Permission::create(['name' => 'update admin units' , 'description' => __('editar cualquier unidad administrativa')]),
            Permission::create(['name' => 'delete admin units' , 'description' => __('eliminar cualquier unidad administrativa')]),
            Permission::create(['name' => 'export admin units' , 'description' => __('exportar datos de unidades administrativas a archivo')]),
        ];

        // permisos de administrador de sistemas
        $permissionsForRolesManagement = [
            Permission::create(['name' => 'create roles' , 'description' => __('crear nuevos roles')]),
            Permission::create(['name' => 'read any role', 'description' => __('ver listado de roles'), 'set_menu' => true]),
            Permission::create(['name' => 'read role'    , 'description' => __('ver detalles de un rol')]),
            Permission::create(['name' => 'update roles' , 'description' => __('editar cualquier rol')]),
            Permission::create(['name' => 'delete roles' , 'description' => __('eliminar cualquier rol')]),
            Permission::create(['name' => 'export roles' , 'description' => __('exportar datos de roles a archivo')]),
        ];
        $permissionsForPermissionsManagement = [
            Permission::create(['name' => 'create permissions' , 'description' => __('crear nuevos permisos')]),
            Permission::create(['name' => 'read any permission', 'description' => __('ver listado de permisos'), 'set_menu' => true]),
            Permission::create(['name' => 'read permission'    , 'description' => __('ver detalles de un permiso')]),
            Permission::create(['name' => 'update permissions' , 'description' => __('editar cualquier permiso')]),
            Permission::create(['name' => 'delete permissions' , 'description' => __('eliminar cualquier permiso')]),
            Permission::create(['name' => 'export permissions' , 'description' => __('exportar datos de permisos a archivo')]),
        ];
        $permissionsForUsersManagement = [
            Permission::create(['name' => 'create users'      , 'description' => __('crear nuevos usuarios')]),
            Permission::create(['name' => 'read any user'     , 'description' => __('ver listado de usuarios'), 'set_menu' => true]),
            Permission::create(['name' => 'read user'         , 'description' => __('ver detalles de un usuario')]),
            Permission::create(['name' => 'update users'      , 'description' => __('editar cualquier usuario')]),
            Permission::create(['name' => 'delete users'      , 'description' => __('eliminar cualquier usuario')]),
            Permission::create(['name' => 'force delete users', 'description' => __('eliminar permanentemente cualquier usuario')]),
            Permission::create(['name' => 'export users'      , 'description' => __('exportar datos de usuarios a archivo')]),
            Permission::create(['name' => 'restore users'     , 'description' => __('restaurar cualquier usuario')]),
        ];
        $permissionsForSystemLogsManagement = [
            Permission::create(['name' => 'read any system log', 'description' => __('ver listado de registros de depuración'), 'set_menu' => true]),
            Permission::create(['name' => 'read system log'    , 'description' => __('ver detalles de un registro de depuración')]),
            Permission::create(['name' => 'delete system logs' , 'description' => __('eliminar los registros de depuración')]),
            Permission::create(['name' => 'export system logs' , 'description' => __('exportar registros de depuración a archivo')]),
        ];
        $permissionsForTracesManagement = [
            Permission::create(['name' => 'read any activity trace', 'description' => __('ver listado de trazas de usuarios'), 'set_menu' => true]),
            Permission::create(['name' => 'read activity trace'    , 'description' => __('ver detalles de un traza de usuario')]),
            Permission::create(['name' => 'export activity traces' , 'description' => __('exportar trazas de usuarios a archivo')]),
        ];
        $permissionForSysadminDashboard= [Permission::create(['name' => 'read sysadmin dashboard', 'description' => __('ver tablero de administrador de sistema')])];

        // creación de roles y asignación de permisos
        $sisadminRole = Role::create([
            'name'        => 'Administrador de Sistemas',
            'description' => __('Gestiona aspectos relacionadas con la seguridad y monitoreo del sistema'),
        ]);

        $sisadminRole->givePermissionTo([
            $permissionsForPermissionsManagement,
            $permissionsForRolesManagement,
            $permissionsForUsersManagement,
            $permissionsForSystemLogsManagement,
            $permissionsForTracesManagement,
            $permissionForSysadminDashboard,
            $permissionsForOrganizationsManagement,
            $permissionsForOrganizationalUnitsManagement,
        ]);
    }
}
