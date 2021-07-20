<?php

namespace Spatie\JsonApiPaginate\Test;

use Spatie\JsonApiPaginate\Test\Models\TargetModel;
use Spatie\JsonApiPaginate\Test\Models\TestModel;
use Spatie\JsonApiPaginate\Test\Models\ThroughModel;

/**
 * @internal
 * @coversNothing
 */
class HasManyThroughTest extends TestCase
{
    protected $model;
    
    public function setUp(): void
    {
        parent::setUp();

        ThroughModel::create([
            'name' => 'Through Model 1',
            'test_model_id' => 1,
        ]);

        collect(range(1, 40))->each(function ($index) {
            TargetModel::create([
                'name' => "Target Model - {$index}",
                'through_model_id' => 1,
            ]);
        });

        $this->model = TestModel::find(1);
    }

    /** @test */
    public function it_can_paginate_records()
    {
        $paginator = $this->model->targetModels()->jsonPaginate();

        $this->assertEquals('http://localhost?page%5Bnumber%5D=2', $paginator->nextPageUrl());
    }

    /** @test */
    public function it_returns_the_amount_of_records_specified_in_the_config_file()
    {
        config()->set('json-api-paginate.default_size', 10);

        $paginator = $this->model->targetModels()->jsonPaginate();

        $this->assertCount(10, $paginator);
    }

    /** @test */
    public function it_can_return_the_specified_amount_of_records()
    {
        $paginator = $this->model->targetModels()->jsonPaginate(15);

        $this->assertCount(15, $paginator);
    }

    /** @test */
    public function it_will_not_return_more_records_that_the_configured_maximum()
    {
        $paginator =$this->model->targetModels()->jsonPaginate(15);

        $this->assertCount(15, $paginator);
    }

    /** @test */
    public function it_can_set_a_custom_base_url_in_the_config_file()
    {
        config()->set('json-api-paginate.base_url', 'https://example.com');

        $paginator = $this->model->targetModels()->jsonPaginate();

        $this->assertEquals('https://example.com?page%5Bnumber%5D=2', $paginator->nextPageUrl());
    }

    /** @test */
    public function it_can_use_simple_pagination()
    {
        config()->set('json-api-paginate.use_simple_pagination', true);

        $paginator = $this->model->targetModels()->jsonPaginate();

        $this->assertFalse(method_exists($paginator, 'total'));
    }

    public function test_the_correct_ids_are_returned()
    {
        $paginator = $this->model->targetModels()->jsonPaginate(15);

        $items = $paginator->items();

        $models = $this->model->targetModels()->limit(15)->get()->all();

        $this->assertEquals($items, $models);

    }
}
