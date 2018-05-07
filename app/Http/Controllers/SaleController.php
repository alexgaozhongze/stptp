<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 18-5-7
 * Time: 下午2:48
 */
namespace App\Http\Controllers;

use App\Stmbuy;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    private function getObject($info)
    {
        if ('stmbuy' == $info['p_name']) {
            $object = new Stmbuy($info['p_id']);
        }
        return $object;
    }

    public function autoSale()
    {
        $categoryList = DB::table('platform')
            ->join('category', 'platform.p_id', '=', 'category.p_id')
            ->where([['platform.p_status', '=', '1'], ['category.c_status', '=', '1']])
            ->select('platform.p_id', 'platform.p_name', 'category.c_id')
            ->get()->toArray();
        foreach ($categoryList as $clv) {
            $clv = (array) $clv;
            $object = self::getObject($clv);
            $object->autoSale($clv['c_id']);
        }
    }

}