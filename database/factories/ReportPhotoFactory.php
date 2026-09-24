<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\ReportPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportPhoto>
 */
class ReportPhotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'report_id' => Report::inRandomOrder()->first()?->id
                ?? Report::factory()->baru()->create()->id,
            'file_path'  => 'report-photos/'.fake()->uuid().'.jpg',
        ];
    }
}
