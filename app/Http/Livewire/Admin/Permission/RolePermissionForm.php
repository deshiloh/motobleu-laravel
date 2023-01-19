<?php

namespace App\Http\Livewire\Admin\Permission;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionForm extends Component
{
    public ?int $selectedRole = null;
    public ?Role $currentRole;
    public $roles;
    public array $rolePermissions = [];
    public array $permissionsCat = [
        'reservation',
        'address reservation',
        'passenger',
        'facture',
        'user'
    ];
    public array $permissionsForm = [];

    public function mount()
    {
        $this->roles = Role::where('name', '!=', 'super admin')
            ->orderBy('name', 'desc')
            ->get();

        $this->currentRole = null;
    }

    public function render()
    {
        return view('livewire.admin.permission.role-permission-form');
    }

    public function updatedSelectedRole()
    {
        $datas = [];
        $this->permissionsForm = [];

        if ($this->selectedRole === null) {
            return [];
        }

        $this->currentRole = Role::where('id', $this->selectedRole)->first();

        $permissionsCat = function($currentCat) {
            $permissionsData = Permission::where('name', 'like', '%' . $currentCat . '%');

            if ($currentCat === 'reservation') {
                $permissionsData->limit(4);
            }

            $permissionsData = $permissionsData->get();

            return $permissionsData->map(function ($currentPermission) {
                if ($this->currentRole->hasPermissionTo($currentPermission)) {
                    $this->permissionsForm[] = $currentPermission->id;
                }

                return $currentPermission;
            })->toArray();
        };

        foreach ($this->permissionsCat as $cat) {
            $this->rolePermissions[$cat] = $permissionsCat($cat);
        }
    }

    public function changePermission()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->currentRole->syncPermissions($this->permissionsForm);
    }
}
