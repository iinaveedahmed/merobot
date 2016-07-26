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
    public function execute($id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            $this->response->errorNotFound('Ops! Shop is closed. (not found)');
        }

        $robots = $shop->robots()->get();
        if ($robots->isEmpty()) {
            $this->response->errorNotFound('All robots are sleeping. (not found)');
        }

        // find maximum steps
        $robots_ids = $robots->pluck('id')->toArray();
        $max_steps = Road::select(DB::raw("max(step) as max"))->whereIn('robot_id', $robots_ids)->first();

        $this->collusionDetection($robots_ids, $max_steps->max);

        //response success
        $response['shop'] = [
            'id' => $shop->id,
            'name' => $this->shopName($shop->id),
            'robots' => count($robots_ids),
            'status' => 'Execution Successful',
        ];

        $robots = Robot::whereIn('id', $robots_ids);
        foreach ($robots->get() as $robot) {
            $road = Road::where('robot_id', $robot->id)
                ->orderBy('step', 'desc')
                ->groupBy('robot_id')
                ->first();
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


    public function collusionDetection($robots, $max_step)
    {
        $last_step = Road::whereIn('robot_id', $robots)
            ->groupBy('robot_id')
            ->orderBy('step', 'desc')
            ->get();

        $max_step_robot = $last_step->pluck('step', 'robot_id');

        for ($i=1; $i <= $max_step; $i++) {
            $robots_nav = Road::whereIn('robot_id', $robots)->where('step', $i)->get();

            foreach ($robots as $robot) {
                if ($i > $max_step_robot[$robot]) {
                    $robots_nav->merge($last_step->where('robot_id', $robot));
                }
            }

            $collision = $robots_nav->unique(
                function ($nav) {
                    return $nav['x'] . '-' . $nav['y'];
                }
            );

            if (count($collision) != count($robots)) {
                $this->response->error("Collusion Decetected on step $i", 403);
            }
        }
    }

}
