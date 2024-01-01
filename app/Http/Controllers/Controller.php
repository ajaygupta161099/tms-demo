<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public $user_id = 0;
    public $user_name = '';
    public $user_type = 0;
    public $module_id = 0;
    public $submodule_id = 0;
    public $browser = '';
    public $ip_address = '';
    public $company_code = '';

    public $action = '';
    public $status = true;
    public $statusCode = 200;
    public $error = array();
    public $response = array();

    public function __construct(){
        $this->middleware(function ($request, $next) {
            $user = \Auth::user();
            $this->user_id = ($user)?$user->id:0;
            $this->user_type = ($user->user_type)??0;
            $this->company_id = ($user->company_id)??0;
            $this->company_code = $request->header('Company-Code');
            $this->subcompany_code = $request->header('Sub-Company-code');
            $this->module_id = $request->header('Module-Id');
            $this->submodule_id = $request->header('Sub-Module-Id');

            return $next($request);
        });
    }
}
