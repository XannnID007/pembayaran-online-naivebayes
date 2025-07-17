<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@ma.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Sample Student Users
        $students = [
            [
                'name' => 'Ahmad Rizki Pratama',
                'email' => 'ahmad.rizki@student.ma.edu',
                'nis' => '2024001',
                'kelas' => 'XII IPA 1',
                'alamat' => 'Jl. Merdeka No. 123, Cimahi',
                'no_hp' => '081234567890'
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@student.ma.edu',
                'nis' => '2024002',
                'kelas' => 'XII IPA 1',
                'alamat' => 'Jl. Sudirman No. 456, Cimahi',
                'no_hp' => '081234567891'
            ],
            [
                'name' => 'Muhammad Fajar',
                'email' => 'muhammad.fajar@student.ma.edu',
                'nis' => '2024003',
                'kelas' => 'XI IPS 2',
                'alamat' => 'Jl. Ahmad Yani No. 789, Cimahi',
                'no_hp' => '081234567892'
            ],
            [
                'name' => 'Dewi Sartika',
                'email' => 'dewi.sartika@student.ma.edu',
                'nis' => '2024004',
                'kelas' => 'XI IPS 2',
                'alamat' => 'Jl. Gatot Subroto No. 321, Cimahi',
                'no_hp' => '081234567893'
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@student.ma.edu',
                'nis' => '2024005',
                'kelas' => 'X IPA 1',
                'alamat' => 'Jl. Diponegoro No. 654, Cimahi',
                'no_hp' => '081234567894'
            ]
        ];

        foreach ($students as $studentData) {
            $user = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'nama' => $studentData['name'],
                'nis' => $studentData['nis'],
                'kelas' => $studentData['kelas'],
                'alamat' => $studentData['alamat'],
                'no_hp' => $studentData['no_hp'],
            ]);
        }

        // Create demo student for easy testing
        $demoStudent = User::create([
            'name' => 'Ilham Atmaja',
            'email' => 'siswa@ma.edu',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);

        Siswa::create([
            'user_id' => $demoStudent->id,
            'nama' => 'Kamaludin',
            'nis' => '2024999',
            'kelas' => 'XII IPA 1',
            'alamat' => 'Alamat Demo',
            'no_hp' => '081234567999',
        ]);
    }
}
