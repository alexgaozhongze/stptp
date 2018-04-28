<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 18-4-28
 * Time: 下午4:27
 */
namespace App\Http\Controllers;
use App\Stmbuy;

class ItemController extends Controller
{
    private $stmbuy;

    public function __construct()
    {
        $this->stmbuy = new Stmbuy();
    }

    public function index()
    {
        $minPrice = $this->stmbuy->itemMinSalePrice('2451373801');
        dump($minPrice);
        die;

        return;
        $itemId = '2451373801';
        $page = 1;
        $html = $this->stmbuy->curlItemIndex($itemId, $page);
//        dump($html);
        $matchAry = [];
//        preg_match_all
//        preg_match_all("/data-goodsid=\"\d+[\s\S]{1}/", $html, $matchAry);
        preg_match_all("/data-goodsid=\"\d+\"[\s\S]{1}data-price=\"\d+\"/", $html, $matchAry);
        dump($matchAry);
    }
}