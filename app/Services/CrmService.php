<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CrmService
{
    public string $token = '';

    public string $server;

    public string $login;

    public string $password;

    public function __construct()
    {
        $this->server = env('CRM_SERVER');
        $this->login = env('CRM_LOGIN');
        $this->password = env('CRM_PASSWORD');
    }

    public function login()
    {
        $response = Http::get($this->server.'/resto/api/auth?login='.$this->login.'&pass='.sha1($this->password));
        if ($response->status() === 200) {
            $token = Token::first();
            if ($token) {
                $token->token = $response->body();
                $token->save();
            } else {
                Token::create([
                    'token' => $response->body(),
                ]);
            }

            return true;
        }

        return false;
    }


    public function logout()
    {
        $token = Token::first();
        $response = Http::get($this->server.'/resto/api/logout?key='.$token->token);

        if ($response->status() === 200) {
            $token->delete();

            return true;
        } elseif ($response->status() === 401) {
            return true;
        } else {
            return false;
        }
    }

    public function getOrders()
    {
        $token = Token::first();
        $str = $this->server.'/resto/api/v2/reports/olap?key='.$token?->token;
        $response = Http::post($str, [
            "reportType" => "SALES",
            "buildSummary" => "false",
            "groupByRowFields" => [
                "OrderNum",
                "OpenDate",
                "FiscalChequeNumber",
                "UniqOrderId.Id",
                "HourOpen",
            ],
            "groupByColFields" => [
            ],
            "aggregateFields" => [
                "DishDiscountSumInt",
            ],
            "filters" => [
                "OpenDate.Typed" => [
                    "filterType" => "DateRange",
                    "periodType" => "CUSTOM",
                    "from" => Carbon::today()->toDateString(),
                    "to" => Carbon::today()->toDateString(),
                    "includeLow" => "true",
                    "includeHigh" => "true",
                ],
                "HourOpen" => [
                    "filterType" => "IncludeValues",
                    "values" => [(string) (Carbon::now()->hour + 2)],
                ],
            ],
        ]);
        dump($response->body());
        return json_decode($response->body());
    }

    public function saveOrders($body)
    {
        $id = "UniqOrderId.Id";
        foreach ($body->data as $crm_order){
            $order = Order::where('unique_order_id', $crm_order->$id)->first();
            if(!$order){
                $order = new Order();
                $order->unique_order_id = $crm_order->$id;
                $order->order_id = $crm_order->OrderNum;
                $order->open_date = $crm_order->OpenDate;
                $order->open_hour = $crm_order->HourOpen;
                $order->total = $crm_order->DishDiscountSumInt;
                $order->check_number = $crm_order->FiscalChequeNumber;
                $order->save();
            }
        }
    }

    public function handle()
    {
        if ($this->login()) {
            $body = $this->getOrders();
            dump($body);
            $this->saveOrders($body);
            $this->logout();

            return true;
        }

        return false;
    }
}
