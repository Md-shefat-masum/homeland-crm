<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\UserSetting;
use App\Models\Device;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate all user-related tables first
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('permission_role')->truncate();
        DB::table('role_user')->truncate();
        DB::table('devices')->truncate();
        DB::table('user_settings')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $password = Hash::make('@#11221122');

        // Create Roles
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrator with full access',
        ]);

        $managerRole = Role::create([
            'name' => 'manager',
            'description' => 'Manager with management access',
        ]);

        $employeeRole = Role::create([
            'name' => 'employee',
            'description' => 'Employee with basic access',
        ]);

        // Create Permissions
        $permissions = [
            ['name' => 'view_dashboard', 'description' => 'View dashboard'],
            ['name' => 'manage_users', 'description' => 'Manage users'],
            ['name' => 'manage_customers', 'description' => 'Manage customers'],
            ['name' => 'manage_leads', 'description' => 'Manage leads'],
            ['name' => 'manage_projects', 'description' => 'Manage projects'],
            ['name' => 'view_reports', 'description' => 'View reports'],
            ['name' => 'manage_settings', 'description' => 'Manage settings'],
            ['name' => 'send_sms', 'description' => 'Send SMS'],
            ['name' => 'view_analytics', 'description' => 'View analytics'],
            ['name' => 'manage_backups', 'description' => 'Manage backups'],
        ];

        $createdPermissions = [];
        foreach ($permissions as $permission) {
            $createdPermissions[$permission['name']] = Permission::create($permission);
        }

        // Assign all permissions to admin role
        $permissionIds = array_map(function($permission) {
            return $permission->id;
        }, $createdPermissions);
        $adminRole->permissions()->attach($permissionIds);

        // Assign some permissions to manager role
        $managerRole->permissions()->attach([
            $createdPermissions['view_dashboard']->id,
            $createdPermissions['manage_customers']->id,
            $createdPermissions['manage_leads']->id,
            $createdPermissions['manage_projects']->id,
            $createdPermissions['view_reports']->id,
            $createdPermissions['send_sms']->id,
            $createdPermissions['view_analytics']->id,
        ]);

        // Assign basic permissions to employee role
        $employeeRole->permissions()->attach([
            $createdPermissions['view_dashboard']->id,
            $createdPermissions['manage_customers']->id,
            $createdPermissions['manage_leads']->id,
            $createdPermissions['send_sms']->id,
        ]);

        // Create Users
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@crm.com',
                'mobile' => '01700000001',
                'uid' => '100001',
                'address' => 'Dhaka, Bangladesh',
                'photo' => 'avatar.png',
                'info' => [
                    'department' => 'IT',
                    'designation' => 'System Administrator',
                    'join_date' => '2024-01-01',
                ],
                'password' => $password,
                'role' => 'admin',
                'is_active' => true,
                'is_approved' => true,
                'is_blocked' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@crm.com',
                'mobile' => '01700000002',
                'uid' => '100002',
                'address' => 'Dhaka, Bangladesh',
                'photo' => 'avatar.png',
                'info' => [
                    'department' => 'Sales',
                    'designation' => 'Sales Manager',
                    'join_date' => '2024-01-15',
                ],
                'password' => $password,
                'role' => 'manager',
                'is_active' => true,
                'is_approved' => true,
                'is_blocked' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Employee',
                'email' => 'employee@crm.com',
                'mobile' => '01700000003',
                'uid' => '100003',
                'address' => 'Dhaka, Bangladesh',
                'photo' => 'avatar.png',
                'info' => [
                    'department' => 'Sales',
                    'designation' => 'Sales Executive',
                    'join_date' => '2024-02-01',
                ],
                'password' => $password,
                'role' => 'employee',
                'is_active' => true,
                'is_approved' => true,
                'is_blocked' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@crm.com',
                'mobile' => '01700000004',
                'uid' => '100004',
                'address' => 'Chittagong, Bangladesh',
                'photo' => 'avatar.png',
                'info' => [
                    'department' => 'Sales',
                    'designation' => 'Sales Agent',
                    'join_date' => '2024-02-10',
                ],
                'password' => $password,
                'role' => 'employee',
                'is_active' => true,
                'is_approved' => true,
                'is_blocked' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@crm.com',
                'mobile' => '01700000005',
                'uid' => '100005',
                'address' => 'Sylhet, Bangladesh',
                'photo' => 'avatar.png',
                'info' => [
                    'department' => 'Sales',
                    'designation' => 'Sales Agent',
                    'join_date' => '2024-02-15',
                ],
                'password' => $password,
                'role' => 'employee',
                'is_active' => true,
                'is_approved' => true,
                'is_blocked' => false,
                'email_verified_at' => now(),
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::create($userData);
            $createdUsers[$userData['email']] = $user;

            // Create User Settings
            UserSetting::create([
                'user_id' => $user->id,
                'call_recording_enabled' => false,
                'speech_to_text_enabled' => true,
                'app_lock_enabled' => false,
                'app_lock_timeout_seconds' => 0,
            ]);

            // Assign role to user
            $roleName = $userData['role'];
            if ($roleName === 'admin') {
                $user->roles()->attach($adminRole->id);
            } elseif ($roleName === 'manager') {
                $user->roles()->attach($managerRole->id);
            } else {
                $user->roles()->attach($employeeRole->id);
            }

            // Create a device for each user (simulating mobile app login)
            Device::create([
                'user_id' => $user->id,
                'device_id' => 'device_' . $user->id . '_' . time(),
                'device_name' => $user->name . "'s Android Device",
                'platform' => 'android',
                'app_version' => '1.0.0',
                'last_seen_at' => now(),
                'is_active' => true,
            ]);
        }

        $this->command->info('UserSeeder completed successfully!');
        $this->command->info('Created ' . count($users) . ' users');
        $this->command->info('Created ' . count($permissions) . ' permissions');
        $this->command->info('Created 3 roles (admin, manager, employee)');
    }
}
