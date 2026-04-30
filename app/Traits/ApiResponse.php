<?php
namespace App\Traits;


trait ApiResponse{
     public function success($data = [], $msg = "Success")
    {
        return response()->json([
            'status' => true,
            'message' => $msg,
            'data' => $data
        ]);
    }

    public function error($msg = "Error", $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $msg
        ], $code);
    }

}