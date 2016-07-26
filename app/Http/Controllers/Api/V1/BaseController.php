<?php

namespace Merobot\Http\Controllers\Api\V1;

use Dingo\Api\Exception\ValidationHttpException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Validator;
use Merobot\Http\Controllers\Controller;
use Merobot\Http\Requests;

class BaseController extends Controller
{


    use Helpers;


    protected function validateApiRequest($validation, $request)
    {
        if (is_array($validation) && count($validation) >= 1) {
            //split name list
            $fields_list = [];

            foreach ($validation as $key => $value) {
                $fields_list[] = $key;
            }

            $fields = $request->only($fields_list);

            $validator = Validator::make($fields, $validation);

            if ($validator->fails()) {
                throw new ValidationHttpException($validator->errors()->all());
            }

            return $fields_list;
        }
    }


    protected function robotName($id)
    {
        // name list
        $names = [
            'R2-D2',
            'C-3PO',
            'BB-8',
            'PIKACHU',
            'MEWTWO',
            'HITMONCHAN',
            'PORYGON',
            'EEVEE',
            'JIGGLYPUFF',
            'WAL-E',
        ];

        $id = ($id > 9) ? $id % 10 : $id;

        return $names[$id];
    }


    protected function shopName($id)
    {
        // name list
        $names = [
            'Nabo',
            'Death Star',
            'Galatic City',
            'Jedi Temple',
            'Droid Factory',
            'Artisan Cave',
            'Battle Resort',
            'Crescent Isle',
            'Dotted Hole',
            'Hall of Origin',
        ];

        $id = ($id > 9) ? $id % 10 : $id;

        return $names[$id];
    }

}
