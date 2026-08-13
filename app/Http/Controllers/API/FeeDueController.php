<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentFeeDue;
use Illuminate\Http\Request;

class FeeDueController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentFeeDue::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->with([
                'student',
                'student.class',
                'student.section',
                'feeType',
                'feeStructure',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Academic Session
        |--------------------------------------------------------------------------
        */

        if ($request->filled('academic_session_id')) {

            $query->where(
                'academic_session_id',
                $request->academic_session_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Class
        |--------------------------------------------------------------------------
        */

        if ($request->filled('class_id')) {

            $query->where(
                'class_id',
                $request->class_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Section
        |--------------------------------------------------------------------------
        */

        if ($request->filled('section_id')) {

            $query->where(
                'section_id',
                $request->section_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Fee Type
        |--------------------------------------------------------------------------
        */

        if ($request->filled('fee_type_id')) {

            $query->where(
                'fee_type_id',
                $request->fee_type_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Fee Structure
        |--------------------------------------------------------------------------
        */

        if ($request->filled('fee_structure_id')) {

            $query->where(
                'fee_structure_id',
                $request->fee_structure_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Period
        |--------------------------------------------------------------------------
        */

        if ($request->filled('period')) {

            $query->where(
                'period',
                'like',
                '%' . $request->period . '%'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Student Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->whereHas(
                'student',
                function ($q) use ($search) {

                    $q->where(
                        'first_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'last_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'admission_no',
                        'like',
                        "%{$search}%"
                    );

                }
            );

        }


        $dues = $query
            ->latest('id')
            ->paginate(
                $request->get('per_page', 20)
            );


        return response()->json([

            'success' => true,

            'data' => $dues,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    public function summary(Request $request)
    {
        $query = StudentFeeDue::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            );


        if ($request->filled('academic_session_id')) {

            $query->where(
                'academic_session_id',
                $request->academic_session_id
            );

        }


        if ($request->filled('class_id')) {

            $query->where(
                'class_id',
                $request->class_id
            );

        }


        if ($request->filled('section_id')) {

            $query->where(
                'section_id',
                $request->section_id
            );

        }


        return response()->json([

            'success' => true,

            'data' => [

                'total_amount' => (float)
                    $query->sum('amount'),

                'total_discount' => (float)
                    $query->sum('discount'),

                'total_late_fee' => (float)
                    $query->sum('late_fee'),

                'total_net_amount' => (float)
                    $query->sum('net_amount'),

                'total_paid' => (float)
                    $query->sum('paid_amount'),

                'total_balance' => (float)
                    $query->sum('balance_amount'),

                'pending' => (int)
                    (clone $query)
                        ->where(
                            'status',
                            'Pending'
                        )
                        ->count(),

                'partial' => (int)
                    (clone $query)
                        ->where(
                            'status',
                            'Partial'
                        )
                        ->count(),

                'paid' => (int)
                    (clone $query)
                        ->where(
                            'status',
                            'Paid'
                        )
                        ->count(),

            ],

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(int $id)
    {
        $due = StudentFeeDue::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->with([
                'student',
                'student.class',
                'student.section',
                'feeType',
                'feeStructure',
            ])
            ->findOrFail($id);


        return response()->json([

            'success' => true,

            'data' => $due,

        ]);
    }
}