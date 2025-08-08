<?php

namespace Tests\MarioDevv\Criteria;

use MarioDevv\Criteria\Criteria;
use MarioDevv\Criteria\Filter;
use MarioDevv\Criteria\Filters;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CriteriaTest extends TestCase
{
    #[Test]
    public function it_should_create_a_criteria(): void
    {
        $filters = Filters::and([
            Filter::fromPrimitives([
                'field'    => 'status',
                'operator' => '=',
                'value'    => 'pending'
            ])
        ]);

        Criteria::withFilters($filters);

        $this->assertEquals(Filters::TYPE_AND, $filters->logicType());
        $this->assertCount(1, $filters->filters());
        $this->assertEquals('status', $filters->filters()[0]->field()->value());
    }

    #[Test]
    public function it_should_create_a_criteria_with_only_or_filters(): void
    {
        $filters = Filters::or([
            Filter::fromPrimitives([
                'field'    => 'category',
                'operator' => '=',
                'value'    => 'news'
            ]),
            Filter::fromPrimitives([
                'field'    => 'category',
                'operator' => '=',
                'value'    => 'blog'
            ])
        ]);

        Criteria::withFilters($filters);

        $this->assertEquals(Filters::TYPE_OR, $filters->logicType());
        $this->assertCount(2, $filters->filters());
        $this->assertEquals('category', $filters->filters()[0]->field()->value());
        $this->assertEquals('news', $filters->filters()[0]->value()->value());
        $this->assertEquals('blog', $filters->filters()[1]->value()->value());
    }

    #[Test]
    public function it_should_create_a_criteria_with_only_and_filters(): void
    {
        $filters = Filters::and([
            Filter::fromPrimitives([
                'field'    => 'status',
                'operator' => '=',
                'value'    => 'active'
            ]),
            Filter::fromPrimitives([
                'field'    => 'role',
                'operator' => '=',
                'value'    => 'admin'
            ])
        ]);

        Criteria::withFilters($filters);

        $this->assertEquals(Filters::TYPE_AND, $filters->logicType());
        $this->assertCount(2, $filters->filters());
        $this->assertEquals('status', $filters->filters()[0]->field()->value());
        $this->assertEquals('active', $filters->filters()[0]->value()->value());
        $this->assertEquals('role', $filters->filters()[1]->field()->value());
        $this->assertEquals('admin', $filters->filters()[1]->value()->value());
    }

    #[Test]
    public function it_should_create_a_criteria_with_compound_filters(): void
    {
        $filters = Filters::and([
            Filter::fromPrimitives([
                'field'    => 'status',
                'operator' => '=',
                'value'    => 'pending'
            ]),
            Filters::or([
                Filter::fromPrimitives([
                    'field'    => 'type',
                    'operator' => '=',
                    'value'    => 'A'
                ]),
                Filter::fromPrimitives([
                    'field'    => 'type',
                    'operator' => '=',
                    'value'    => 'B'
                ])
            ])
        ]);

        Criteria::withFilters($filters);

        $this->assertEquals(Filters::TYPE_AND, $filters->logicType());
        $this->assertCount(2, $filters->filters());

        // Primer filtro es simple
        $this->assertInstanceOf(Filter::class, $filters->filters()[0]);
        $this->assertEquals('status', $filters->filters()[0]->field()->value());
        $this->assertEquals('pending', $filters->filters()[0]->value()->value());

        // Segundo filtro es un grupo OR
        $this->assertInstanceOf(Filters::class, $filters->filters()[1]);
        $orGroup = $filters->filters()[1];
        $this->assertEquals(Filters::TYPE_OR, $orGroup->logicType());
        $this->assertCount(2, $orGroup->filters());

        $this->assertEquals('type', $orGroup->filters()[0]->field()->value());
        $this->assertEquals('A', $orGroup->filters()[0]->value()->value());
        $this->assertEquals('type', $orGroup->filters()[1]->field()->value());
        $this->assertEquals('B', $orGroup->filters()[1]->value()->value());
    }

}
