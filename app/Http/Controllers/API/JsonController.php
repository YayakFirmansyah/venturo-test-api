<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JsonController extends Controller
{
    public function getMenu()
    {
        $jsonContent = file_get_contents("http://tes-web.landa.id/intermediate/menu");
        echo $jsonContent;
    }

    public function getAllTransaksi($tahun)
    {
        $jsonContent = file_get_contents("https://tes-web.landa.id/intermediate/transaksi?tahun=" . $tahun);
        echo $jsonContent;
    }
}
