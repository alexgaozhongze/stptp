<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 18-5-2
 * Time: 上午11:33
 */
namespace App\Http\Controllers;
use App\Stmbuy;
use Illuminate\Support\Facades\DB;

class StmbuyController extends Controller
{
    private $stmbuy;
    private $itemPrice = [
        [
            'itemId' => '2451526758',
            'itemCostPrice' => '10.50'
        ],
        [
            'itemId' => '2662539730',
            'itemCostPrice' => '3.50'
        ],
        [
            'itemId' => '2662539761',
            'itemCostPrice' => '0.50'
        ],
        [
            'itemId' => '2662539708',
            'itemCostPrice' => '0.50'
        ]
    ];

    public function __construct()
    {
        $this->stmbuy = new Stmbuy();
    }

    public function autoSale()
    {
        $itemPrice = $this->itemPrice;
        $itemOnSale = $this->stmbuy->itemOnSale();
        foreach ($itemPrice as $ipv) {
            $minPrice = $this->stmbuy->itemMinSalePrice($ipv['itemId']);
            if ($minPrice['needChange'] && $minPrice['minPrice'] > $ipv['itemCostPrice']) {
                $data = [];
                foreach ($itemOnSale[$ipv['itemId']]['goodIds'] as $iosv) {
                    $data[] = [
                        'id' => $iosv,
                        'price' => $minPrice['minPrice'] * 100
                    ];
                }
                $this->stmbuy->changePrice($data);
            }
        }
    }

}