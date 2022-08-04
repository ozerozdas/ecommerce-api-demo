<?php
namespace App\Helpers;

class Discount {
    protected $result = [];
    protected $order;

    // Types: BUY_5_GET_1 | 10_PERCENT_OVER_1000 | BUY_2_GET_20_PERCENT_FOR_CHEAPEST

    public function __construct($order){
        $this->order = $order;
    }

    public function getDiscountDetails(){
        $categoryDetails = [];
        foreach(array_column($this->order['items'], 'category') as $category){
            $categoryDetails[$category] = !empty($categoryDetails[$category]) ? $categoryDetails[$category] + 1 : 1;
        }

        // 10_PERCENT_OVER_1000
        if($this->order['total'] > 1000){
            $this->result[] = [
                'discountReason' => "10_PERCENT_OVER_1000",
                'discountAmount' => (double) $this->order['total'] / 10,
                'subtotal' => $this->order['total'] - ($this->order['total'] / 10)
            ];
        }
        

        foreach($this->order['items'] as $item){
            // BUY_5_GET_1
            if($item['category'] == 2 and $item['quantity'] >= 6){
                $this->result[] = [
                    'discountReason' => "BUY_5_GET_1",
                    'discountAmount' => (double) $item['unitPrice'],
                    'subtotal' => $this->order['total'] - $item['unitPrice']
                ];
            }
        }

        // BUY_2_GET_20_PERCENT_FOR_CHEAPEST
        if(!empty($categoryDetails['1'])){
            if($categoryDetails['1'] >= 2){
                $product = $this->findCheapestProduct($this->order['items']);
                $this->result[] = [
                    'discountReason' => "BUY_2_GET_20_PERCENT_FOR_CHEAPEST",
                    'discountAmount' => (double) $product['unitPrice'] / 5,
                    'subtotal' => $this->order['total'] - ($product['unitPrice'] / 5)
                ];
            }
        }
        
        return $this->result;
    }

    function findCheapestProduct(array $items){
        $prices = array_column($items, 'unitPrice');
        return $items[array_search(min($prices), $prices)];
    }
    function findMostExpensiveProduct(array $items){
        $prices = array_column($items, 'unitPrice');
        return $items[array_search(max($prices), $prices)];
    }
}