<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage organisers',
            'manage events',
            'manage tickets',
            'manage attendees',
            'admit attendees',
        ];

        $this->out('Seeding permissions');
        collect($permissions)->each(function($permission) {
            Permission::create([
                'name' => $permission,
            ]);
        });

        $roles = [
            'super admin',
            'user',
            'attendee check in',
        ];

        $this->out('Seeding roles');
        collect($roles)->each(function($role) {
            Role::create([
                'name' => $role,
            ]);
        });

        $assignables = [
            'super admin' => [
                'manage organisers',
                'manage events',
                'manage tickets',
                'manage attendees',
                'admit attendees',
            ],
            'user' => [
                'manage tickets',
                'manage attendees',
                'admit attendees',
            ],
            'attendee check in' => [
                'admit attendees',
            ],
        ];

        $this->out('Assigning permissions to roles');
        collect($assignables)->each(function($permissions, $roleName) {
            collect($permissions)->each(function($permissionName) use ($roleName) {
                $permission = Permission::findByName($permissionName);
                /** @var $role Role */
                $role = Role::findByName($roleName);

                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            });
        });

        // We want to assign the default user role to every user in the database if there exists any
        $this->out('Checking legacy users to attach the default roles to');

        $allUsers = User::all();
        Log::debug(sprintf("Found %d users to retrofit default role to", $allUsers->count()));

        if ($allUsers->count() > 0) {
            $allUsers->each(function($user) {
                /** @var \App\Models\User $user */
                if ($user->hasRole('user') === false) {
                    Log::debug(sprintf("Assigning super admin role to legacy user: [ID:%d]", $user->id));
                    $user->assignRole('super admin');
                }
            });
        } else {
            Log::debug('No users could be found that needed to be updated with roles');
        }
    }

    /**
     * @param string $message
     */
    private function out($message)
    {
        $this->command->getOutput()->writeln("<info>$message</info>");
    }
}
