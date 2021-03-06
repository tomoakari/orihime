<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\Export;
use App\Calendar;



class ManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('management');
    }


    /**
     * エクセル出力テスト
     */
    public function export_()
    {
        $calendars = Calendar::get();

        $view  = \view('exporttest', compact('calendars'));
        return \Excel::download(new Export($view), 'exp.xlsx');
    }

    /**
     * PDF出力
     */
    public function export(Request $request)
    {
        /*
        // 例
        $calendars = Calendar::get();
        $view      = \view('exporttest', compact('calendars'));
        $pdf       = \PDF::loadHTML($view)->setPaper('b4', 'landscape');
        // return $pdf->stream('title.pdf');
        return $pdf->download('title.pdf');
        */

        $calendarInt = array(
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
            21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
        
        $exportDate = date('Y年m月d日');

        // 検索結果を取得
        $results = app('ManagementService')->getOrderList($request);

        // 出力する元のHTMLビューの設定
        $view  = \view('export_management', compact('results', 'calendarInt', 'exportDate'));
        return $view;
        // 出力するPDFの設定
        //$pdf = \PDF::loadHTML($view)->setPaper('b4', 'landscape'); 

        // ブラウザにPDFを直接表示させたい場合
        //return $pdf->stream('title.pdf');

        


        // ※ちなみに直接ダウンロードさせたい場合はこちら
        // return $pdf->download('title.pdf');
    }
}
