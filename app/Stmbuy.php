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
        self::$cookieFiles = public_path().'/cookie/.stmbuy.cookie'; //设置cookie保存的路径
    }

    private function loginPost($url, $post)
    {
        $ch = curl_init(); //初始化curl模块
        curl_setopt($ch, CURLOPT_URL, $url); //登录提交的地址
        curl_setopt($ch, CURLOPT_HEADER, 0); //是否显示头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0); //是否自动显示返回的信息
        curl_setopt($ch, CURLOPT_COOKIEJAR, self::$cookieFiles); //设置cookie信息保存在指定的文件夹中
        curl_setopt($ch, CURLOPT_POST, 1); //以POST方式提交
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));//要执行的信息
        curl_exec($ch); //执行CURL
        curl_close($ch);
    }

    private function getContent($url){
        $ch = curl_init(); //初始化curl模块
        curl_setopt($ch, CURLOPT_URL, $url); //登录提交的地址
        curl_setopt($ch, CURLOPT_HEADER, 0); //是否显示头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //是否自动显示返回的信息
        curl_setopt($ch, CURLOPT_COOKIEFILE, self::$cookieFiles);//设置cookie信息保存在指定的文件夹中
        $response = curl_exec($ch); //执行curl转去页面内容
        curl_close($ch);
        return $response; //返回字符串
    }

    public function login()
    {
        $url = "http://api.stmbuy.com/member/login.json"; //登录地址， 和原网站一致
        $post = [
            'username' => 'johnnyalex',
            'password' => '4d8d0415c811fe4ca0f61a5e6847282f',
            'hmackey' => '5eb7qhbxl8w',
            'keep' => 0
        ];
        self::loginPost($url, $post); //调用模拟登录
        self::getContent('http://www.stmbuy.com/logineddata.json');
    }

    public function loginData()
    {
        return self::getContent('http://www.stmbuy.com/logineddata.json');
    }

    public function index()
    {
        return self::getContent('http://www.stmbuy.com/');
    }

    public function my()
    {
        return self::getContent('http://www.stmbuy.com/my');
    }
}