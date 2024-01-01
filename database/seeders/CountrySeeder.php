<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Country::truncate();

        $supportedCountries = $this->countries();

        foreach ($supportedCountries as $country) {
            Country::create($country);
        }
    }

    /**
     * List of supported countries
     *
     * @var array
     */
    private function countries(): array
    {
        return [
            ["name" => "United States of America", "code" => "US"],
        ];
    }
}
