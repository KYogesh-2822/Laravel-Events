<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Facades\Product;


class ProductController extends Controller
{
    use ApiResponse; 
    public function  __construct(private ProductService $productService){

    }

    public function getProduct(){
        // $product = $this->productService->getAllProduct(); // with dependency injection we ca call serivce like this
        $product = Product::getAllProduct(); // without dependency inject with th help of facade we can call the service method like this
        return $this->success([
             'product' => $product,
        ], 'Products get successfully');
        // return response()->json([
        //     'product' => $product,
        //      'status' => true
        // ]);
    }
}
