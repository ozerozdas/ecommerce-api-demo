<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Discount;

class ApiController extends Controller {
    protected $pageStatus = 404;
    protected $result = ['status' => false];

    public function listingOrder(){
        $result = [];
        $orders = \DB::table('orders')
            ->leftJoin('customers', 'customers.id' , '=', 'orders.customerId')
            ->get();
        foreach($orders as $key => $order){
            $productDetails = [];
            $items = json_decode($order->items);
            foreach($items as $i => $line){
                $productInfo = \DB::table('products')->where('id', $line->productId)->first();
                $productDetails[$i] = (array) $line;
                $productDetails[$i]['name'] = $productInfo->name;
                $productDetails[$i]['category'] = $productInfo->category;
            }
            $result[$key] = [
                'id' => $order->id,
                'customerId' => $order->customerId,
                'customerName' => $order->name,
                'customerCreateDate' => $order->since,
                'items' => $productDetails,
                'total' => $order->total
            ];
        }
        if(!empty($result)){
            $this->result = [
                'status' => true,
                'result' => $result
            ];
            $this->pageStatus = 200;
        }
        return response()->json($this->result, $this->pageStatus);
    }

    public function saveOrder(Request $request){
        $valid = true;
        if(gettype($request->get('items', null)) !== 'array' || gettype($request->get('customerId', null)) !== 'integer'){
            $valid = false;
            $this->result = [
                'status' => false,
                'message' => 'Invalid payload'
            ];
            $this->pageStatus = 500;
        }
        
        if($valid){
            foreach($request->get('items') as $item){
                $productInfo = \DB::table('products')->where('id', $item['productId'])->first();
                if($item['quantity'] > $productInfo->stock){
                    $this->result = [
                        'status' => false,
                        'message' => 'There is not enough stock. Product Name: ' . $productInfo->name
                    ];
                    $this->pageStatus = 500;
                    $valid = false;
                }
            }
        }

        try {
            if($valid){
                $total = array_sum(array_column($request->get('items'), 'total'));
                \DB::table('orders')->insert([
                    'customerId' => $request->get('customerId'),
                    'items' => json_encode($request->get('items')),
                    'total' => $total
                ]);
                $this->result = [
                    'status' => true,
                    'message' => 'Order successfully saved!'
                ];
                $this->pageStatus = 200;
            }
        } catch (\Exception $e) {
            $this->result = [
                'status' => false,
                'message' => $e->getMessage()
            ];
            $this->pageStatus = 500;
        }
        return response()->json($this->result, $this->pageStatus);
    }

    public function deleteOrder(Request $request){
        $id = $request->get('id', null);
        if(gettype($id) !== 'integer'){
            $this->result = [
                'status' => false,
                'message' => 'Invalid payload'
            ];
            $this->pageStatus = 500;
        }else{
            try{
                \DB::table('orders')->where('id', $id)->delete();
                $this->result = [
                    'status' => true,
                    'message' => 'Order successfully deleted!'
                ];
                $this->pageStatus = 200;
            } catch (\Exception $e) {
                $this->result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
                $this->pageStatus = 500;
            }
        }
        return response()->json($this->result, $this->pageStatus);
    }

    public function discountCalculator($id){
        $order = \DB::table('orders')
            ->leftJoin('customers', 'customers.id' , '=', 'orders.customerId')
            ->where('orders.id', $id)
            ->first();
        $productDetails = [];
        $items = json_decode($order->items);
        foreach($items as $i => $line){
            $productInfo = \DB::table('products')->where('id', $line->productId)->first();
            $productDetails[$i] = (array) $line;
            $productDetails[$i]['name'] = $productInfo->name;
            $productDetails[$i]['category'] = $productInfo->category;
        }
        $result = [
            'id' => $order->id,
            'customerId' => $order->customerId,
            'customerName' => $order->name,
            'customerCreateDate' => $order->since,
            'items' => $productDetails,
            'total' => $order->total
        ];
        $discount = new Discount($result);
        $discountDetails = $discount->getDiscountDetails();
        $discountTotal = 0;
        if(!empty($discountDetails)){
            $discountTotal = array_sum(array_column($discountDetails, 'discountAmount'));
        }
        $this->result = [
            'status' => true,
            'result' => [
                'orderId' => $order->id,
                'discounts' => $discountDetails,
                'totalDiscount' => $discountTotal,
                'discountedTotal' => $order->total - $discountTotal
            ]
        ];

        return response()->json($this->result, $this->pageStatus);
    }
}
