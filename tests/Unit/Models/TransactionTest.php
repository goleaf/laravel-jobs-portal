<?php

namespace Tests\Unit\Models;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = Transaction::factory()->create();

        $this->assertInstanceOf(Transaction::class, $model);
        $this->assertDatabaseHas('transactions', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new Transaction;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new Transaction;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = Transaction::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = Transaction::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('transactions', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = Transaction::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('transactions', [
            'id' => $modelId,
        ]);
    }
}
