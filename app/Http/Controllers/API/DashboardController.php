<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\FeePayment;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([

            'students' => Student::count(),

            'teachers' => Teacher::count(),

            'parents' => Guardian::count(),

            'classes' => ClassRoom::count(),

            'attendance' => 98,

            'today_fee' => FeePayment::whereDate('payment_date', today())->sum('amount'),

            'pending_fee' => 120000,

            'exams' => Exam::count(),

        ]);
    }
}