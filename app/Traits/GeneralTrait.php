<?php
namespace App\Traits;

use Illuminate\Database\QueryException;

trait GeneralTrait
{

 

    public function returnError($responseCode, $msg)
    {
        return response()->json([
            'status' => false,
            'responseCode' => $responseCode,
            'msg'    => $msg
        ]);
    }
    public function returnSuccessMessage($responseCode, $msg)
    {
        return response()->json([
            'status' => true,
            'responseCode' => $responseCode,
            'msg'    => $msg
        ]);
    }
    public function returnData($responseCode, $msg, $key, $value)
    {
        return response()->json([
            'status' => true,
            'responseCode' => $responseCode,
            'msg'    => $msg,
            $key   => $value,
        ]);
    }
    public function returnValidations($responseCode, $validator)
    {
        return response()->json([
            'status' => false,
            'responseCode' => $responseCode,
            'validation'    => $validator->errors(),
        ]);
    }
}
