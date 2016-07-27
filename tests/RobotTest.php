<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RobotTest extends TestCase
{
    /**
     * Test execute robot post api and result
     * @return void
     */
    public function testRobotPost()
    {
        // create shop
        $shop = factory(\Merobot\Shop::class)->create();

        // post robot
        $this->post('/api/shop/' . $shop->id . '/robot', [
            'x'=> rand(2, 20),
            'y' => rand(2, 20),
            'heading' => 'N',
            'command' => 'M'
        ]);

        // validate json structure
        $this->seeJsonStructure([
            'x',
            'y',
            'id',
            'name',
            'route',
            'shop_id'
        ]);
    }


    /**
     * Test execute robot get api and result
     * @return void
     */
    public function testRobotGet()
    {
        // create shop
        $shop = factory(\Merobot\Shop::class)->create();

        // create robot in shop
        $robot = factory(Merobot\Robot::class)->create([
            'shop_id' => $shop->id,
        ]);

        // post robot
        $this->put('/api/shop/' . $shop->id . '/robot/' . $robot->id, [
            'x'=> rand(2, 20),
            'y' => rand(2, 20),
            'heading' => 'N',
            'command' => 'M'
        ]);

        // validate json structure
        $this->seeJsonStructure([
            'x',
            'y',
            'id',
            'name',
            'route',
            'shop_id'
        ]);
    }


    /**
     * Test execute robot delete api and result
     * @return void
     */
    public function testRobotDelete()
    {

        // create shop
        $shop = factory(Merobot\Shop::class)->create();

        // create robot in shop
        $robot = factory(Merobot\Robot::class)->create([
            'shop_id' => $shop->id,
        ]);

        // request delete robot by id
        $this->delete('/api/shop/' . $shop->id . '/robot/' . $robot->id);

        // validate structure
        $this->seeJsonStructure([
            'status'
        ]);

        // validate result
        $this->seeJson([
            'status' => 'success',
        ]);
    }
}
