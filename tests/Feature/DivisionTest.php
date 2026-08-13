<?php

namespace Tests\Feature;

use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DivisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_division(): void
    {
        $division = Division::factory()->create([
            'name' => 'Divisi Teknologi',
            'description' => 'Mengembangkan sistem perangkat lunak.',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('divisions', [
            'name' => 'Divisi Teknologi',
            'is_active' => 1,
        ]);
    }

    public function test_active_scope_returns_only_active_divisions(): void
    {
        $activeDivision = Division::factory()->create(['is_active' => true]);
        $inactiveDivision = Division::factory()->create(['is_active' => false]);

        $activeDivisions = Division::query()->active()->get();

        $this->assertTrue($activeDivisions->contains($activeDivision));
        $this->assertFalse($activeDivisions->contains($inactiveDivision));
    }
}
