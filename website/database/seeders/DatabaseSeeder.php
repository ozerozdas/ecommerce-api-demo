<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->importProducts();
        $this->importCustomers();
        $this->importOrders();
    }

    function importProducts(){
        $params = [];
        $data = file_get_contents('https://raw.githubusercontent.com/ideasoft/se-take-home-assessment/master/example-data/products.json');
        $data = json_decode($data);
        foreach($data as $item){
            $params[] = [
                'id' => !empty($item->id) ? $item->id : null,
                'name' => !empty($item->name) ? $item->name : $item->description,
                'category' => !empty($item->category) ? $item->category : 0,
                'price' => !empty($item->price) ? $item->price : 0,
                'stock' => !empty($item->stock) ? $item->stock : 0,
            ];
        }
        if(!empty($params)){
            \DB::table('products')->truncate();
            \DB::table('products')->insert($params);
        }
    }

    function importCustomers(){
        $params = [];
        $data = file_get_contents('https://raw.githubusercontent.com/ideasoft/se-take-home-assessment/master/example-data/customers.json');
        $data = json_decode($data);
        foreach($data as $item){
            $params[] = [
                'id' => !empty($item->id) ? $item->id : 0,
                'name' => !empty($item->name) ? $item->name : '',
                'since' => !empty($item->since) ? $item->since : 0,
            ];
        }
        if(!empty($params)){
            \DB::table('customers')->truncate();
            \DB::table('customers')->insert($params);
        }
    }

    function importOrders(){
        $params = [];
        $data = file_get_contents('https://raw.githubusercontent.com/ideasoft/se-take-home-assessment/master/example-data/orders.json');
        $data = json_decode($data);
        foreach($data as $item){
            $params[] = [
                'id' => !empty($item->id) ? $item->id : null,
                'customerId' => !empty($item->customerId) ? $item->customerId : 0,
                'items' => !empty($item->items) ? json_encode($item->items) : '',
                'total' => !empty($item->total) ? $item->total : 0,
            ];
        }
        if(!empty($params)){
            \DB::table('orders')->truncate();
            \DB::table('orders')->insert($params);
        }
    }
}
