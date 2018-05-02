<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 18-5-2
 * Time: 上午11:33
 */
namespace App\Http\Controllers;
use App\Stmbuy;

class StmbuyController extends Controller
{
    private $stmbuy;
    private $itemPrice = [
        [
            'itemId' => '2451526758',
            'itemCostPrice' => '10.50'
        ]
    ];

    public function __construct()
    {
        $this->stmbuy = new Stmbuy(false);
    }

    public function index()
    {
        $this->stmbuy->checkLogin = false;
        echo $this->stmbuy->curlIndex();
    }

    public function test()
    {
        $pas = 'alex333';
        $key = 'eollt0hj994';
        $res = hash_hmac('sha1', $pas, $key);
        echo md5($res);
        echo '<br/>';
        echo $res;
    }

    public function autoSale()
    {
        $itemPrice = $this->itemPrice;
        $itemOnSale = $this->stmbuy->itemOnSale();
        foreach ($itemPrice as $value) {
            $minPrice = $this->stmbuy->itemMinSalePrice($value['itemId']);
            if (!$minPrice['isMine'] && $minPrice['minPrice'] > $value['itemCostPrice']) {
                dump($itemOnSale[$value['itemId']]);
            }
        }
    }
}