<?php
/**
 * Stmbuy 登录
 * User: alex
 * Date: 18-4-25
 * Time: 上午10:25
 */

namespace App;

class Stmbuy
{
    private static $cookieFiles;

    public function __construct($autoLogin = true)
    {
        self::$cookieFiles = public_path().'/cookie/.stmbuy.cookie';
        $autoLogin && self::autoLogin();
    }

    private function loginPost($url, $post)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_COOKIEJAR, self::$cookieFiles);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_exec($ch);
        curl_close($ch);
    }

    private function getContent($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, self::$cookieFiles);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function curlLogin()
    {
        $url = "http://api.stmbuy.com/member/login.json";
        $post = [
            'username' => 'johnnyalex',
            'password' => '7b0b4ff311fe586cad39f5a77fab3bdc',
            'hmackey' => 'eollt0hj994',
            'keep' => 0
        ];
        self::loginPost($url, $post);
//        self::getContent('http://www.stmbuy.com/logineddata.json');
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

    public function autoLogin()
    {
        $userInfo = self::curlLoginData();
        isset($userInfo['status']) && (false == $userInfo['status']) && self::curlLogin();
    }

    public function itemMinSalePrice($itemId, $page = 1)
    {
        $html = self::curlItemIndex($itemId, $page);
        $matchAry = [];
        preg_match('/(<li).*?(\/li)/is', $html, $matchAry);
        $matchStr = reset($matchAry);
        preg_match("/data-price=\"\d+/", $matchStr, $matchAry);
        $matchPrice = preg_replace('/data-price="/', '', reset($matchAry));
        $minPrice = $matchPrice / 100;
        preg_match('/(title=").*?(">)/is', $html, $matchAry);
        $matchName = preg_replace(['/(title=")/', '/">/'], '', reset($matchAry));
        $isMine = false;
        if ('alexgaozhongze' !== $matchName) {
            $minPrice -= 0.01;
        } else {
            $isMine = true;
        }
        return [
            'minPrice' => $minPrice,
            'isMine' => $isMine
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
            preg_match('/(\'\[").*?(item-\d+)/is', $value, $matchAry);
            $matchStr = reset($matchAry);
            $itemId = preg_replace(['/(\').*?(\')/', '/[\r\n\s]/', '/data-goodslink="\/pubg\/item-/'], '', $matchStr);
            $goodId = preg_replace(['/\'/', '/[\r\n\s]/', '/data-goodslink="\/pubg\/item-\d+/'], '', $matchStr);
            $goodAry = json_decode($goodId, true);
            $onSaleItem[$itemId] = $goodAry;
        }
        return $onSaleItem;
    }

    public function checkItemSalePrice()
    {
        $html = self::curlOnSaleGoods();
        echo $html;
        dump($html);

    }
}