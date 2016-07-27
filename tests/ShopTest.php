<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShopTest extends TestCase
{
    
    /**
     * Test execute shop post and result
     * @return void
     */
    public function testShopPost()
    {
        // post shop
        $this->post('/api/shop', [
            'width'=> rand(2, 20),
            'height' => rand(2, 20),
        ]);

        // validate structure
        $this->seeJsonStructure([
            'width',
            'height',
            'id',
        ]);
    }


    /**
     * Test execute shop get and result
     * @return void
     */
    public function testShopGet()
    {

        // create shop
        $shop = factory(Merobot\Shop::class)->create();

        // request shop by id
        $this->get('/api/shop/'.$shop->id);

        // validate structure
        $this->seeJsonStructure([
            'width',
            'height',
            'id',
        ]);

        // validate result
        $this->seeJson([
            'width' => $shop->width,
            'height' => $shop->height,
            'id' => $shop->id,
        ]);
    }


    /**
     * Test execute shop delete and result
     * @return void
     */
    public function testShopDelete()
    {

        // create shop
        $shop = factory(Merobot\Shop::class)->create();

        // request shop delete by id
        $this->delete('/api/shop/'.$shop->id);

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
