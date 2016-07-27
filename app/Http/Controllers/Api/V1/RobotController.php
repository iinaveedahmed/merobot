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


    /**
     * @Api POST
     * Add Robot to shop
     * @param Request $request
     * @param         $id
     * @return array|void|static
     */
    public function setRobot(Request $request, $id)
    {
        // validate shop id
        $shop = Shop::find($id);
        if (!$shop) {
            // throw error if not found
            return $this->response->errorNotFound('Try next street. (shop id not found)');
        }

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

        // look for shop wall breach and find paths
        $map_paths = $this->mapPath($request, $id);

        // add the robot to database
        $robot = Robot::create([
            'shop_id' => $id,
            'x'       => $request->x,
            'y'       => $request->y,
            'heading' => $request->heading,
        ]);

        // clear all old paths
        Road::where('robot_id', $robot->id)->delete();

        // add robot id to paths and save
        foreach ($map_paths as $path) {
            $path['robot_id'] = $robot->id;
            Road::create($path);
        }

        // convert robots object to array
        $robot = $robot->toArray();

        // name the robot :) and add path to returning payload
        $robot['name'] = $this->robotName($robot['id']);
        $robot['route'] = $map_paths;

        return $robot;
    }


    /**
     * @API PUT
     * @param Request $request
     * @param         $id
     * @param         $rid
     * @return mixed
     */
    public function updateRobot(Request $request, $id, $rid)
    {

        // validate shop id
        $shop = Shop::find($id);
        if (!$shop) {
            // throw error if not found
            $this->response->errorNotFound('Try next street. (shop id not found)');
        }

        // validate robot id
        $robot = Robot::find($rid);
        if (!$robot) {
            // throw error if not found
            $this->response->errorNotFound(
                'Ops! ' . $this->robotName($rid) . ' gone missing. (robot id not found)'
            );
        }

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

        // look for shop wall breach and find paths
        $map_paths = $this->mapPath($request, $id);

        // update robot
        $robot->update([
            'shop_id' => $id,
            'x'       => $request->x,
            'y'       => $request->y,
            'heading' => $request->heading,
        ]);

        // clear all old paths
        Road::where('robot_id', $rid)->delete();

        // add robot id to paths and save
        foreach ($map_paths as $path) {
            $path['robot_id'] = $rid;
            Road::create($path);
        }

        // name the robot :) and add path to returning payload
        $robot['name'] = $this->robotName($rid);
        $robot['route'] = $map_paths;

        return $robot->toArray();
    }

    /**
     * @Api DELETE
     * Delete robot
     * @param $id
     * @param $rid
     * @return array
     */
    public function deleteRobot($id, $rid)
    {
        // validate robot id
        $robot = Robot::find($rid);
        if (!$robot) {
            // throw error if not found
            $this->response->errorNotFound(
                'Ops! ' . $this->robotName($rid) . ' gone missing. (robot id not found)'
            );
        } else {
            $robot->delete();
            return ['status' => 'success'];
        }
    }


    /**
     * Validate command integrity
     * @param $command_string
     */
    protected function checkCommandIntegrity($command_string)
    {
        // allowed moves
        $valid = ['L', 'R', 'M'];
        $commands = str_split($command_string);

        foreach ($commands as $command) {
            if (!in_array($command, $valid)) {
                throw new ValidationHttpException([['Invalid move in command string']]);
            }
        }
    }


    /**
     * Follow move command wrt. heading
     * And return path
     * @param $request
     * @param $shop_id
     * @return array
     */
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

            // list change in bot
            $map[] = [
                'x'       => $x,
                'y'       => $y,
                'heading' => $heading,
                'step'    => $step++,
            ];
        }

        return $map;
    }


    /**
     * Return change of heading
     * @param $heading
     * @param $command
     * @return mixed|string
     */
    protected function getHeading($heading, $command)
    {
        // heading hash
        $heading_hash = ['N', 'E', 'S', 'W',];

        // find current heading in hash
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


    /**
     * Get updated position wrt last pos
     * .: only one step is possible
     * @param $x
     * @param $y
     * @param $heading
     * @return array
     */
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

        // check wall breach
        $this->checkBreach($x, $y);

        return [
            'x' => $x,
            'y' => $y
        ];
    }


    /**
     * Check wall breach wrt. new position
     * @param $x
     * @param $y
     * @return bool
     */
    protected function checkBreach($x, $y)
    {
        if ($x < 0  || $y < 0) {
            // if bot is going out of shop grid from top or left
            $this->response
                ->errorBadRequest('OPS! '.$this->robotName($x).' is jumping out of shop! (Out of shop border)');
        } elseif ($this->shop->x > $x || $this->shop->y > $y) {
            // if bot is going out of shop grid from bottom or right
            $this->response
                ->errorBadRequest($this->robotName($x).' going to point of no return. (Out of shop border)');
        }

        return true;
    }
}
