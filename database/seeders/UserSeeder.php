<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id' => 1,
                'fk_m_hak_akses' => 1,
                'password' => Hash::make('12345'), // class untuk mengenkripsi/hash password
                'nama_pengguna' => 'Gelby Firmansyah',
                'alamat_pengguna' => 'Jl. Joyo Raharjo',
                'no_hp_pengguna' => '08111',
                'email_pengguna' => 'gelbifirmansyah12@gmail.com',
                'pekerjaan_pengguna' => 'Administrasi',   
                'nik_pengguna' => '11111',
                'created_by' => 'Gelby F.'
            ],
            [
                'user_id' => 2,
                'fk_m_hak_akses' => 2,
                'password' => Hash::make('12345'), // class untuk mengenkripsi/hash password
                'nama_pengguna' => 'Zainal Arifin',
                'alamat_pengguna' => 'Jl. Sudirman',
                'no_hp_pengguna' => '08222',
                'email_pengguna' => 'zainalarifin@gmail.com',
                'pekerjaan_pengguna' => 'Verifikator',   
                'nik_pengguna' => '22222',
                'created_by' => 'Gelby F.'
            ],
            [
                'user_id' => 3,
                'fk_m_hak_akses' => 3,
                'password' => Hash::make('12345'), // class untuk mengenkripsi/hash password
                'nama_pengguna' => 'Agus Subianto',
                'alamat_pengguna' => 'Jl. Veteran',
                'no_hp_pengguna' => '08333',
                'email_pengguna' => 'agussubianto@gmail.com',
                'pekerjaan_pengguna' => 'Manajemen dan Pimpinan Unit',   
                'nik_pengguna' => '33333',
                'created_by' => 'Gelby F.'
            ],
            [
                'user_id' => 4,
                'fk_m_hak_akses' => 4,
                'password' => Hash::make('12345'), // class untuk mengenkripsi/hash password
                'nama_pengguna' => 'Ahmad Isroqi',
                'alamat_pengguna' => 'Jl. Soekarno Hatta',
                'no_hp_pengguna' => '08444',
                'email_pengguna' => 'isroqiaja@gmail.com',
                'pekerjaan_pengguna' => 'Manajer',   
                'nik_pengguna' => '44444',
                'created_by' => 'Gelby F.'
            ],
        ];    
        DB::table('m_user')->insert($data);
    }
}
