<?php
namespace App\Http\Controllers;
trait HelperMethods
{
    public function success($msg="ok", $data=null, $status=200)
    {
        return response()->json(['message' => $msg, 'data' => $data], $status);
    }

    public function fail($msg="not found", $status=404)
    {
        return response()->json(['errors' => $msg], $status);
    }
}
