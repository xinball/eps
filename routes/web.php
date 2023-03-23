<?php

use App\Entity\User;
use Illuminate\Support\Facades\Route;

use App\Entity\Problem;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    //return view('welcome');
    //return Problem::all();
});*/

//需要显示
Route::get('/','NoticeController@listview');
Route::get('/notice','NoticeController@listview');
Route::get('/notice/{nid}','NoticeController@indexview');

Route::get('/station','StationController@listview');
Route::get('/station/{sid}','StationController@indexview');

Route::get('/contest','ContestController@listview');
Route::get('/contest/{cid}','ContestController@indexview');

Route::get('/status','StatusController@listview');
Route::get('/status/{sid}','StatusController@indexview');

Route::get('/active','UserController@activeview');    //激活
Route::get('/forget','UserController@forgetview');    //忘记密码
Route::get('/user','UserController@settingview');
Route::get('/user/setting','UserController@settingview');
Route::get('/user/problem','UserController@problemview');
Route::get('/user/icontest','UserController@icontestview');
Route::get('/user/jcontest','UserController@jcontestview');
Route::get('/user/status','UserController@statusview');
Route::get('/user/tag','UserController@tagview');
Route::get('/user/{uid}','UserController@indexview');
Route::post('/user/upload','UserController@upload');             //上传

Route::get('/admin','AdminController@settingview');
Route::get('/admin/setting','AdminController@settingview');
Route::get('/admin/notice','AdminController@noticeview');
Route::get('/admin/tag','AdminController@tagview');
Route::get('/admin/station','AdminController@stationview');
Route::get('/admin/location','AdminController@locationview');
Route::get('/admin/appoint','AdminController@appointview');
Route::get('/admin/report','AdminController@reportview');
Route::get('/admin/user','AdminController@userview');


//服务，不显示
Route::group(['prefix'=>'service'],function (){
    Route::group(['prefix'=>'system'],function (){
        Route::get('/getaddr','SystemController@getaddr');
        Route::get('/getaddrlist','SystemController@getaddrlist');
        Route::get('/ping','SystemController@ping');
    });
    Route::group(['prefix'=>'user'],function (){
        Route::post('/login','UserController@login');
        Route::post('/register','UserController@register');
        Route::get('/active','UserController@active');
        Route::get('/forget','UserController@forget');
        Route::get('/logout','UserController@logout');

        Route::get('/get/{uid}','UserController@get');
        Route::post('/alter','UserController@alter');
        Route::post('/alterslogan','UserController@alterslogan');
        Route::post('/uploadavatar','UserController@uploadavatar');
        Route::post('/uploadbanner','UserController@uploadbanner');

    });
    Route::group(['prefix'=>'admin'],function (){
        Route::post('/login','AdminController@login');
        Route::get('/logout','AdminController@logout');
        Route::post('/alter/{config}','AdminController@alter');
        Route::get('/getuser',function (){
            return User::paginate(4)->withQueryString();
        });

        Route::group(['prefix'=>'user'],function (){
            Route::get('/get/{uid}','UserController@aget');
            Route::get('/getlist','UserController@agetlist');
        });
        Route::group(['prefix'=>'notice'],function (){
            Route::get('/get/{nid}','NoticeController@aget');
            Route::get('/getlist','NoticeController@agetlist');
            Route::get('/del/{nid}','NoticeController@del');
            Route::post('/insert','NoticeController@insert');
            Route::post('/alter/{nid}','NoticeController@alter');
            Route::get('/recover/{nid}','NoticeController@recover');
        });
        Route::group(['prefix'=>'station'],function (){
            Route::get('/get/{sid}','StationController@aget');
            Route::get('/getlist','StationController@agetlist');
            Route::post('/insert','StationController@insert');
            Route::get('/del/{sid}','StationController@del');
            Route::get('/recover/{sid}','StationController@recover');
            Route::post('/alter/{sid}','StationController@alter');
        });
        Route::group(['prefix'=>'location'],function (){
            Route::get('/get/{lid}','LocationController@aget');
            Route::get('/getlist','LocationController@agetlist');
            Route::post('/insert','LocationController@insert');
            Route::get('/del/{lid}','LocationController@del');
            Route::get('/recover/{lid}','LocationController@recover');
            Route::post('/alter/{lid}','LocationController@alter');
        });
        Route::group(['prefix'=>'appoint'],function (){
            Route::get('/get/{aid}','AppointController@aget');
            Route::get('/getlist','AppointController@agetlist');
            Route::get('/approve/{aid}','AppointController@approve');
            Route::post('/refuse/{aid}','AppointController@refuse');
        });
        Route::group(['prefix'=>'report'],function (){
            Route::get('/get/{rid}','ReportController@aget');
            Route::get('/getlist','ReportController@agetlist');
            Route::get('/approve/{rid}','ReportController@approve');
            Route::post('/refuse/{rid}','ReportController@refuse');
        });
    });
    Route::group(['prefix'=>'notice'],function (){
        Route::get('/get/{nid}','NoticeController@get');
        Route::get('/getlist','NoticeController@getlist');
    });
    Route::group(['prefix'=>'station'],function (){
        Route::get('/get/{sid}','StationController@get');
        Route::get('/getlist','StationController@getlist');
    });
    Route::group(['prefix'=>'location'],function (){
        Route::get('/get/{lid}','LocationController@get');
        Route::get('/getlist','LocationController@getlist');
    });
    Route::group(['prefix'=>'appoint'],function (){
        Route::get('/get/{aid}','AppointController@get');
        Route::get('/getlist','AppointController@getlist');
        Route::get('/del/{aid}','AppointController@del');
        Route::get('/recover/{aid}','AppointController@recover');
        Route::post('/insert','AppointController@insert');
        Route::post('/alter','AppointController@alter');
        Route::post('/cancel/{aid}','AppointController@cancel');
        Route::get('/apply/{aid}','AppointController@apply');
    });
    Route::group(['prefix'=>'report'],function (){
        Route::get('/get/{rid}','ReportController@get');
        Route::get('/getlist','ReportController@getlist');
        Route::get('/del/{rid}','ReportController@del');
        Route::get('/recover/{rid}','ReportController@recover');
        Route::post('/insert','ReportController@insert');
        Route::post('/alter','ReportController@alter');
        Route::post('/cancel/{rid}','ReportController@cancel');
        Route::get('/apply/{rid}','ReportController@apply');
    });


    Route::group(['prefix'=>'activity'],function (){
        Route::get('/get/{uid}/{aid}','ActivityController@get');
        Route::get('/getlist/{uid}','ActivityController@getlist');
    });
    Route::group(['prefix'=>'tag'],function (){
        Route::get('/get/{tid}','TagController@get');
        Route::get('/getlist','TagController@getlist');
        Route::get('/like/{tid}','TagController@like');
        Route::get('/dellike/{tid}','TagController@dellike');
    });
});
