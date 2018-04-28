<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 18-4-25
 * Time: 上午10:17
 */
namespace App\Http\Controllers;
use App\Stmbuy;

class UserController extends Controller
{
    private $stmbuy;

    public function __construct()
    {
        $this->stmbuy = new Stmbuy();
    }

    public function login()
    {
        $this->stmbuy->curlLogin();
    }

    public function logindata()
    {
        $userInfo = $this->stmbuy->curlLoginData();
        dump($userInfo);
    }

    public function index()
    {
        echo $this->stmbuy->curlIndex();
    }

    public function my()
    {
        echo $this->stmbuy->curlMy();
    }
}