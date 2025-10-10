<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Faculty,Semester,Subject,Tag};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['BSc CSIT','BE Computer','BCA'] as $n) {
            Faculty::firstOrCreate(['name' => $n]);
        }

        foreach (range(1, 8) as $i) {
            Semester::firstOrCreate(['name' => "Semester $i"]);
        }

        Subject::firstOrCreate(['name' => 'Data Structures']);
        Subject::firstOrCreate(['name' => 'Operating Systems']);

        foreach (['algorithm','mathematics','programming','notes'] as $t) {
            Tag::firstOrCreate(['slug' => str($t)->slug()], ['name' => $t]);
        }

        // ✅ Call the dedicated admin seeder; do NOT also create admin here.
        $this->call(AdminUserSeeder::class);
    }
}
