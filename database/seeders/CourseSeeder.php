<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::create([
            'title' => 'Pengantar Cloud Computing',
            'description' => 'Mempelajari arsitektur dasar AWS dan GCP.',
        ]);

        Course::create([
            'title' => 'Implementasi AI dalam Aplikasi Web',
            'description' => 'Menggunakan Hugging Face API untuk analisis teks.',
        ]);
    }
}
