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
        $this->stmbuy = new Stmbuy(true);
    }

    public function index()
    {
        echo $this->stmbuy->curlIndex();
    }

    public function test()
    {
        $this->stmbuy->csrfToken();
    }

    public function htmlTest()
    {
        echo $this->stmbuy->csrfToken();
    }

    public function autoSale()
    {
        $itemPrice = $this->itemPrice;
        $itemOnSale = $this->stmbuy->itemOnSale();
        foreach ($itemPrice as $value) {
            $minPrice = $this->stmbuy->itemMinSalePrice($value['itemId']);
            if (!$minPrice['isMine'] && $minPrice['minPrice'] > $value['itemCostPrice']) {
                $data = [];
                foreach ($itemOnSale[$value['itemId']]['goodIds'] as $value) {
                    $data[] = [
                        'id' => $value,
                        'price' => $minPrice['minPrice'] * 100
                    ];
                }
                $this->stmbuy->changePrice($data);
            }
        }
    }
}