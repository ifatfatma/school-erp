<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | RESET SPATIE CACHE
        |--------------------------------------------------------------------------
        */

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();


        /*
        |--------------------------------------------------------------------------
        | ALL PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $permissions = [

            /*
            |--------------------------------------------------------------------------
            | DASHBOARD
            |--------------------------------------------------------------------------
            */

            'dashboard.view',


            /*
            |--------------------------------------------------------------------------
            | SCHOOL MANAGEMENT
            |--------------------------------------------------------------------------
            */

            'school.view',
            'school.create',
            'school.update',
            'school.delete',

            'academic_session.view',
            'academic_session.create',
            'academic_session.update',
            'academic_session.delete',


            /*
            |--------------------------------------------------------------------------
            | ACADEMIC
            |--------------------------------------------------------------------------
            */

            'class.view',
            'class.create',
            'class.update',
            'class.delete',

            'section.view',
            'section.create',
            'section.update',
            'section.delete',

            'subject.view',
            'subject.create',
            'subject.update',
            'subject.delete',

            'timetable.view',
            'timetable.create',
            'timetable.update',
            'timetable.delete',


            /*
            |--------------------------------------------------------------------------
            | STUDENTS
            |--------------------------------------------------------------------------
            */

            'student.view',
            'student.create',
            'student.update',
            'student.delete',

            'attendance.view',
            'attendance.create',
            'attendance.update',
            'attendance.delete',

            'guardian.view',
            'guardian.create',
            'guardian.update',
            'guardian.delete',

            'promotion.view',
            'promotion.create',
            'promotion.update',
            'promotion.delete',
            'promotion.execute',


            /*
            |--------------------------------------------------------------------------
            | TEACHERS
            |--------------------------------------------------------------------------
            */

            'teacher.view',
            'teacher.create',
            'teacher.update',
            'teacher.delete',

            'leave.view',
            'leave.create',
            'leave.update',
            'leave.delete',
            'leave.approve',
            'leave.reject',

            'leave_types.view',
            'leave_types.create',
            'leave_types.update',
            'leave_types.delete',

            'teacher_attendance.view',
            'teacher_attendance.create',
            'teacher_attendance.update',
            'teacher_attendance.delete',


            /*
            |--------------------------------------------------------------------------
            | EXAMINATIONS
            |--------------------------------------------------------------------------
            */

            'exam_types.view',
            'exam_types.create',
            'exam_types.update',
            'exam_types.delete',

            'exam_subjects.view',
            'exam_subjects.create',
            'exam_subjects.update',
            'exam_subjects.delete',

            'exam_schedules.view',
            'exam_schedules.create',
            'exam_schedules.update',
            'exam_schedules.delete',

            'exam.view',
            'exam.create',
            'exam.update',
            'exam.delete',

            'exam_marks.view',
            'exam_marks.create',
            'exam_marks.update',
            'exam_marks.delete',
            'exam_marks.publish',

            'grade.view',
            'grade.create',
            'grade.update',
            'grade.delete',

            'result_grades.view',
            'result_grades.create',
            'result_grades.update',
            'result_grades.delete',

            'report_card.view',
            'report_card.create',
            'report_card.update',
            'report_card.delete',
            'report_card.publish',
            'report_card.print',
            'report_card.export',

            'exam_ranks.view',
            'exam_ranks.create',
            'exam_ranks.update',
            'exam_ranks.delete',
            'exam_ranks.export',


            /*
            |--------------------------------------------------------------------------
            | FEES & ACCOUNTS
            |--------------------------------------------------------------------------
            */

            'fee_types.view',
            'fee_types.create',
            'fee_types.update',
            'fee_types.delete',

            'fee_structures.view',
            'fee_structures.create',
            'fee_structures.update',
            'fee_structures.delete',

            /*
            | Keep existing frontend permission names
            */

            'fee-dues.index',
            'fee-dues.view',
            'fee-dues.create',
            'fee-dues.update',
            'fee-dues.delete',

            'fee-assignments.index',
            'fee-assignments.view',
            'fee-assignments.create',
            'fee-assignments.update',
            'fee-assignments.delete',

            'fee-collection.index',
            'fee-collection.view',
            'fee-collection.create',
            'fee-collection.update',
            'fee-collection.delete',
            'fee-collection.collect',
            'fee-collection.cancel',

            'payment-history.index',
            'payment-history.view',
            'payment-history.export',

            'fee-receipts.index',
            'fee-receipts.view',
            'fee-receipts.create',
            'fee-receipts.print',
            'fee-receipts.download',

            'discounts.index',
            'discounts.view',
            'discounts.create',
            'discounts.update',
            'discounts.delete',

            'late-fees.index',
            'late-fees.view',
            'late-fees.create',
            'late-fees.update',
            'late-fees.delete',

            'fee-reports.index',
            'fee-reports.view',
            'fee-reports.export',


            /*
            |--------------------------------------------------------------------------
            | EXPENSE MANAGEMENT
            |--------------------------------------------------------------------------
            */

            'expense_categories.view',
            'expense_categories.create',
            'expense_categories.update',
            'expense_categories.delete',

            'expenses.view',
            'expenses.create',
            'expenses.update',
            'expenses.delete',

            'expense_payments.view',
            'expense_payments.create',
            'expense_payments.update',
            'expense_payments.delete',

            'recurring_expenses.view',
            'recurring_expenses.create',
            'recurring_expenses.update',
            'recurring_expenses.delete',

            'expense_reports.view',
            'expense_reports.export',


            /*
            |--------------------------------------------------------------------------
            | INCOME MANAGEMENT
            |--------------------------------------------------------------------------
            */

            'income_categories.view',
            'income_categories.create',
            'income_categories.update',
            'income_categories.delete',

            'incomes.view',
            'incomes.create',
            'incomes.update',
            'incomes.delete',

            'income_payments.view',
            'income_payments.create',
            'income_payments.update',
            'income_payments.delete',

            'recurring_incomes.view',
            'recurring_incomes.create',
            'recurring_incomes.update',
            'recurring_incomes.delete',

            'income_reports.view',
            'income_reports.export',


            /*
            |--------------------------------------------------------------------------
            | LIBRARY
            |--------------------------------------------------------------------------
            */

            'authors.view',
            'authors.create',
            'authors.update',
            'authors.delete',

            'publishers.view',
            'publishers.create',
            'publishers.update',
            'publishers.delete',

            'books.view',
            'books.create',
            'books.update',
            'books.delete',

            'book_copies.view',
            'book_copies.create',
            'book_copies.update',
            'book_copies.delete',

            'library_members.view',
            'library_members.create',
            'library_members.update',
            'library_members.delete',

            'book_issues.view',
            'book_issues.create',
            'book_issues.update',
            'book_issues.delete',
            'book_issues.issue',

            'book_returns.view',
            'book_returns.create',
            'book_returns.update',
            'book_returns.delete',
            'book_returns.return',

            'book_renewals.view',
            'book_renewals.create',
            'book_renewals.update',
            'book_renewals.delete',
            'book_renewals.renew',

            'lost_damaged_books.view',
            'lost_damaged_books.create',
            'lost_damaged_books.update',
            'lost_damaged_books.delete',

            'library_fines.view',
            'library_fines.create',
            'library_fines.update',
            'library_fines.delete',
            'library_fines.collect',

            'library_reports.view',
            'library_reports.export',


            /*
            |--------------------------------------------------------------------------
            | INVENTORY
            |--------------------------------------------------------------------------
            */

            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            'units.view',
            'units.create',
            'units.update',
            'units.delete',

            'suppliers.view',
            'suppliers.create',
            'suppliers.update',
            'suppliers.delete',

            'inventory.view',
            'inventory.create',
            'inventory.update',
            'inventory.delete',

            'purchase.view',
            'purchase.create',
            'purchase.update',
            'purchase.delete',
            'purchase.approve',

            'stock_ins.view',
            'stock_ins.create',
            'stock_ins.update',
            'stock_ins.delete',

            'stock_movements.view',
            'stock_movements.create',
            'stock_movements.update',
            'stock_movements.delete',

            'inventory_dashboard.view',

            'inventory_stock_outs.view',
            'inventory_stock_outs.create',
            'inventory_stock_outs.update',
            'inventory_stock_outs.delete',


            /*
            |--------------------------------------------------------------------------
            | TRANSPORT
            |--------------------------------------------------------------------------
            */

            'transport_dashboard.view',

            'transport_vehicles.view',
            'transport_vehicles.create',
            'transport_vehicles.update',
            'transport_vehicles.delete',

            'transport_drivers.view',
            'transport_drivers.create',
            'transport_drivers.update',
            'transport_drivers.delete',

            'transport_routes.view',
            'transport_routes.create',
            'transport_routes.update',
            'transport_routes.delete',

            'transport_stops.view',
            'transport_stops.create',
            'transport_stops.update',
            'transport_stops.delete',

            'transport_students.view',
            'transport_students.create',
            'transport_students.update',
            'transport_students.delete',

            'transport_assignments.view',
            'transport_assignments.create',
            'transport_assignments.update',
            'transport_assignments.delete',

            'transport_fees.view',
            'transport_fees.create',
            'transport_fees.update',
            'transport_fees.delete',
            'transport_fees.collect',

            'transport_maintenance.view',
            'transport_maintenance.create',
            'transport_maintenance.update',
            'transport_maintenance.delete',

            'transport_expenses.view',
            'transport_expenses.create',
            'transport_expenses.update',
            'transport_expenses.delete',

            'transport_reports.view',
            'transport_reports.export',


            /*
            |--------------------------------------------------------------------------
            | HOSTEL
            |--------------------------------------------------------------------------
            */

            'hostel.view',
            'hostel.create',
            'hostel.update',
            'hostel.delete',

            'hostel_buildings.view',
            'hostel_buildings.create',
            'hostel_buildings.update',
            'hostel_buildings.delete',

            'hostel_rooms.view',
            'hostel_rooms.create',
            'hostel_rooms.update',
            'hostel_rooms.delete',

            'hostel_beds.view',
            'hostel_beds.create',
            'hostel_beds.update',
            'hostel_beds.delete',

            'hostel_students.view',
            'hostel_students.create',
            'hostel_students.update',
            'hostel_students.delete',

            'hostel_fees.view',
            'hostel_fees.create',
            'hostel_fees.update',
            'hostel_fees.delete',
            'hostel_fees.collect',

            'hostel_attendance.view',
            'hostel_attendance.create',
            'hostel_attendance.update',
            'hostel_attendance.delete',

            'hostel_expenses.view',
            'hostel_expenses.create',
            'hostel_expenses.update',
            'hostel_expenses.delete',

            'hostel_reports.view',
            'hostel_reports.export',


            /*
            |--------------------------------------------------------------------------
            | REPORTS
            |--------------------------------------------------------------------------
            */

            'report.view',
            'report.create',
            'report.export',
            'report.print',


            /*
            |--------------------------------------------------------------------------
            | ADMINISTRATION - USERS
            |--------------------------------------------------------------------------
            */

            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.activate',
            'user.deactivate',
            'user.reset_password',


            /*
            |--------------------------------------------------------------------------
            | ADMINISTRATION - ROLES
            |--------------------------------------------------------------------------
            */

            'role.view',
            'role.create',
            'role.update',
            'role.delete',
            'role.assign_permission',


            /*
            |--------------------------------------------------------------------------
            | ADMINISTRATION - PERMISSIONS
            |--------------------------------------------------------------------------
            */

            'permission.view',
            'permission.create',
            'permission.update',
            'permission.delete',


            /*
            |--------------------------------------------------------------------------
            | SETTINGS
            |--------------------------------------------------------------------------
            */

            'settings.view',
            'settings.create',
            'settings.update',
            'settings.delete',


            /*
            |--------------------------------------------------------------------------
            | PROFILE
            |--------------------------------------------------------------------------
            */

            'profile.view',
            'profile.update',
            'profile.change_password',

        ];


        /*
        |--------------------------------------------------------------------------
        | REMOVE DUPLICATES
        |--------------------------------------------------------------------------
        */

        $permissions = array_values(
            array_unique($permissions)
        );


        /*
        |--------------------------------------------------------------------------
        | CREATE PERMISSIONS
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permission) {

            Permission::firstOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => 'sanctum',
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN ROLE
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::firstOrCreate(
            [
                'name' => 'Super Admin',
                'guard_name' => 'sanctum',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | GIVE ALL PERMISSIONS TO SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        $allPermissions = Permission::where(
            'guard_name',
            'sanctum'
        )->get();

        $superAdmin->syncPermissions(
            $allPermissions
        );


        /*
        |--------------------------------------------------------------------------
        | SCHOOL ADMIN ROLE
        |--------------------------------------------------------------------------
        */

        $schoolAdmin = Role::firstOrCreate(
            [
                'name' => 'School Admin',
                'guard_name' => 'sanctum',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | SCHOOL ADMIN PERMISSIONS
        |--------------------------------------------------------------------------
        |
        | School Admin gets normal school-management permissions.
        | Administration permissions are intentionally excluded.
        |
        */

        $schoolAdminPermissions = collect($permissions)
            ->reject(function ($permission) {

                return str_starts_with(
                    $permission,
                    'user.'
                )
                ||
                str_starts_with(
                    $permission,
                    'role.'
                )
                ||
                str_starts_with(
                    $permission,
                    'permission.'
                )
                ||
                str_starts_with(
                    $permission,
                    'settings.'
                );

            })
            ->values()
            ->all();


        $schoolAdmin->syncPermissions(
            Permission::whereIn(
                'name',
                $schoolAdminPermissions
            )
            ->where(
                'guard_name',
                'sanctum'
            )
            ->get()
        );


        /*
        |--------------------------------------------------------------------------
        | CLEAR CACHE
        |--------------------------------------------------------------------------
        */

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();


        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        $this->command?->info(
            '----------------------------------------'
        );

        $this->command?->info(
            'Permissions created: ' .
            count($permissions)
        );

        $this->command?->info(
            'Super Admin permissions: ' .
            $superAdmin
                ->permissions()
                ->count()
        );

        $this->command?->info(
            'School Admin permissions: ' .
            $schoolAdmin
                ->permissions()
                ->count()
        );

        $this->command?->info(
            'Permission seeding completed.'
        );

        $this->command?->info(
            '----------------------------------------'
        );
    }
}