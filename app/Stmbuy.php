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

    public function __construct()
    {
        self::$cookieFiles = public_path().'/cookie/.stmbuy.cookie';
        self::autoLogin();
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
            'password' => '4d8d0415c811fe4ca0f61a5e6847282f',
            'hmackey' => '5eb7qhbxl8w',
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

    public function autoLogin()
    {
        $userInfo = self::curlLoginData();
        isset($userInfo['status']) && (false == $userInfo['status']) && self::curlLogin();
    }

    public function itemMinSalePrice($itemId)
    {
        $html = self::curlItemIndex($itemId, 1);
        $matchAry = [];
//        preg_match_all("/data-goodsid=\"\d+\"[\s\S]{1}data-price=\"\d+\"/", $html, $matchAry);
        preg_match("/data-price=\"\d+/", $html, $matchAry);
        $matchAry = preg_replace("/data-price=\"/", '', $matchAry);
        $minPrice = false;
        is_array($matchAry) && $minPrice = (int) reset($matchAry);
        return $minPrice;
    }
}