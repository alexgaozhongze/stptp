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
        self::$cookieFiles = public_path() . '/cookie/.stmbuy.cookie';
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

    private function getContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, self::$cookieFiles);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function postData($url, $post)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
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

    public function csrfToken()
    {
        $html = self::curlOnSale();
        $matchAry = [];
        preg_match('/(csrfToken).*?(;)/', $html, $matchAry);
        $matchToken = preg_replace(['/csrfToken[\s]=[\s]/', '/[\';]/'], '', reset($matchAry));
        return $matchToken;
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
        $csrfToken = self::csrfToken();
        $data = [
            'csrf_token' => $csrfToken,
            'param' => $param
        ];
        self::postData('http://api.stmbuy.com/member/item/changeprice.json', $data);
    }

    public function checkItemSalePrice()
    {
        $html = self::curlOnSaleGoods();
        echo $html;
        dump($html);

    }
}