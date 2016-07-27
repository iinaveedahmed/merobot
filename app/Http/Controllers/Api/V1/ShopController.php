<?php

namespace Merobot\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Merobot\Http\Requests;
use Merobot\Shop;

class ShopController extends BaseController
{


    /**
     * @Api POST
     * Create new shop
     * @param Request $request
     * @return array|static
     */
    public function setShop(Request $request)
    {
        // validate
        $validation = [
            'width'  => 'required|integer|min:2,',
            'height' => 'required|integer|min:2',
        ];
        $this->validateApiRequest($validation, $request);

        // create
        $shop = Shop::create([
            'width'  => $request->width,
            'height' => $request->height,
        ]);
        $shop = $shop->toArray();

        // name the shop
        $shop['name'] = $this->shopName($shop['id']);

        return $shop;
    }


    /**
     * @Api GET
     * Get shop info by id
     * @param $id
     */
    public function getShop($id)
    {
        // look for shop
        $shop = Shop::find($id);

        if ($shop) {
            // if shop present
            $shop = $shop->toArray();

            // name the shop
            $shop['name'] = $this->shopName($shop['id']);

            return $shop;
        } else {
            // throw error if not found
            $this->response->errorNotFound('Try next street. (shop id not found)');
        }
    }


    /**
     * @Api DELETE
     * Delete shop by id
     * @param $id
     * @return array|void
     */
    public function deleteShop($id)
    {
        // look for shop
        $store = Shop::find($id);

        if ($store) {
            // if shop present; delete it
            $store->delete();

            // return success
            return ['status' => 'success'];
        } else {
            // throw error if not found
            $this->response->errorNotFound('Your shop is missing. OPSI! (shop id not found)');
        }
    }

}
