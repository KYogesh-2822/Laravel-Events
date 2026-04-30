<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSearchSeeder extends Seeder
{
    // ─────────────────────────────────────────────────────────────
    // SEEDER — For testing with realistic data
    //
    // For 50M real rows, run this in batches and use MySQL
    // LOAD DATA INFILE or a dedicated ETL tool.
    //
    // This seeds 10,000 rows (adjust TOTAL_USERS as needed for testing)
    // Full 50M seeding should use: php artisan db:seed --chunk 50M data
    // ─────────────────────────────────────────────────────────────

    private const TOTAL_USERS = 5_000_000;
    private const CHUNK_SIZE  = 500;

    private array $cities = [
        'Mumbai','Delhi','Bangalore','Chennai','Hyderabad','Pune',
        'Kolkata','Ahmedabad','Jaipur','Surat','Lucknow','Kanpur',
        'Nagpur','Indore','Bhopal','Patna','Agra','Vadodara',
        'Coimbatore','Visakhapatnam','Kochi','Chandigarh','Jodhpur',
    ];

    private array $states = [
        'Maharashtra','Delhi','Karnataka','Tamil Nadu','Telangana',
        'West Bengal','Gujarat','Rajasthan','Uttar Pradesh','Bihar',
        'Andhra Pradesh','Madhya Pradesh','Kerala','Punjab','Haryana',
    ];

    private array $professions = [
        'Software Engineer','Data Scientist','Product Manager','UI Designer',
        'DevOps Engineer','Marketing Manager','Sales Executive','Financial Analyst',
        'HR Manager','Content Writer','Backend Developer','Full Stack Developer',
        'Mobile Developer','QA Engineer','Business Analyst','Project Manager',
        'Database Administrator','Network Engineer','Security Analyst','ML Engineer',
        'Frontend Developer','Cloud Architect','Scrum Master','Technical Lead',
    ];

    private array $firstNames = [
        'Aarav','Arjun','Rohan','Vikram','Siddharth','Amit','Rahul','Priya',
        'Sneha','Pooja','Kavya','Ananya','Neha','Divya','Meera','Ravi',
        'Suresh','Deepak','Kiran','Lakshmi','Mohammed','Fahad','Zara','Aisha',
        'Imran','Rajesh','Sunita','Ramesh','Geeta','Harish','Nisha','Aditya',
        'Shreya','Tanvi','Yash','Kunal','Swati','Ankita','Manish','Preeti',
    ];

    private array $lastNames = [
        'Sharma','Patel','Singh','Kumar','Gupta','Verma','Joshi','Mehta',
        'Shah','Rao','Reddy','Nair','Iyer','Menon','Pillai','Khan',
        'Ansari','Siddiqui','Chaudhary','Yadav','Malhotra','Chopra','Kapoor',
        'Bose','Das','Roy','Banerjee','Chatterjee','Mishra','Tiwari','Dubey',
    ];

    public function run(): void
    {
        $this->command->info('Seeding ' . number_format(self::TOTAL_USERS) . ' users...');

        // Disable foreign key checks for faster insert
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('user_details')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $bar = $this->command->getOutput()->createProgressBar(self::TOTAL_USERS);

        // ── Batch insert for performance ──────────────────────────
        // Inserting one-by-one would take hours at 50M.
        // Chunk inserts reduce round trips: 10K / 500 = 20 DB calls.
        for ($i = 0; $i < self::TOTAL_USERS; $i += self::CHUNK_SIZE) {
            $users       = [];
            $userDetails = [];
            $now         = now();

            for ($j = 0; $j < self::CHUNK_SIZE && ($i + $j) < self::TOTAL_USERS; $j++) {
                $id    = $i + $j + 1;
                $fn    = $this->firstNames[array_rand($this->firstNames)];
                $ln    = $this->lastNames[array_rand($this->lastNames)];
                $email = strtolower("{$fn}.{$ln}{$id}@") . ['gmail','yahoo','outlook','hotmail'][rand(0,3)] . '.com';

                $users[] = [
                    'id'         => $id,
                    'name'       => "{$fn} {$ln}",
                    'email'      => $email,
                    'phone'      => '+91' . rand(7000000000, 9999999999),
                    'city'       => $this->cities[array_rand($this->cities)],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $exp = rand(0, 18);
                $userDetails[] = [
                    'user_id'    => $id,
                    'profession' => $this->professions[array_rand($this->professions)],
                    'bio'        => "{$fn} is an experienced professional from {$this->cities[array_rand($this->cities)]} with {$exp} years of hands-on experience.",
                    'age'        => rand(21, 58),
                    'experience' => $exp,
                    'state'      => $this->states[array_rand($this->states)],
                    'country'    => 'India',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('users')->insert($users);
            DB::table('user_details')->insert($userDetails);
            $bar->advance(count($users));
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('✅ Done! Full-text indexes are rebuilt automatically on insert.');
        $this->command->info('   For MySQL config, ensure: ft_min_word_len = 3');
        $this->command->info('   Then run: REPAIR TABLE users QUICK; to rebuild index.');
    }
}