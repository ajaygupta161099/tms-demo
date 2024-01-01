<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use ErrorException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Zone;
use Error;
use App\Helpers\Helper;

class ZoneController extends Controller
{
    public function zoneCreate(Request $request)
{
    $id=$request->id??0;
    $this->action = $id ? 'update' : 'create';
    $validator = Validator::make($request->all(), [
        'zone' => 'required|string|max:50',
        'parent_zone' => 'required|string|max:20',
        'status' => 'required|integer|max:4',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json(['status' => false, 'statusCode' => 422, 'errors' => $validator->errors()]);
    }

    // Validation passed, proceed with updateOrCreate
    $validatedData = $validator->validate();

    $id = $request->input('id');
    $action = $id ? "updated" : "created" ;

    $response = Zone::updateOrCreate(['id' => $id], $validatedData);

    Helper::updateUserLog(["user_id"=>$this->user_id, "user_name"=>$this->user_name, "parent"=>"zone", "parent_id"=> "",
    "action"=>$this->action, "browser"=>$this->browser, "ip_address"=>$this->ip_address, "request"=>$request->all(), "response"=>$response ]);
    return response()->json(['status' => true, 'statusCode' => 200, 'error' => [], 'description' => 'Zone '.$action.' successfully']);
}

    public function getZoneById(Request $request, $id)
    {
        try {
            $zone = Zone::findOrFail($id);

            return response()->json(['success' => true, 'status' => 200, 'data' => $zone]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Zone not found.']);
        }
    }
    public function deleteById(Request $request, $id)
    {
        $this->action = 'delete';
        $this->browser = $request->header('User-Agent');
        $this->ip_address = $request->ip();

        $id = $request->id??0;

        try {
            $zone = Zone::findOrFail($id)->delete();

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Zone not found or could not be deleted.']);
        }

        $response = ["status"=>$this->status,"statusCode"=>$this->statusCode,"error"=>$this->error,"response"=>$this->response];

        Helper::updateUserLog(["user_id"=>$this->user_id, "user_name"=>$this->user_name, "parent"=>"zone", "parent_id"=> "",
        "action"=>$this->action, "browser"=>$this->browser, "ip_address"=>$this->ip_address, "request"=>$request->all(), "response"=>$response ]);

        return response()->json(['success' => true, 'status' => 200, 'message' => 'zone deleted successfully']);
    }
    public function changeZoneStatus(Request $request)
        {
            $this->browser = $request->header('User-Agent');
            $this->ip_address = $request->ip();
            $this->action = 'change status';

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:zones,id',
            'status' => 'required|numeric',
            'reason' => 'required_if:status,0,4|string',
        ]);

        try {
            $validatedData = $validator->validate();
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'status' => 422, 'errors' => $e->errors()]);
        }

        $project = Zone::find($validatedData['id']);
        $project->update([
            'status' => $validatedData['status'],
            'reason_to_delete' => $validatedData['reason'] ?? '',
        ]);

        $response = ["status"=>$this->status,"statusCode"=>$this->statusCode,"error"=>$this->error,"response"=>$this->response];

        Helper::updateUserLog(["user_id"=>$this->user_id, "user_name"=>$this->user_name, "parent"=>"zone", "parent_id"=> "",
        "action"=>$this->action, "browser"=>$this->browser, "ip_address"=>$this->ip_address, "request"=>$request->all(), "response"=>$response ]);

            return response()->json(['success' => true, 'status' => 200, 'message' => 'Zone status changed successfully']);
        }
        public function getZoneStatusCounter(Request $request)
    {
        try {
            $statusNames = [
                -1 => '-1',
                0 => '0',
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5',
                6 => '6',
            ];

            $statusCount = Zone::select('status', DB::raw('count(zones.status) as count'))
                ->groupBy('status')
                ->get();

            $formattedStatusCount = [];
            foreach ($statusNames as $statusValue => $statusName) {
                $count = $statusCount->where('status', $statusValue)->first()->count ?? 0;
                $formattedStatusCount[] = ['count' => $count, 'status' => $statusName];
            }

            return response()->json(['success' => true, 'status' => 200, 'data' => $formattedStatusCount]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => 'Error while fetching status counts.']);
        }
    }
}
