<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{

    public function createComplaint(Request $request)
    {
        try{
            $complaint = $request->only([
                'involved_establishment_id',
                'date_of_incident', 
                'description', 
                'response', 
                'tourist_id',  
                'resolved'
            ]);

            $newComplaint = Complaint::create($complaint);

            return response()->json([
                'status' => 'success',
                'message' => 'Complaint created successfully.',
                'complaint' => $newComplaint
            ], 201);

        } catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        
    }

    public function getAllComplaints() {
        try {
            $complaints = Complaint::with((["tourist"]))->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Complaints fetched successfully.',
                'complaints' => $complaints
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }    
    }

    public function getComplaintsByTouristId($id){
        try {
            $complaints = Complaint::where('tourist_id', $id)->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Complaints fetched successfully.',
                'complaints' => $complaints
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }        
    }  

    public function updateComplaintResponse(Request $request, $id){
        try {
            $complaint = Complaint::find($id);

            if (!$complaint) {
                return response()->json([
                    'status' => false,
                    'message' => 'Complaint not found.'
                ], 404);
            }

            $validatedData = $request->validate([
                'response' => 'required|string' 
            ]);

            $complaint->response = $validatedData['response'];
            $complaint->resolved = true;
            $complaint->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Complaint response updated successfully.',
                'complaint' => $complaint
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteComplaint($id){
        try {
            $complaint = Complaint::find($id);

            if (!$complaint) {
                return response()->json([
                    'status' => false,
                    'message' => 'Complaint not found.'
                ], 404);
            }

            $complaint->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Complaint deleted successfully.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}