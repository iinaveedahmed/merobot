<?php

namespace Merobot\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use Merobot\Http\Requests;
use Merobot\Http\Controllers\Controller;
use Merobot\Road;
use Merobot\Robot;
use Merobot\Shop;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Iterator\PathFilterIterator;

class RunnerController extends BaseController
{


    /**
     * @Api POST
     * Run robots in shop
     * @param $id
     * @return array
     */
    public function execute($id)
    {
        // validate shop id
        $shop = Shop::find($id);
        if (!$shop) {
            $this->response->errorNotFound('Ops! Shop is closed. (shop id not found)');
        }

        // validate if robot is present in shop
        $robots = $shop->robots()->get();
        if ($robots->isEmpty()) {
            $this->response->errorNotFound('All robots are sleeping. (no robot found)');
        }

        // find maximum number of steps
        $robots_ids = $robots->pluck('id')->toArray();
        $max_steps = Road::select(DB::raw("max(step) as max"))->whereIn('robot_id', $robots_ids)->first();

        // detect if any collision is eminent
        $this->collisionDetection($robots_ids, $max_steps->max);

        //response success
        $response['shop'] = [
            'id' => $shop->id,
            'name' => $this->shopName($shop->id),
            'robots' => count($robots_ids),
            'status' => 'Execution Successful',
        ];

        // get all robots by id
        $robots = Robot::whereIn('id', $robots_ids);

        // return initial and final position of all robots
        // list remain in same order as inserted
        foreach ($robots->get() as $robot) {
            // get robot last step
            $road = Road::where('robot_id', $robot->id)
                ->orderBy('step', 'desc')
                ->first();

            // add to payload
            $response['robots'][] = [
                'id' => $robot->id,
                'name' => $this->robotName($robot->id),
                'initial_position' => [
                    'x' => $robot->x,
                    'y' => $robot->y,
                    'heading' => $robot->heading,
                ],
                'final_position' => [
                    'x' => $road->x,
                    'y' => $road->y,
                    'heading' => $road->heading,
                ]
            ];
        }

        return $response;
    }


    /**
     * Detect Collision by steps unique location
     * @param $robots
     * @param $max_step
     * @return boolean
     */
    public function collisionDetection($robots, $max_step)
    {
        // get final step of each robot
        $last_step = Road::whereIn('robot_id', $robots)
            ->groupBy('robot_id')
            ->orderBy('step', 'desc')
            ->get();

        // find the max step of each robot
        $max_step_robot = $last_step->pluck('step', 'robot_id');

        // go through each step to find any collision
        for ($i=1; $i <= $max_step; $i++) {
            // get position of current step
            $robots_nav = Road::whereIn('robot_id', $robots)->where('step', $i)->get();

            // if robot steps are already finished use last step
            foreach ($robots as $robot) {
                if ($i > $max_step_robot[$robot]) {
                    $robots_nav->merge($last_step->where('robot_id', $robot));
                }
            }

            // check if all robot are on unique location
            $collision = $robots_nav->unique(
                function ($nav) {
                    return $nav['x'] . '-' . $nav['y'];
                }
            );

            if (count($collision) != count($robots)) {
                // if all robots are not on unique location
                $this->response->error("Collusion Decetected on step $i", 403);
            }
        }

        return true;
    }

}
