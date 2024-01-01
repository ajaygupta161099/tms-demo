<?php

namespace App\Helpers;

use Error;
use ErrorException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class Helper
{

    public static function updateUserLog($input = [])
    {
        if (env('CUSTOM_LOG')) {
            try {
                $log = array(
                    'user_id' => $input['user_id'] ?? 0,
                    'user_name' => $input['user_name'] ?? '',
                    'parent' => $input['parent'] ?? '',
                    'parent_id' => $input['parent_id'] ?? '',
                    'action' => $input['action'] ?? '',
                    'request' => $input['request'] ? serialize($input['request']) : [],
                    'response' => $input['response'] ? serialize($input['response']) : [],
                    'status' => $input['response'] ? $input['response']['status'] : [],
                    'ip_address' => $input['ip_address'] ?? '',
                    'browser' => $input['browser'] ?? ''
                );

                DB::table('user_logs')->insert($log);
            } catch (ErrorException | Error $e) {
                $status = false;
                $statusCode = 500;
                $error['code'] = '';
                $error['title'] = 'INTERNAL_ERROR';
                $error['description'] = 'Syntax Error.';
                $response = [];

                return response()->json(["status" => $status, "statusCode" => $statusCode, "error" => $error, "response" => $response], $statusCode);
            } catch (QueryException $e) {
                $status = false;
                $statusCode = 502;
                $error['code'] = '';
                $error['title'] = 'DB_ERROR';
                $error['description'] = 'SQL Syntax Error.';
                $error['message'] = $e->getMessage();
                $response = [];

                return response()->json(["status" => $status, "statusCode" => $statusCode, "error" => $error, "response" => $response], $statusCode);
            }
        }
    }

    public static function logs($request)
    {
        $logs = DB::table('user_logs')->where('parent', $request->parent)
            ->where('parent_id', $request->parent_id)
            ->get();

        return $logs;
    }
}
