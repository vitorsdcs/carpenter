<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->be(factory('App\User')->create());
    }

    public function testCategoryIndex()
    {
        $response = $this->call('GET', '/v1/categories');

        $this->assertEquals(200, $response->status());
    }

    public function testCategoryRead()
    {
        $category = factory('App\Category')->create();

        $response = $this->call('GET', "/v1/categories/$category->id");

        $this->assertEquals(200, $response->status());
    }

    public function testCategoryCreation()
    {
        $category = factory('App\Category')->make()->toArray();

        $response = $this->call('POST', '/v1/categories', $category);

        $this->assertEquals(201, $response->status());
    }

    public function testCategoryCreationIncomplete()
    {
        $category = factory('App\Category')->make()->toArray();
        unset($category['name']);

        $response = $this->call('POST', '/v1/categories', $category);

        $this->assertEquals(422, $response->status());
    }

    public function testCategoryCreationWithUnprocessableTitle()
    {
        $category = factory('App\Category')->make(['name' => str_repeat('a', 256)])->toArray();

        $response = $this->call('POST', '/v1/categories', $category);

        $this->assertEquals(422, $response->status());
    }

    public function testCategoryUpdate()
    {
        $category = factory('App\Category')->create();
        $updatedCategory = factory('App\Category')->make()->toArray();

        $response = $this->call('PUT', "/v1/categories/$category->id", $updatedCategory);

        $this->assertEquals(200, $response->status());
    }

    public function testCategoryUpdateIncomplete()
    {
        $category = factory('App\Category')->create();
        $updatedCategory = factory('App\Category')->make()->toArray();
        unset($updatedCategory['name']);

        $response = $this->call('PUT', "/v1/categories/$category->id", $updatedCategory);

        $this->assertEquals(422, $response->status());
    }

    public function testCategoryDeletion()
    {
        $category = factory('App\Category')->create();

        $response = $this->call('DELETE', "/v1/categories/$category->id");

        $this->assertEquals(204, $response->status());
    }

    public function testCategoryTargeting()
    {
        $anotherUser = factory('App\User')->create();
        $category = factory('App\Category')->create(['client_id' => $anotherUser->client_id]);
        $updatedCategory = factory('App\Category')->make()->toArray();

        $response = $this->call('GET', "/v1/categories/$category->id");
        $this->assertEquals(404, $response->status());

        $response = $this->call('PUT', "/v1/categories/$category->id", $updatedCategory);
        $this->assertEquals(404, $response->status());

        $response = $this->call('DELETE', "/v1/categories/$category->id");
        $this->assertEquals(404, $response->status());
    }
}
