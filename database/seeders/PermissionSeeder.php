<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'tambah level',
            'lihat level',
            'edit level',
            'hapus level',

            'tambah user',
            'lihat user',
            'edit user',
            'hapus user',

            'tambah tunjangan',
            'lihat tunjangan',
            'edit tunjangan',
            'hapus tunjangan',

            'tambah gaji karyawan',
            'lihat gaji karyawan',
            'edit gaji karyawan',
            'hapus gaji karyawan',

            'ajukan kasbon',
            'lihat kasbon',
            'edit kasbon',
            'hapus kasbon',
            'approve kasbon',
            'tolak kasbon',
            'slip gaji karyawan',
            'rekap gaji',

            'tambah tipe kasbon',
            'lihat tipe kasbon',
            'edit tipe kasbon',
            'hapus tipe kasbon',
        ];


        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
