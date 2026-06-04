<?php

namespace Database\Seeders;

use App\Models\LabTestDefinition;
use Illuminate\Database\Seeder;

class LabTestDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        LabTestDefinition::factory()->count(8)->create();
    }
}
