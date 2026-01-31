<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    /**
     * Generate unique code by appending random number if needed
     */
    private function generateUniqueCode(string $baseCode): string
    {
        $code = strtoupper($baseCode);
        
        // Check if code already exists
        while (\App\Models\Address::where('code', $code)->exists()) {
            // Append random 2-digit number
            $code = $baseCode . rand(10, 99);
        }
        
        return $code;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate addresses table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('addresses')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get admin user for created_by
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first();
        }
        $adminId = $admin ? $admin->id : 1;

        // Create Bangladesh (Country)
        $bangladesh = Address::create([
            'name' => 'Bangladesh',
            'type' => 'country',
            'parent_id' => null,
            'path' => '/',
            'depth' => 0,
            'code' => $this->generateUniqueCode('BD'),
            'is_active' => true,
            'sort_order' => 1,
            'created_by' => $adminId,
        ]);

        // Divisions
        $divisions = [
            ['name' => 'Chattogram', 'code' => 'CTG'],
            ['name' => 'Dhaka', 'code' => 'DHA'],
            ['name' => 'Rajshahi', 'code' => 'RAJ'],
            ['name' => 'Khulna', 'code' => 'KHU'],
            ['name' => 'Barisal', 'code' => 'BAR'],
            ['name' => 'Sylhet', 'code' => 'SYL'],
            ['name' => 'Rangpur', 'code' => 'RAN'],
            ['name' => 'Mymensingh', 'code' => 'MYM'],
        ];

        $createdDivisions = [];
        foreach ($divisions as $index => $division) {
            $div = Address::create([
                'name' => $division['name'],
                'type' => 'division',
                'parent_id' => $bangladesh->id,
                'path' => '/' . $bangladesh->id . '/',
                'depth' => 1,
                'code' => $this->generateUniqueCode($division['code']),
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_by' => $adminId,
            ]);
            $createdDivisions[$division['name']] = $div;
        }

        // Districts under Chattogram Division
        $chattogramDistricts = [
            ['name' => 'Noakhali', 'code' => 'NOA'],
            ['name' => 'Cumilla', 'code' => 'CUM'],
            ['name' => 'Chandpur', 'code' => 'CDP'],
            ['name' => 'Feni', 'code' => 'FEN'],
            ['name' => 'Lakshmipur', 'code' => 'LAK'],
        ];

        $createdDistricts = [];
        foreach ($chattogramDistricts as $index => $district) {
            $chattogram = $createdDivisions['Chattogram'];
            $dist = Address::create([
                'name' => $district['name'],
                'type' => 'district',
                'parent_id' => $chattogram->id,
                'path' => '/' . $bangladesh->id . '/' . $chattogram->id . '/',
                'depth' => 2,
                'code' => $this->generateUniqueCode($district['code']),
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_by' => $adminId,
            ]);
            $createdDistricts[$district['name']] = $dist;
        }

        // Upazilas under Noakhali District
        $noakhaliUpazilas = [
            ['name' => 'Companigonj', 'code' => 'COM'],
            ['name' => 'Begumganj', 'code' => 'BEG'],
            ['name' => 'Chatkhil', 'code' => 'CHT'],
            ['name' => 'Senbagh', 'code' => 'SEN'],
            ['name' => 'Noakhali Sadar', 'code' => 'NOS'],
        ];

        $createdUpazilas = [];
        foreach ($noakhaliUpazilas as $index => $upazila) {
            $noakhali = $createdDistricts['Noakhali'];
            $upz = Address::create([
                'name' => $upazila['name'],
                'type' => 'upazila',
                'parent_id' => $noakhali->id,
                'path' => '/' . $bangladesh->id . '/' . $createdDivisions['Chattogram']->id . '/' . $noakhali->id . '/',
                'depth' => 3,
                'code' => $this->generateUniqueCode($upazila['code']),
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_by' => $adminId,
            ]);
            $createdUpazilas[$upazila['name']] = $upz;
        }

        // Unions under Companigonj Upazila
        $companigonjUnions = [
            ['name' => 'Sahajadpur', 'code' => 'SAH'],
            ['name' => 'Basurhat', 'code' => 'BAS'],
            ['name' => 'Chaprashirhat', 'code' => 'CPR'],
            ['name' => 'Char Jabbar', 'code' => 'CJA'],
            ['name' => 'Char Parbati', 'code' => 'CPA'],
        ];

        $createdUnions = [];
        foreach ($companigonjUnions as $index => $union) {
            $companigonj = $createdUpazilas['Companigonj'];
            $un = Address::create([
                'name' => $union['name'],
                'type' => 'union',
                'parent_id' => $companigonj->id,
                'path' => '/' . $bangladesh->id . '/' . $createdDivisions['Chattogram']->id . '/' . $createdDistricts['Noakhali']->id . '/' . $companigonj->id . '/',
                'depth' => 4,
                'code' => $this->generateUniqueCode($union['code']),
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_by' => $adminId,
            ]);
            $createdUnions[$union['name']] = $un;
        }

        // Villages under Sahajadpur Union
        $sahajadpurVillages = [
            ['name' => 'Kabirapukur', 'code' => 'KAB'],
            ['name' => 'Sahajadpur', 'code' => 'SAV'],
            ['name' => 'Char Sahajadpur', 'code' => 'CSA'],
            ['name' => 'Bara Sahajadpur', 'code' => 'BSA'],
            ['name' => 'Chhota Sahajadpur', 'code' => 'CHS'],
        ];

        foreach ($sahajadpurVillages as $index => $village) {
            $sahajadpur = $createdUnions['Sahajadpur'];
            Address::create([
                'name' => $village['name'],
                'type' => 'village',
                'parent_id' => $sahajadpur->id,
                'path' => '/' . $bangladesh->id . '/' . $createdDivisions['Chattogram']->id . '/' . $createdDistricts['Noakhali']->id . '/' . $createdUpazilas['Companigonj']->id . '/' . $sahajadpur->id . '/',
                'depth' => 5,
                'code' => $this->generateUniqueCode($village['code']),
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_by' => $adminId,
            ]);
        }

        // Add some Dhaka districts and areas for variety
        $dhakaDistricts = [
            ['name' => 'Dhaka', 'code' => 'DKD'],
            ['name' => 'Gazipur', 'code' => 'GAZ'],
            ['name' => 'Narayanganj', 'code' => 'NAR'],
        ];

        foreach ($dhakaDistricts as $index => $district) {
            $dhaka = $createdDivisions['Dhaka'];
            $dist = Address::create([
                'name' => $district['name'],
                'type' => 'district',
                'parent_id' => $dhaka->id,
                'path' => '/' . $bangladesh->id . '/' . $dhaka->id . '/',
                'depth' => 2,
                'code' => $this->generateUniqueCode($district['code']),
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_by' => $adminId,
            ]);

            // Add some areas under Dhaka district
            if ($district['name'] === 'Dhaka') {
                $dhakaAreas = [
                    ['name' => 'Dhanmondi', 'code' => 'DHA'],
                    ['name' => 'Gulshan', 'code' => 'GUL'],
                    ['name' => 'Banani', 'code' => 'BAN'],
                    ['name' => 'Uttara', 'code' => 'UTT'],
                    ['name' => 'Mirpur', 'code' => 'MIR'],
                ];

                foreach ($dhakaAreas as $areaIndex => $area) {
                    Address::create([
                        'name' => $area['name'],
                        'type' => 'area',
                        'parent_id' => $dist->id,
                        'path' => '/' . $bangladesh->id . '/' . $dhaka->id . '/' . $dist->id . '/',
                        'depth' => 3,
                        'code' => $this->generateUniqueCode($area['code']),
                        'is_active' => true,
                        'sort_order' => $areaIndex + 1,
                        'created_by' => $adminId,
                    ]);
                }
            }
        }

        $this->command->info('AddressSeeder completed successfully!');
        $this->command->info('Created Bangladesh administrative hierarchy:');
        $this->command->info('  - 1 Country (Bangladesh)');
        $this->command->info('  - ' . count($divisions) . ' Divisions');
        $this->command->info('  - Multiple Districts, Upazilas, Unions, Villages, and Areas');
        $this->command->info('Example path: Bangladesh > Chattogram > Noakhali > Companigonj > Sahajadpur > Kabirapukur');
    }
}
