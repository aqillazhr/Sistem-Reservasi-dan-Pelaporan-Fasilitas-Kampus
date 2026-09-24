<?php

namespace Database\Factories;

use App\Models\FacilityPhoto;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityPhoto>
 */
class FacilityPhotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'facility_id' => Facility::inRandomOrder()->first()?->id
                ?? Facility::factory()->create()->id,
            'file_path'   => 'facility-photos/'.fake()->uuid().'.jpg',
            'is_primary'  => false,
        ];
    }
}
