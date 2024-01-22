<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected array $data = [];
    protected array $menuTotal = [];
    protected array $bulanTotal = [];
    protected array $subBulanTotal = [];
    protected array $kategoriTotal = [];
    protected string $finalTotal = '';

    public function index(Request $request)
    {
        // Get tahun from request
        $tahun = $request['tahun'];

        // If tahun is not null, get transaksi data from API
        if ($tahun != null) {
            $this->getTransaksi($tahun);

            // dd($this->kategoriTotal);
            // dd($this->subBulanTotal);

            return view('index', [
                'tahun' => $tahun,
                'data' => $this->data,
                'menuTotal' => $this->menuTotal,
                'bulanTotal' => $this->bulanTotal,
                'finalTotal' => $this->finalTotal,
                'subBulanTotal' => $this->subBulanTotal,
                'kategoriTotal' => $this->kategoriTotal,
            ]);
        }

        // Else, return index view
        return view('index');
    }

    public function getTransaksi($tahun)
    {
        // Fetch data from API
        $menu = json_decode(file_get_contents("https://tes-web.landa.id/intermediate/menu"), true);
        $transaksi = json_decode(file_get_contents("https://tes-web.landa.id/intermediate/transaksi?tahun=" . $tahun), true);

        foreach ($menu as $value) {
            $this->menuTotal[$value['menu']] = 0;

            for ($i = 1; $i <= 12; $i++) {
                $this->data[$value['kategori']][$value['menu']][$i] = 0;
                $this->bulanTotal[$i] = 0;

                $this->subBulanTotal[$value['kategori']][$i] = 0;
            }
        }

        // Map transaksi to data, menuTotal, and bulanTotal
        foreach ($transaksi as $value) {
            $kategori = $this->getKategori($value['menu']);
            $bulan = substr($value['tanggal'], 5, 2);
            $bulan = ltrim($bulan, '0');

            $this->data[$kategori][$value['menu']][$bulan] += $value['total'];
            $this->menuTotal[$value['menu']] += $value['total'];
            $this->bulanTotal[$bulan] += $value['total'];

            // Map subBulanTotal
            $this->subBulanTotal[$kategori][$bulan] += $value['total'];
        }

        // dd($this->data, $this->subBulanTotal);

        // Sum subBulanTotal to kategoriTotal
        foreach ($this->subBulanTotal as $key => $value) {
            $this->kategoriTotal[$key] = array_sum($value);
        }

        // dd($this->kategoriTotal);

        // Reformat data
        $this->reformatData();
    }

    public function reformatData()
    {
        // Reformat menuTotal from number to string
        $this->menuTotal = array_map(function ($value) {
            return number_format($value);
        }, $this->menuTotal);

        // Reformat final total from number to string
        $this->finalTotal = number_format(array_sum($this->bulanTotal));

        // Reformat subBulanTotal from number to string
        foreach ($this->subBulanTotal as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if ($value2 == 0) {
                    $this->subBulanTotal[$key][$key2] = '';
                    continue;
                }
                $this->subBulanTotal[$key][$key2] = number_format($value2);
            }
        }

        // Reformat ketegoriTotal from number to string
        $this->kategoriTotal = array_map(function ($value) {
            return number_format($value);
        }, $this->kategoriTotal);

        // Reformat bulanTotal from number to string
        $this->bulanTotal = array_map(function ($value) {
            return $value != null ? number_format($value) : '';
        }, $this->bulanTotal);

        // Reformat data from number to string
        foreach ($this->data as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $this->data[$key][$key2][$key3] = $value3 != null ? number_format($value3) : '';
                }
            }
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
}
