<?php

namespace App\Http\Controllers\Api\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportReportController extends Controller
{
    protected function schoolId(?\Illuminate\Http\Request $request = null): ?int
    {
        return $this->resolveSchoolId($request);
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    public function summary(Request $request)
    {
        $schoolId = $this->schoolId();

        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        $vehicleCount = DB::table('transport_vehicles')
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->whereNull('deleted_at')
            ->count();

        $driverCount = DB::table('transport_drivers')
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->whereNull('deleted_at')
            ->count();

        $maintenanceQuery = DB::table(
            'transport_vehicle_maintenances'
        )
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->whereNull('deleted_at');

        if ($dateFrom) {
            $maintenanceQuery->whereDate(
                'maintenance_date',
                '>=',
                $dateFrom
            );
        }

        if ($dateTo) {
            $maintenanceQuery->whereDate(
                'maintenance_date',
                '<=',
                $dateTo
            );
        }

        $maintenanceCost = $maintenanceQuery->sum('cost');


        $expenseQuery = DB::table(
            'transport_expenses'
        )
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->whereNull('deleted_at');

        if ($dateFrom) {
            $expenseQuery->whereDate(
                'expense_date',
                '>=',
                $dateFrom
            );
        }

        if ($dateTo) {
            $expenseQuery->whereDate(
                'expense_date',
                '<=',
                $dateTo
            );
        }

        $expenseTotal = $expenseQuery->sum('amount');


        $fuelQuery = DB::table(
            'transport_expenses'
        )
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->whereNull('deleted_at')
            ->whereIn('expense_type', [
                'Fuel',
                'Diesel',
                'Petrol',
                'CNG',
            ]);

        if ($dateFrom) {
            $fuelQuery->whereDate(
                'expense_date',
                '>=',
                $dateFrom
            );
        }

        if ($dateTo) {
            $fuelQuery->whereDate(
                'expense_date',
                '<=',
                $dateTo
            );
        }

        $fuelCost = $fuelQuery->sum('amount');


        return response()->json([
            'success' => true,

            'data' => [

                'vehicles' => $vehicleCount,

                'drivers' => $driverCount,

                'maintenance_cost' =>
                    round($maintenanceCost, 2),

                'fuel_cost' =>
                    round($fuelCost, 2),

                'total_expenses' =>
                    round($expenseTotal, 2),

            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | VEHICLE REPORT
    |--------------------------------------------------------------------------
    */

    public function vehicles(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = DB::table(
            'transport_vehicles as v'
        )
        ->leftJoin(
            'transport_expenses as e',
            function ($join) use ($schoolId, $request) {

                $join->on(
                    'e.transport_vehicle_id',
                    '=',
                    'v.id'
                )
                ->where(
                    'e.school_id',
                    $schoolId
                )
                ->whereNull(
                    'e.deleted_at'
                );

                if ($request->date_from) {
                    $join->whereDate(
                        'e.expense_date',
                        '>=',
                        $request->date_from
                    );
                }

                if ($request->date_to) {
                    $join->whereDate(
                        'e.expense_date',
                        '<=',
                        $request->date_to
                    );
                }
            }
        )
        ->where(
            'v.school_id',
            $schoolId
        )
        ->whereNull(
            'v.deleted_at'
        )
        ->select([
            'v.id',
            'v.vehicle_no',
            'v.registration_no',
            'v.vehicle_type',
            'v.model',
            'v.capacity',
            'v.status',

            DB::raw(
                'COALESCE(SUM(e.amount), 0) as expense_total'
            ),

            DB::raw(
                'COUNT(e.id) as expense_count'
            ),
        ])
        ->groupBy(
            'v.id',
            'v.vehicle_no',
            'v.registration_no',
            'v.vehicle_type',
            'v.model',
            'v.capacity',
            'v.status'
        )
        ->orderBy('v.vehicle_no');

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DRIVER REPORT
    |--------------------------------------------------------------------------
    */

    public function drivers(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = DB::table(
            'transport_drivers as d'
        )
        ->leftJoin(
            'transport_expenses as e',
            function ($join) use ($schoolId, $request) {

                $join->on(
                    'e.transport_driver_id',
                    '=',
                    'd.id'
                )
                ->where(
                    'e.school_id',
                    $schoolId
                )
                ->whereNull(
                    'e.deleted_at'
                );

                if ($request->date_from) {
                    $join->whereDate(
                        'e.expense_date',
                        '>=',
                        $request->date_from
                    );
                }

                if ($request->date_to) {
                    $join->whereDate(
                        'e.expense_date',
                        '<=',
                        $request->date_to
                    );
                }
            }
        )
        ->where(
            'd.school_id',
            $schoolId
        )
        ->whereNull(
            'd.deleted_at'
        )
        ->select([
            'd.id',
            'd.name',
            'd.code',
            'd.phone',
            'd.license_no',
            'd.status',

            DB::raw(
                'COALESCE(SUM(e.amount), 0) as expense_total'
            ),

            DB::raw(
                'COUNT(e.id) as expense_count'
            ),
        ])
        ->groupBy(
            'd.id',
            'd.name',
            'd.code',
            'd.phone',
            'd.license_no',
            'd.status'
        )
        ->orderBy('d.name');

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | MAINTENANCE REPORT
    |--------------------------------------------------------------------------
    */

    public function maintenance(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = DB::table(
            'transport_vehicle_maintenances as m'
        )
        ->join(
            'transport_vehicles as v',
            'v.id',
            '=',
            'm.transport_vehicle_id'
        )
        ->where(
            'm.school_id',
            $schoolId
        )
        ->whereNull(
            'm.deleted_at'
        )
        ->select([
            'm.id',
            'm.maintenance_no',
            'm.maintenance_date',
            'm.maintenance_type',
            'm.service_provider',
            'm.cost',
            'm.status',
            'm.next_service_date',

            'v.vehicle_no',
            'v.registration_no',
        ]);

        if ($request->date_from) {
            $query->whereDate(
                'm.maintenance_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->date_to) {
            $query->whereDate(
                'm.maintenance_date',
                '<=',
                $request->date_to
            );
        }

        if ($request->transport_vehicle_id) {
            $query->where(
                'm.transport_vehicle_id',
                $request->transport_vehicle_id
            );
        }

        return response()->json([
            'success' => true,
            'data' => $query
                ->orderByDesc('m.maintenance_date')
                ->get(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FUEL REPORT
    |--------------------------------------------------------------------------
    */

    public function fuel(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = DB::table(
            'transport_expenses as e'
        )
        ->leftJoin(
            'transport_vehicles as v',
            'v.id',
            '=',
            'e.transport_vehicle_id'
        )
        ->where(
            'e.school_id',
            $schoolId
        )
        ->whereNull(
            'e.deleted_at'
        )
        ->whereIn(
            'e.expense_type',
            [
                'Fuel',
                'Diesel',
                'Petrol',
                'CNG',
            ]
        )
        ->select([
            'e.id',
            'e.expense_no',
            'e.expense_date',
            'e.expense_type',
            'e.quantity',
            'e.unit',
            'e.rate',
            'e.amount',
            'e.vendor',
            'e.payment_method',
            'e.reference_no',

            'v.vehicle_no',
            'v.registration_no',
        ]);

        if ($request->date_from) {
            $query->whereDate(
                'e.expense_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->date_to) {
            $query->whereDate(
                'e.expense_date',
                '<=',
                $request->date_to
            );
        }

        if ($request->transport_vehicle_id) {
            $query->where(
                'e.transport_vehicle_id',
                $request->transport_vehicle_id
            );
        }

        return response()->json([
            'success' => true,
            'data' => $query
                ->orderByDesc('e.expense_date')
                ->get(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | EXPENSE REPORT
    |--------------------------------------------------------------------------
    */

    public function expenses(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = DB::table(
            'transport_expenses as e'
        )
        ->leftJoin(
            'transport_vehicles as v',
            'v.id',
            '=',
            'e.transport_vehicle_id'
        )
        ->leftJoin(
            'transport_drivers as d',
            'd.id',
            '=',
            'e.transport_driver_id'
        )
        ->where(
            'e.school_id',
            $schoolId
        )
        ->whereNull(
            'e.deleted_at'
        )
        ->select([
            'e.*',

            'v.vehicle_no',
            'v.registration_no',

            'd.name as driver_name',
        ]);

        if ($request->date_from) {
            $query->whereDate(
                'e.expense_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->date_to) {
            $query->whereDate(
                'e.expense_date',
                '<=',
                $request->date_to
            );
        }

        if ($request->expense_type) {
            $query->where(
                'e.expense_type',
                $request->expense_type
            );
        }

        if ($request->transport_vehicle_id) {
            $query->where(
                'e.transport_vehicle_id',
                $request->transport_vehicle_id
            );
        }

        return response()->json([
            'success' => true,
            'data' => $query
                ->orderByDesc('e.expense_date')
                ->get(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | OPTIONS
    |--------------------------------------------------------------------------
    */

    public function options()
    {
        $schoolId = $this->schoolId();

        return response()->json([
            'success' => true,

            'data' => [

                'vehicles' =>
                    DB::table('transport_vehicles')
                        ->where(
                            'school_id',
                            $schoolId
                        )
                        ->whereNull(
                            'deleted_at'
                        )
                        ->orderBy('vehicle_no')
                        ->get([
                            'id',
                            'vehicle_no',
                            'registration_no',
                        ]),

                'expense_types' => [
                    'Fuel',
                    'Diesel',
                    'Petrol',
                    'CNG',
                    'Engine Oil',
                    'Toll',
                    'Parking',
                    'Repair',
                    'Spare Parts',
                    'Driver Expense',
                    'Cleaning',
                    'Permit',
                    'Insurance',
                    'Tax',
                    'Other',
                ],
            ],
        ]);
    }
}