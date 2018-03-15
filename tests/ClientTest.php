<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ClientTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->be(factory('App\User')->create());
    }

    public function testClientIndex()
    {
        $response = $this->call('GET', '/v1/clients');

        $this->assertEquals(200, $response->status());
    }

    public function testClientRead()
    {
        $client = factory('App\Client')->create();

        $response = $this->call('GET', "/v1/clients/$client->client_id");

        $this->assertEquals(200, $response->status());
    }

    public function testClientCreation()
    {
        $client = factory('App\Client')->make()->toArray();

        $response = $this->call('POST', '/v1/clients', $client);

        $this->assertEquals(201, $response->status());
    }

    public function testClientCreationIncomplete()
    {
        $client = factory('App\Client')->make()->toArray();
        unset($client['title']);

        $response = $this->call('POST', '/v1/clients', $client);

        $this->assertEquals(422, $response->status());
    }

    public function testClientCreationWithUnprocessableTitle()
    {
        $client = factory('App\Client')->make(['title' => str_repeat('a', 256)])->toArray();

        $response = $this->call('POST', '/v1/clients', $client);

        $this->assertEquals(422, $response->status());
    }

    public function testClientUpdate()
    {
        $client = factory('App\Client')->create();
        $updatedClient = factory('App\Client')->make()->toArray();

        $response = $this->call('PUT', "/v1/clients/$client->client_id", $updatedClient);

        $this->assertEquals(200, $response->status());
    }

    public function testClientUpdateIncomplete()
    {
        $client = factory('App\Client')->create();
        $updatedClient = factory('App\Client')->make()->toArray();
        unset($updatedClient['title']);

        $response = $this->call('PUT', "/v1/clients/$client->client_id", $updatedClient);

        $this->assertEquals(422, $response->status());
    }

    public function testClientDeletion()
    {
        $client = factory('App\Client')->create();

        $response = $this->call('DELETE', "/v1/clients/$client->client_id");

        $this->assertEquals(204, $response->status());
    }
}
