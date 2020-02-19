<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
//use App\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //Rol de Administrador
        $role = Role::create(['name' => 'admin', 'description' => 'Administrator']);
        Permission::create(['name' => 'admin_roles_create']);
        Permission::create(['name' => 'admin_roles_edit']);
        Permission::create(['name' => 'admin_roles_show']);
        Permission::create(['name' => 'admin_roles_destroy']);
        //$role->givePermissionTo('admin_roles_agregar');

        $role->givePermissionTo([
            'admin_roles_create',
            'admin_roles_edit',
            'admin_roles_show',
            'admin_roles_destroy'
        ]);


        //Rol de Administrador de Almacén   
        $role = Role::create(['name' => 'almacen_admin', 'description' => 'Almacen_admin']);
        Permission::create(['name' => 'almacen_admin_create']);
        Permission::create(['name' => 'almacen_admin_edit']);
        Permission::create(['name' => 'almacen_admin_show']);
        Permission::create(['name' => 'almacen_admin_delete']);
        //$role->givePermissionTo('admin_roles_ver');

        $role->givePermissionTo([
            'almacen_admin_create',
            'almacen_admin_edit',
            'almacen_admin_show',
            'almacen_admin_delete'
        ]);

        //Rol de Capturista de Almacén
        $role = Role::create(['name' => 'almacen_capturista', 'description' => 'Almacen_capturista']);
        Permission::create(['name' => 'almacen_capturista_create']);
        Permission::create(['name' => 'almacen_capturista_edit']);
        Permission::create(['name' => 'almacen_capturista_show']);
        //Permission::create(['name' => 'almacen_capturista_roles_destroy']);
        //$role->givePermissionTo('admin_roles_ver');

        $role->givePermissionTo([
            'almacen_capturista_create',
            'almacen_capturista_edit',
            'almacen_capturista_show'//,
            //'almacen_admin_destroy'
        ]);

        //Rol de Oficinista de Almacén    
        $role = Role::create(['name' => 'almacen_oficinista', 'description' => 'Almacen_oficinista']);
        Permission::create(['name' => 'almacen_oficinista_create']);
        //Permission::create(['name' => 'almacen_oficinista_roles_edit']);
        Permission::create(['name' => 'almacen_oficinista_show']);
        //Permission::create(['name' => 'almacen_oficinista_roles_destroy']);
        //$role->givePermissionTo('admin_roles_ver');

        $role->givePermissionTo([
            'almacen_oficinista_create',
            //'almacen_oficinista_edit',
            'almacen_oficinista_show'//,
            //'almacen_oficinista_destroy'

        ]);

        

    }
}


