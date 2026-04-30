<?php
namespace App\Services;

class ProductService 
{
    public function getAllProduct(){
        return [
        ['one'=>'first','val'=>2],
        ['two'=>'second','val'=>4],
        ];
    }
}