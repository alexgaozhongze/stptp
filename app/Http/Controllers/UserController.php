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

    public function showProfile()
    {
        $this->stmbuy->login();
    }

    public function logindata()
    {
        echo $this->stmbuy->loginData();
    }

    public function index()
    {
        echo $this->stmbuy->index();
    }

    public function my()
    {
        echo $this->stmbuy->my();
    }
}