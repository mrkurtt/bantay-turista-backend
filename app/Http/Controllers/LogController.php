<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tourist;
use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function allLogs(){
      
        //return response()->json(['logs' => $logs]);
       // return view('log.index', ['logs' => $logs]);

        try{
            $logs = Log::with(['tourist', 'establishment'])->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'qr_code' => $log->qr_code,
                'tourist' => [
                    'id' => $log->tourist_id,
                    'name' => $log->tourist->first_name.' '.$log->tourist->last_name ?? null, // Ensure null-safety
                ],
                'establishment' => [
                    'id' => $log->establishment_id,
                    'name' => $log->establishment->name ?? null,
                ],
                'date_time' => $log->date_time,
                'created_at' => $log->created_at,
                'updated_at' => $log->updated_at,
            ];
        });

            return response()->json( [
                'status'=>true, 
                'message' => 'Logs fetched successfully.', 
                'data' => $logs
            ],  200);

        } catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function createLog(Request $request)
    {
        try{

            $qrCode = $request->input('qr_code');

            $tourist = Tourist::where('qr_code', $qrCode)->first();

            if(!$tourist){
                return response()->json([
                    'status' => 'false',
                    'message' => 'Tourist with qr code ' . $qrCode . ' was not found.',
                ], 404);
            }

            $logDetails = [
                'qr_code' => $qrCode,
                'date_time' =>  $request->input('date_time'),
                'establishment_id' => $request->input('establishment_id'),
                'tourist_id' => $tourist->id,
                'tourist' => $tourist
            ];

            $log = Log::create($logDetails);

            $newLog = Log::with('tourist')->where('id', $log->id)->get();

            return response()->json([
                'status' => 'true',
                'message' => 'Log created successfully',
                'log' => $newLog
            ], 201);

        } catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        
    }

    public function getLogsByEstablishmentIdAndDate(Request $request){
        try{

            $dateParam = $request->query('date');
            $establishmentId = $request->query('establishment_id');

            $logs = Log::with(['tourist', 'establishment'])
                ->where('establishment_id', $establishmentId)
                ->when($dateParam, function ($query) use ($dateParam) {
                    return $query->whereDate('date_time', $dateParam);
                })
                ->orderBy('date_time', 'desc')
                ->get();

            return response()->json([
                'status' => 'true',
                'message' => 'Logs fetched successfully',
                'logs' => $logs
            ], 200);

        } catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    
}