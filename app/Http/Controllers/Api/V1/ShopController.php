<?php

namespace Merobot\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Merobot\Http\Requests;
use Merobot\Robot;
use Merobot\Shop;

class ShopController extends BaseController
{
    
    public function setShop(Request $request)
    {
        $validation = [
            'width' => 'required|integer',
            'height' => 'required|integer',
        ];

        $this->validateApiRequest($validation, $request);

        $shop = Shop::create([
            'width' => $request->width,
            'height' => $request->height,
        ]);

        $shop = $shop->toArray();
        $shop['name'] = $this->shopName($shop['id']);

        return $shop;
    }
    
    public function getShop($id)
    {
        $shop = Shop::find($id);

        if ($shop) {
            $shop = $shop->toArray();
            $shop['name'] = $this->shopName($shop['id']);
            return $shop;
        } else {
            return $this->response->errorNotFound('Try next street. (id not found)');
        }
    }

    public function deleteShop($id)
    {
        $store = Shop::find($id);

        if ($store) {
            $store->delete();
            return ['status' => 'success'];
        } else {
            return $this->response->errorNotFound('Your shop is missing. OPSI! (id not found)');
        }
    }
    
}
