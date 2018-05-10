<?php
/**
 * Stmbuy 登录
 * User: alex
 * Date: 18-4-25
 * Time: 上午10:25
 */

namespace App;

use Illuminate\Support\Facades\DB;

class Stmbuy
{
    private static $cookieFiles;
    private static $p_id;

    public function __construct($p_id)
    {
        self::$cookieFiles = public_path() . '/cookie/.stmbuy.cookie';
        self::$p_id = $p_id;
        self::autoLogin();
    }

    private function loginPost($url, $post)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, self::$cookieFiles);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_exec($ch);
        curl_close($ch);
    }

    private function getContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, self::$cookieFiles);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function postData($url, $post)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, self::$cookieFiles);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function curlLogin($data)
    {
        $url = "http://api.stmbuy.com/member/login.json";
        self::loginPost($url, $data);
    }

    public function curlLoginData()
    {
        $loginData = self::getContent('http://www.stmbuy.com/logineddata.json');
        return json_decode($loginData, true);
    }

    public function curlIndex()
    {
        return self::getContent('http://www.stmbuy.com/');
    }

    public function curlMy()
    {
        return self::getContent('http://www.stmbuy.com/my');
    }

    public function curlItemIndex($itemId, $page)
    {
        return self::getContent("http://www.stmbuy.com/pubg/item-$itemId/onsale&page=$page");
    }

    public function curlOnSale()
    {
        return self::getContent('http://www.stmbuy.com/my/backpack.html?tab=onsale');
    }

    public function curlOnSaleGoods()
    {
        return self::getContent('http://www.stmbuy.com/my/onsaleGoods.html?category_id=1793pv37tad5&list_mode=0&row=50&page=1');
    }

    public function curlItemGoods($c_id)
    {
        return self::getContent("http://www.stmbuy.com/my/itemgoods.html?category_id=$c_id&list_mode=0");
    }

    public function curlBackpack()
    {
        return self::getContent("http://www.stmbuy.com/my/backpack.html?tab=item");
    }

    public function curlBuyLog($keyword)
    {
        return self::getContent("http://www.stmbuy.com/my/buylog?keywords=$keyword");
    }

    public function login()
    {
        $username = 'johnnyalex';
        $password = 'alex333';
        $hmackey = base_convert(time(), 10, 33);
        $hamcPwd = md5($hmackey . md5($username . $password));
        $data = [
            'username' => $username,
            'password' => $hamcPwd,
            'hmackey' => $hmackey,
            'keep' => 0
        ];
        self::curlLogin($data);
    }

    public function autoLogin()
    {
        $userInfo = self::curlLoginData();
        isset($userInfo['status']) && ('error' == $userInfo['status']) && self::login();
    }

    public function csrfToken($html)
    {
        $matchAry = [];
        preg_match('/(csrfToken).*?(;)/', $html, $matchAry);
        $matchToken = preg_replace(['/csrfToken[\s]=[\s]/', '/[\';]/'], '', reset($matchAry));
        return $matchToken;
    }

    public function itemMinSalePrice($itemId, $page = 1)
    {
        $html = self::curlItemIndex($itemId, $page);
        $matchAry = [];
        preg_match_all('/(<li).*?(\/li)/is', $html, $matchAry);
        foreach (reset($matchAry) as $value) {
            preg_match("/data-price=\"\d+/", $value, $matchAry);
            $matchPrice = preg_replace('/data-price="/', '', reset($matchAry));
            preg_match('/(title=").*?(">)/is', $html, $matchAry);
            $matchName = preg_replace(['/(title=")/', '/">/'], '', reset($matchAry));
            if ('alexgaozhongze' != $matchName) {
                $needChange = true;
                $minPrice = $matchPrice / 100 - 0.01;
                break;
            } else {
                $needChange = false;
                $minPrice = $matchPrice / 100;
                break;
            }
        }
        return [
            'minPrice' => $minPrice,
            'needChange' => $needChange
        ];
    }

    public function itemOnSale()
    {
        $html = self::curlOnSaleGoods();
        $matchAry = [];
        $onSaleItem = [];
        preg_match('/(<ul[\s\S]{1}class="goods-list).*?(\/ul)/is', $html, $matchAry);
        preg_match_all('/(<li).*?(\/li)/is', reset($matchAry), $matchAry);
        foreach (reset($matchAry) as $value) {
            preg_match('/(data-id=").*?(item-\d+)/is', $value, $matchAry);
            $matchStr = reset($matchAry);
            $itemId = preg_replace(['/(data-id=").*?(]\')/', '/[\r\n\s]/', '/data-goodslink="\/pubg\/item-/'], '', $matchStr);
            $goodIds = preg_replace(['/(data-id=").*?(data-ids=)/', '/[\r\n\s]/', '/\'/', '/data-goodslink="\/pubg\/item-\d+/'], '', $matchStr);
            preg_match('/(data-id=").*?(")/', $matchStr, $matchAry);
            $goodId = preg_replace(['/data-id=/', '/"/'], '', reset($matchAry));
            $goodAry = json_decode($goodIds, true);
            $onSaleItem[$itemId] = [
                'goodId' => $goodId,
                'goodIds' => $goodAry
            ];
        }
        return $onSaleItem;
    }

    public function changePrice($param)
    {
        $html = self::curlOnSale();
        $csrfToken = self::csrfToken($html);
        $data = [
            'csrf_token' => $csrfToken,
            'param' => $param
        ];
        self::postData('http://api.stmbuy.com/member/item/changeprice.json', $data);
    }

    public function backpack($c_id)
    {
        $html = self::curlItemGoods($c_id);
        $matchAry = [];
        $backpackItem = [];
        preg_match('/(<ul[\s\S]{1}class="goods-list).*?(\/ul)/is', $html, $matchAry);
        preg_match_all('/(<li).*?(\/li)/is', reset($matchAry), $matchAry);
        foreach (reset($matchAry) as $value) {
            preg_match('/(data-id=").*?(item-\d+)/is', $value, $matchAry);
            $matchStr = reset($matchAry);
            $itemId = preg_replace('/(data-id=").*?(item-)/is', '', $matchStr);
            $goodIds = preg_replace(['/(data-id=").*?(\')/is', '/(\').*?(item-\d+)/is'], '', $matchStr);
            preg_match('/(data-id=").*?(")/', $matchStr, $matchAry);
            $goodAry = json_decode($goodIds, true);
            $backpackItem[$itemId] = $goodAry;
        }
        return $backpackItem;
    }

    public function putOn($param)
    {
        $html = self::curlBackpack();
        $csrfToken = self::csrfToken($html);
        $data = [
            'csrf_token' => $csrfToken,
            'param' => $param
        ];
        $response = self::postData('http://api.stmbuy.com/member/item/puton.json', $data);
        return json_decode($response, true);
    }

    public function itemCostPrice($gid, $pdtid)
    {
        $keyword = DB::table('good')
            ->where([['p_id', '=', self::$p_id], ['g_id', '=', $gid]])
            ->select('g_keyword')
            ->get()->toArray();
        $keyword = reset($keyword);
        $html = self::curlBuyLog($keyword->g_keyword);
        $matchAry = [];
        preg_match('/(<tbody>).*?(<\/tbody>)/is', $html, $matchAry);
        preg_match_all('/(<tr>).*?(<\/tr>)/is', reset($matchAry), $matchAry);
        $costPrice = [];
        $allCostPrice = [];
        foreach (reset($matchAry) as $value) {
            preg_match_all('/(<td).*?(<\/td>)/is', $value, $matchAry);
            $buyInfo = reset($matchAry);
            preg_match("/$gid/", $buyInfo[0], $matchAry);
            if ($matchAry) {
                preg_match_all("/\d+/", $buyInfo[3], $matchAry);
                $buyPrice = implode('.', reset($matchAry));
                $buyNum = preg_replace('/(<).*?(>)/is', '', $buyInfo[5]);
                for ($i = 0; $i < $buyNum; $i++) {
                    $allCostPrice[] = $buyPrice;
                }
            }
        }
        foreach ($pdtid as $key => $value) {
            $costPrice[$value] = $allCostPrice[$key];
        }
        return $costPrice;
    }

    public function autoSale($c_id)
    {
        self::backpackAutoSale($c_id);

    }

    private function backpackAutoSale($c_id)
    {
        $backpackItem = self::backpack($c_id);
        foreach ($backpackItem as $bik => $biv) {
            $minPrice = self::itemMinSalePrice($bik);
            $costPrice = self::itemCostPrice($bik, $biv);
            self::itemCostPriceSave($bik, $costPrice);
            $data = [];
            foreach ($biv as $bivv) {
                $data[] = [
                    'id' => $bivv,
                    'price' => $minPrice['minPrice'] * 100
                ];
            }
//            $response = self::putOn($data);
            $response = true;
            if (isset($response['errno']) && $response['errno']) {

            } else {
                self::itemSaleStatusSave($bik, $costPrice);
            }
        }
    }

    private function itemCostPriceSave($g_id, $costPrice)
    {
        $addData = [];
        foreach ($costPrice as $key => $value) {
            $data = [
                'p_id' => self::$p_id,
                'g_id' => $g_id,
                'pdt_id' => $key
            ];
            $checkExists = DB::table('product')->where($data)->get()->toArray();
            if ($checkExists) {
                $saveData = [
                    'pdt_cost_price' => $value,
                    'pdt_sale_status' => '0'
                ];
                DB::table('product')->where($data)->update($saveData);
            } else {
                $data['pdt_cost_price'] = $value;
                $addData[] = $data;
            }
        }
        $addData && DB::table('product')->insert($addData);
    }

    private function itemSaleStatusSave($g_id, $costPrice)
    {
        foreach ($costPrice as $key => $value) {
            $data = [
                'p_id' => self::$p_id,
                'g_id' => $g_id,
                'pdt_id' => $key
            ];
            DB::table('product')->where($data)->update(['pdt_sale_status' => 1]);
        }
    }
}