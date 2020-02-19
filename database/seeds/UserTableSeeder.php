<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        $role_admin = Role::where('name', 'admin')->first();
        $role_almacen_admin = Role::where('name', 'almacen_admin')->first();
        $role_almacen_capturista = Role::where('name', 'almacen_capturista')->first();
        $role_almacen_oficinista = Role::where('name', 'almacen_oficinista')->first();
      

        //Administrador
        $user = new User();
        $user->name = 'KLUNA';
        $user->username = 'KLUNA';
        $user->email = 'kluna@ipe.com';
        $user->password = bcrypt('1234567');
        $user->id_empleado = 3;
        $user->save();
        $user->roles()->attach($role_admin);

        //Administrador de Almacén
        $user = new User();
        $user->name = 'SCASTILLO';
        $user->username = 'SCASTILLO';
        $user->email = 'scastillo@ipe.com';
        $user->password = bcrypt('1234567');
        $user->id_empleado = 1;
        $user->save();
        $user->roles()->attach($role_almacen_admin);

        //Capturista de Almacén
        $user = new User();
        $user->name = 'VLOPEZ';
        $user->username = 'VLOPEZ';
        $user->email = 'vlopez@ipe.com';
        $user->password = bcrypt('1234567');
        $user->id_empleado = 6;
        $user->save();
        $user->roles()->attach($role_almacen_capturista);

        //Oficinista de Almacén
        $user = new User();
        $user->name = 'ABALBOA';
        $user->username = 'ABALBOA';
        $user->email = 'abalboa@ipe.com';
        $user->password = bcrypt('1234567');
        $user->id_empleado = 5;
        $user->save();
        $user->roles()->attach($role_almacen_oficinista);


    }
}

