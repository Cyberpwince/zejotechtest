<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function createtrx(){
        $str = "ze".rand(10000,99999);

        return $str;
    }
}
