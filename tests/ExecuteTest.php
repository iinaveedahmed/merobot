<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExecuteTest extends TestCase
{


    /**
     * Test Execute Api Call and final result
     * @return void
     */
    public function testExecuteGet()
    {
        $shop = factory(\Merobot\Shop::class)->create();
        
        // add first bot
        $this->post('/api/shop/' . $shop->id . '/robot', [
            'x'=> 1,
            'y' => 1,
            'heading' => 'S',
            'command' => 'MMMLM'
        ]);

        // add second bot
        $this->post('/api/shop/' . $shop->id . '/robot', [
            'x'=> 10,
            'y' => 10,
            'heading' => 'N',
            'command' => 'MMMLM'
        ]);

        // execute shop
        $this->post('/api/shop/' . $shop->id . '/execute');

        // validate json structure
        $this->seeJsonStructure([
            'shop',
            'robots',
        ]);

        // validate first bot final position
        $this->seeJson(
            [
                "final_position" => [
                    "x" => 4,
                    "y" => 2,
                    "heading" => "E",
                ]
            ]
        );

        // validate second bot final position
        $this->seeJson(
            [
                "final_position" => [
                    "x" => 4,
                    "y" => 2,
                    "heading" => "E",
                ]
            ]
        );
    }
}
