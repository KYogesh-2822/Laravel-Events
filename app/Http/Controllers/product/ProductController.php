<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function  __construct(private ProductService $productService){

    }

    public function getProduct(){
        $product = $this->productService->getAllProduct();
        return response()->json([
            'product' => $product,
             'status' => true
        ]);
    }
}
