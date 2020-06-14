<?php

use App\Payment_type;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;
use App\Mail\StatsMail;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

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
Route::get('/', 'PaymentController@index')->middleware('auth');;

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Route::get('/payments', 'PaymentController@index')->name('payments.show')->middleware('auth');
//Route::get('/payments/create', 'PaymentController@create')->middleware('auth');
Route::get('/payments/{id}', 'PaymentController@show')->middleware('auth');
Route::post('/payments/store', 'PaymentController@store')->middleware('auth');
Route::delete('/payment/{payment}', 'PaymentController@destroy')->middleware('auth');
Route::delete('/payment/delete/{id}', 'PaymentController@destroyById')->middleware('auth');
Route::post('/payments/update/{id}', "PaymentController@update")->middleware('auth');

Route::get('/apigroupedpayments', 'PaymentController@groupPayments')->middleware('auth');
Route::get('/apipayments', 'PaymentController@getPaymentsForTable')->middleware('auth');
Route::get('/apipayments/stats', 'PaymentController@stats')->middleware('auth');

Route::get('/apicarts', function(){
    return Auth::user()->carts;
})->middleware('auth');

Route::get('/carts', 'CartController@index')->name('carts.show')->middleware('auth');
Route::post('/carts/update/{id}', "CartController@update")->middleware('auth');
Route::post('/carts/add', "CartController@add")->middleware('auth');

Route::get('/user', 'UserController@index')->name('user.settings')->middleware('auth');

Route::get('/apilinechartpayments', 'PaymentController@lineChartPayments')->middleware('auth');
Route::get('/apipiechartpayments', 'PaymentController@pieChartPayments')->middleware('auth');
Route::get('/stats', function() {
    $payment_types=\App\Payment_type::all();
    return view('stats.index', compact('payment_types'));
})->name('stats.show')->middleware('auth');
Route::post('/apiedituser', 'UserController@editUser')->middleware('auth');
Route::post('/apidestroydata', 'UserController@destroyPaymentsCarts')->middleware('auth');
Route::post('/apidestroyuser', 'UserController@destroyUser')->middleware('auth');
Route::get('/apipaymenttypes', 'PaymentTypeController@index');

Route::get('/periodic', 'PeriodicPaymentController@index')->name('periodic.show')->middleware('auth');
Route::post('/periodic/store', 'PeriodicPaymentController@store')->middleware('auth');
Route::delete('/periodic/delete/{id}', 'PeriodicPaymentController@destroyById')->middleware('auth');
Route::post('/periodic/update/{id}', "PeriodicPaymentController@update")->middleware('auth');
Route::get('/apitodaypayments', 'PeriodicPaymentController@todayPayments')->middleware('auth');
Route::post('/periodic/storePayment', 'PeriodicPaymentController@storePayment')->middleware('auth');
Route::get('/apibalance', 'PaymentController@balance')->middleware('auth');

Route::get('/email', function() {
    //kiekvienam useriui mailas
    Mail::to('miglezub@gmail.com')->send(new StatsMail(2));
    //return new StatsMail(2);
});

Route::get('/sendEmail', function() {
    $users = User::all();
    foreach($users as $user) {
        if($user->subscription == true)
            Mail::to($user->email)->send(new StatsMail($user->id));
    }
});

//Route::get('/seedtypes', 'PaymentTypeController@seedPaymentTypes');
Route::get('/seedpayments', 'PaymentController@seedPayments');
