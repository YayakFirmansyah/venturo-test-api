<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected array $data = [];
    protected array $totalMenu = [];
    protected array $totalBulan = [];
    protected string $totalBulanText = '';

    public function index(Request $request)
    {
        $tahun = $request['tahun'];
        if ($tahun != null) {
            $this->transaksiDetail($tahun);

            return view('index', [
                'tahun' => $tahun,
                'data' => $this->data,
                'menuTotal' => $this->totalMenu,
                'bulanTotal' => $this->totalBulan,
                'totalBulanText' => $this->totalBulanText,
            ]);
        }
        return view('index');
    }

    public function transaksiDetail($tahun)
    {
        $menu = json_decode(file_get_contents("https://tes-web.landa.id/intermediate/menu"), true);
        $transaksi = json_decode(file_get_contents("https://tes-web.landa.id/intermediate/transaksi?tahun=" . $tahun), true);

        foreach ($menu as $value) {
            $this->totalMenu[$value['menu']] = 0;

            for ($i = 1; $i <= 12; $i++) {
                $this->data[$value['kategori']][$value['menu']][$i] = 0;
                $this->totalBulan[$i] = 0;
            }
        }

        foreach ($transaksi as $value) {
            $kategori = $this->getKategori($value['menu']);
            $bulan = substr($value['tanggal'], 5, 2);
            $bulan = ltrim($bulan, '0');

            $this->data[$kategori][$value['menu']][$bulan] += $value['total'];
            $this->totalMenu[$value['menu']] += $value['total'];
            $this->totalBulan[$bulan] += $value['total'];
        }

        foreach ($this->totalMenu as $key => $value) {
            $this->totalMenu[$key] = number_format($value);
        }

        $this->totalBulanText = number_format(array_sum($this->totalBulan));

        foreach ($this->totalBulan as $key => $value) {
            $this->totalBulan[$key] =  $value != null ? number_format($value) : '';
        }
    }

    public function getKategori($menu)
    {
        foreach ($this->data as $key => $value) {
            if (isset($this->data[$key][$menu])) {
                return $key;
            }
        }

        return null;
    }


    public function menu()
    {
        $jsonContent = file_get_contents("http://tes-web.landa.id/intermediate/menu");
        echo $jsonContent;
    }

    public function transaksi($tahun)
    {
        $jsonContent = file_get_contents("https://tes-web.landa.id/intermediate/transaksi?tahun=" . $tahun);
        echo $jsonContent;
    }
}
