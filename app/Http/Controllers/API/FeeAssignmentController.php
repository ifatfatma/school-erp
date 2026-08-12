<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\StudentFeeDue;
use Illuminate\Http\Request;

class FeeAssignmentController extends Controller
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
                'feeType',
                'feeStructure',
            ]);

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

        if ($request->filled('fee_type_id')) {
            $query->where(
                'fee_type_id',
                $request->fee_type_id
            );
        }

        if ($request->filled('period')) {
            $query->where(
                'period',
                $request->period
            );
        }

        $data = $query
            ->latest('id')
            ->paginate(
                $request->get('per_page', 20)
            );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Students
    |--------------------------------------------------------------------------
    */

    public function students(Request $request)
    {
        $request->validate([

            'class_id' => [
                'required',
                'integer',
                'exists:classes,id',
            ],

            'section_id' => [
                'nullable',
                'integer',
                'exists:sections,id',
            ],

            'academic_session_id' => [
                'required',
                'integer',
                'exists:academic_sessions,id',
            ],

        ]);


        $query = Student::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'academic_session_id',
                $request->academic_session_id
            )
            ->where(
                'class_id',
                $request->class_id
            )
            ->where(
                'status',
                true
            );


        if ($request->filled('section_id')) {

            $query->where(
                'section_id',
                $request->section_id
            );

        }


        return response()->json([

            'success' => true,

            'data' => $query
                ->orderBy('roll_no')
                ->get(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Assign
    |--------------------------------------------------------------------------
    */

    public function assign(Request $request)
    {
        $validated = $request->validate([

            'academic_session_id' => [
                'required',
                'integer',
                'exists:academic_sessions,id',
            ],

            'class_id' => [
                'required',
                'integer',
                'exists:classes,id',
            ],

            'section_id' => [
                'nullable',
                'integer',
                'exists:sections,id',
            ],

            'fee_structure_id' => [
                'required',
                'integer',
                'exists:fee_structures,id',
            ],

            'fee_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'nullable',
                'date',
            ],

            'period' => [
                'required',
                'string',
                'max:50',
            ],

            'student_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'student_ids.*' => [
                'integer',
                'exists:students,id',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Fee Structure
        |--------------------------------------------------------------------------
        */

        $structure = FeeStructure::query()
            ->where(
                'id',
                $validated['fee_structure_id']
            )
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'academic_session_id',
                $validated['academic_session_id']
            )
            ->where(
                'class_id',
                $validated['class_id']
            )
            ->where(
                'status',
                true
            )
            ->with('feeType')
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Validate Section belongs to Class
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['section_id'])) {

            $sectionExists = \App\Models\Section::query()
                ->where(
                    'id',
                    $validated['section_id']
                )
                ->where(
                    'school_id',
                    auth()->user()->school_id
                )
                ->where(
                    'class_id',
                    $validated['class_id']
                )
                ->exists();

            if (!$sectionExists) {

                return response()->json([
                    'message' =>
                        'Selected section does not belong to selected class.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Get Students
        |--------------------------------------------------------------------------
        */

        $studentsQuery = Student::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'academic_session_id',
                $validated['academic_session_id']
            )
            ->where(
                'class_id',
                $validated['class_id']
            )
            ->whereIn(
                'id',
                $validated['student_ids']
            )
            ->where(
                'status',
                true
            );


        if (!empty($validated['section_id'])) {

            $studentsQuery->where(
                'section_id',
                $validated['section_id']
            );

        }


        $students = $studentsQuery->get();


        if ($students->count() !== count(
            array_unique($validated['student_ids'])
        )) {

            return response()->json([
                'message' =>
                    'One or more selected students are invalid.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Create Dues
        |--------------------------------------------------------------------------
        */

        $created = [];

        foreach ($students as $student) {

            /*
             * Prevent duplicate assignment
             */

            $exists = StudentFeeDue::query()
                ->where(
                    'school_id',
                    auth()->user()->school_id
                )
                ->where(
                    'student_id',
                    $student->id
                )
                ->where(
                    'fee_structure_id',
                    $structure->id
                )
                ->where(
                    'fee_date',
                    $validated['fee_date']
                )
                ->where(
                    'period',
                    $validated['period']
                )
                ->exists();


            if ($exists) {
                continue;
            }


            $amount = (float) $structure->amount;

            $lateFee = (float) (
                $structure->late_fee ?? 0
            );

            $netAmount = $amount + $lateFee;


            $created[] = StudentFeeDue::create([

                'school_id' =>
                    auth()->user()->school_id,

                'academic_session_id' =>
                    $validated['academic_session_id'],

                'student_id' =>
                    $student->id,

                'class_id' =>
                    $student->class_id,

                'section_id' =>
                    $student->section_id,

                'fee_type_id' =>
                    $structure->fee_type_id,

                'fee_structure_id' =>
                    $structure->id,

                'fee_date' =>
                    $validated['fee_date'],

                'due_date' =>
                    $validated['due_date'] ?? null,

                'period' =>
                    $validated['period'],

                'amount' =>
                    $amount,

                'discount' =>
                    0,

                'late_fee' =>
                    $lateFee,

                'net_amount' =>
                    $netAmount,

                'paid_amount' =>
                    0,

                'balance_amount' =>
                    $netAmount,

                'status' =>
                    'Pending',

                'created_by' =>
                    auth()->id(),

            ]);
        }


        return response()->json([

            'success' => true,

            'message' =>
                count($created)
                    ? count($created) .
                        ' student fee(s) assigned successfully.'
                    : 'No new fees were assigned. The selected fees already exist.',

            'data' => $created,

        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Assignment
    |--------------------------------------------------------------------------
    */

    public function destroy(int $id)
    {
        $due = StudentFeeDue::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->findOrFail($id);


        /*
         * Don't delete a fee that already has payment.
         */

        if (
            (float) $due->paid_amount > 0
        ) {

            return response()->json([

                'message' =>
                    'This fee cannot be deleted because payment has already been collected.'

            ], 422);

        }


        $due->delete();


        return response()->json([

            'success' => true,

            'message' =>
                'Fee assignment deleted successfully.',

        ]);
    }
}