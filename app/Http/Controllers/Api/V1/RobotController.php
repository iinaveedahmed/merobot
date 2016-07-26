<?php

namespace Merobot\Http\Controllers\Api\V1;

use Dingo\Api\Exception\ValidationHttpException;
use Illuminate\Http\Request;
use Merobot\Http\Requests;
use Merobot\Road;
use Merobot\Robot;
use Merobot\Shop;

class RobotController extends BaseController
{

    protected $shop;

    public function setRobot(Request $request, $id)
    {
        //validate request
        $validation = [
            'x'       => 'required|integer',
            'y'       => 'required|integer',
            'heading' => 'required|in:N,S,E,W',
            'command' => 'required|alpha',
        ];

        $this->validateApiRequest($validation, $request);

        // validate command
        $this->checkCommandIntegrity($request->command);

        // look for shop wall breach
        $map_paths = $this->mapPath($request, $id);

        $robot = Robot::create([
            'shop_id' => $id,
            'x'       => $request->x,
            'y'       => $request->y,
            'heading' => $request->heading,
        ]);

        Road::where('robot_id', $robot->id)->delete();

        foreach ($map_paths as $path) {
            $path['robot_id'] = $robot->id;
            Road::create($path);
        }

        $robot = $robot->toArray();
        $robot['name'] = $this->robotName($robot['id']);
        $robot['route'] = $map_paths;
        return $robot;
    }


    protected function checkCommandIntegrity($command_string)
    {
        $valid = ['L', 'R', 'M'];
        $commands = str_split($command_string);

        foreach ($commands as $command) {
            if (!in_array($command, $valid)) {
                throw new ValidationHttpException([['Invalid command string']]);
            }
        }
    }


    protected function mapPath($request, $shop_id)
    {
        //load shop
        $this->shop = Shop::find($shop_id);

        //init mapping var
        $x = $request->x;
        $y = $request->y;
        $heading = $request->heading;
        $step = 0;

        //commands list
        $commands = str_split($request->command);

        //set initial position
        $map[] = [
            'x'       => $x,
            'y'       => $y,
            'heading' => $heading,
            'step'    => $step,
        ];

        foreach ($commands as $command) {
            // if rotating
            if (in_array($command, ['L', 'R'])) {
                $heading = $this->getHeading($heading, $command);
            }

            // if moving
            if ($command == 'M') {
                $position = $this->getPosition($x, $y, $heading);
                $x = $position['x'];
                $y = $position['y'];
            }

            // list change
            $map[] = [
                'x'       => $x,
                'y'       => $y,
                'heading' => $heading,
                'step'    => $step++,
            ];
        }

        return $map;
    }


    protected function getHeading($heading, $command)
    {
        $heading_hash = ['N', 'E', 'S', 'W',];
        $heading = array_search($heading, $heading_hash);

        // decode direction
        switch ($command) {
            case 'L':
                $heading --;
                break;
            case 'R':
                $heading ++;
                break;
        }

        // return new direction
        if ($heading > 3) {
            return 'N';
        } elseif ($heading < 0) {
            return 'W';
        } else {
            return $heading_hash[$heading];
        }
    }


    protected function getPosition($x, $y, $heading)
    {
        // decode new position
        switch ($heading) {
            case 'N':
                $x--;
                break;
            case 'S':
                $x++;
                break;
            case 'W':
                $y--;
                break;
            case 'E':
                $y++;
                break;
        }

        $this->checkBreach($x, $y);

        return [
            'x' => $x,
            'y' => $y
        ];
    }

    protected function checkBreach($x, $y)
    {
        if ($x < 0  || $y < 0) {
            $this->response
                ->errorBadRequest('OPS! '.$this->robotName($x).' is jumping out of shop! (Out of shop border)');
        } elseif ($this->shop->x > $x || $this->shop->y > $y) {
            $this->response
                ->errorBadRequest($this->robotName($x).' going to point of no return. (Out of shop border)');
        }

        return true;
    }
}
