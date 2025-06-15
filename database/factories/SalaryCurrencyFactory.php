<?php

namespace Database\Factories;

use App\Models\SalaryCurrency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Enhanced Factory for SalaryCurrency
 * Generated using Laravel 12 best practices.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalaryCurrency>
 */
class SalaryCurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalaryCurrency::class;

    /**
     * Define the model's default state using Enhanced patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currencies = [
            ['name' => 'US Dollar', 'code' => 'USD', 'icon' => '$'],
            ['name' => 'Euro', 'code' => 'EUR', 'icon' => '€'],
            ['name' => 'British Pound', 'code' => 'GBP', 'icon' => '£'],
            ['name' => 'Japanese Yen', 'code' => 'JPY', 'icon' => '¥'],
            ['name' => 'Canadian Dollar', 'code' => 'CAD', 'icon' => 'C$'],
            ['name' => 'Australian Dollar', 'code' => 'AUD', 'icon' => 'A$'],
            ['name' => 'Swiss Franc', 'code' => 'CHF', 'icon' => 'CHF'],
            ['name' => 'Chinese Yuan', 'code' => 'CNY', 'icon' => '¥'],
            ['name' => 'Indian Rupee', 'code' => 'INR', 'icon' => '₹'],
            ['name' => 'South Korean Won', 'code' => 'KRW', 'icon' => '₩'],
            ['name' => 'Mexican Peso', 'code' => 'MXN', 'icon' => '$'],
            ['name' => 'Brazilian Real', 'code' => 'BRL', 'icon' => 'R$'],
            ['name' => 'Russian Ruble', 'code' => 'RUB', 'icon' => '₽'],
            ['name' => 'South African Rand', 'code' => 'ZAR', 'icon' => 'R'],
            ['name' => 'Singapore Dollar', 'code' => 'SGD', 'icon' => 'S$'],
            ['name' => 'Hong Kong Dollar', 'code' => 'HKD', 'icon' => 'HK$'],
            ['name' => 'Norwegian Krone', 'code' => 'NOK', 'icon' => 'kr'],
            ['name' => 'Swedish Krona', 'code' => 'SEK', 'icon' => 'kr'],
            ['name' => 'Danish Krone', 'code' => 'DKK', 'icon' => 'kr'],
            ['name' => 'Turkish Lira', 'code' => 'TRY', 'icon' => '₺'],
        ];

        $currency = $this->faker->unique()->randomElement($currencies);

        return [
            'currency_name' => $currency['name'],
            'is_default' => $this->faker->boolean(5), // 5% chance of being default
            'currency_code' => $currency['code'],
            'currency_icon' => $currency['icon'],
        ];
    }
}
