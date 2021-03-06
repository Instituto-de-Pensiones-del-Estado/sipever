<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Role extends Model
{
    protected $appends = [ 'ids_permisos'];

    public function users()
    {
        return $this->belongsToMany('App\User', 'role_user', 'role_id', 'user_id');
    }

    public function permisions()
    {
        return $this->belongsToMany('Spatie\Permission\Models\Permission', 'role_has_permissions', 'role_id', 'permission_id');
    }

    /*public function getNameAttribute()
    {
        return $this->name;
		
    }*/

    public function getIdsPermisosAttribute($value)
    {
       // dd($this->permisions->pluck('id'));
     return  $this->permisions->pluck('id');
    }

    


    protected $attributes = ['guard_name' => 'web'];
}
