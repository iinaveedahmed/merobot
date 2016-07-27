# meRobot v1.0 Beta
_An ~~Two~~ Multi-Robot path mapping and collision detection api service._


## PURPOSE
Create API end points that help
__Nigel__ (an avid C<sub>8</sub> H<sub>10</sub> N<sub>4</sub> O<sub>2</sub> fuelled engineer) control his robots;
which will be serving coffee in his shop. :wink:

## END POINTS
_PS : All Paths given here reference to public folder of the application_


## LOGIC
#### Basics
The system use laravel framework for PHP, and utilize most of the core functionality.
The main logic for manoeuvre is handle by collection handler in laravel (quite similar to java collection).

Additionally, The system uses Dingo Api Library for easy request handling. And MySQL database to store data.

__Requirements__
Following basic requirements that must met for deployment of the system and unit test execution
* Apache Headers Module
* CURL Module
* PHP >= 5.5.9
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* Composer
* MySQL

__Deployment__
* Install Apache
* Install MySQL
* Install PHP
* Install Modules & Extension
* Install Composer
* Clone Repo to web folder
* Run ``composer install -vvv --no-dev`` in source directory
* Create .env file using .env.example
* Update .env file with correct environment details
* Run ``php artisan migrate`` in source directory
_ps. make sure all the file permission are set correctly_

#### Details
* The system is using laravel model to access database (/app).
* The system is using laravel migration to generate database tables and relations (/database/migrations).
* The system is using laravel factories to generate database data in unit test (/database/factories/ModelFactory.php)
* The system is using phpunit and laravel TestCase Hook for unit testing (/tests)
* All the api handler are register within main route file with in api v1 scope (/app/Http/routes.php)
* All main login are handled within api controller (/app/Http/Controllers/Api)

After saving the shop directly when the robot is added to system; system check go through all the step the is specified
in the ``command`` param.
System then detect is robot is going out of grid by comparing each step position with shop grid size.
If no collusion is detected the position wrt each move along with heading are added to ``roads`` table.

When the execute function is called system already have all the path and heading for each bot and system find maximum
steps that are required and loop through it. System the check if any two or more bot are not on unique position
and thus they are colliding. If any bot step is less then the max steps bots last position is used.

___
### SHOP
####`POST`

Will create a shop by given height and width grid

__URL__

/api/shop

__Data Params__
```
width: [integer] | greater then 2
height: [integer] | greater then 2
```

___
####`GET`

Will retrive the shop by id

__URL__

/api/shop/[shop_id]

__URL Params__
```
[shop_id]: [integer]
```

___
####`DELETE`

Will delete the shop by id

__URL__

/api/shop/[shop_id]

__URL Params__
```
[shop_id]: [integer]
```

___
### ROBOT
####`POST`

Will add new robot to the shop by id

__URL__

/api/shop/[shop_id]/robot

__URL Params__
```
[shop_id]: [integer]
```

__Data Params__
```
x: [integer] | greater then 0 | less then shop width
y: [integer] | greater then 0 | less then shop height
heading: [character] | one of (N = North, S = South, E = East, W = West)
command: [string] | can contain (M = Move, L = Left, R = Right)
```

####`PUT`

Will update the robot in the shop by shop and robot id

__URL__

/api/shop/[shop_id]/robot/[robot_id]

__URL Params__
```
[shop_id]: [integer]
[robot_id]: [integer]
```

__Data Params__
```
x: [integer] | greater then 0 | less then shop width
y: [integer] | greater then 0 | less then shop height
heading: [character] | one of (N = North, S = South, E = East, W = West)
command: [string] | can contain (M = Move, L = Left, R = Right)
```

####`PUT`

Will delete the robot in the shop by shop and robot id

__URL__

/api/shop/[shop_id]/robot/[robot_id]

__URL Params__
```
[shop_id]: [integer]
[robot_id]: [integer]
```
___
### EXECUTE
####`POST`

Will execute robots manoeuvre in the shop by id

__URL__

/api/shop/[shop_id]/execute

__URL Params__
```
[shop_id]: [integer]
```

_Todo: Add all the response type and example calls in documentation_

___

Created by: [Naveed Ahmed](http://inaveed.com).

Source code: [GitHub](https://github.com/inaveedahmed/merobot)

This work is licensed under a [Creative Commons Attribution 4.0 International License](http://creativecommons.org/licenses/by/4.0/).
