<?php

namespace Tests\Unit\Models;

use App\Models\Skill;
use PHPUnit\Framework\TestCase;

class SkillModelTest extends TestCase
{
    /** @test */
    public function it_has_correct_table_name()
    {
        $skill = new Skill();
        $this->assertEquals('skills', $skill->getTable());
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $skill = new Skill();
        $fillable = $skill->getFillable();

        $expectedFillable = [
            'name',
            'description',
            'is_default',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $skill = new Skill();
        $casts = $skill->getCasts();

        $expectedCasts = [
            'id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'is_default' => 'boolean',
        ];

        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertEquals($cast, $casts[$attribute]);
        }
    }

    /** @test */
    public function it_can_be_instantiated_with_attributes()
    {
        $skill = new Skill([
            'name' => 'PHP',
            'description' => 'PHP Programming Language',
            'is_default' => false,
        ]);

        $this->assertEquals('PHP', $skill->name);
        $this->assertEquals('PHP Programming Language', $skill->description);
        $this->assertEquals(false, $skill->is_default);
    }

    /** @test */
    public function it_has_relationship_methods()
    {
        $skill = new Skill();
        
        // Test that relationship methods exist
        $this->assertTrue(method_exists($skill, 'jobs'));
        $this->assertTrue(method_exists($skill, 'candidate'));
        $this->assertTrue(method_exists($skill, 'jobsSkill'));
    }

    /** @test */
    public function it_has_validation_rules()
    {
        $expectedRules = [
            'name' => 'required|unique:skills,name|max:150',
        ];

        $this->assertEquals($expectedRules, Skill::$rules);
    }
} 