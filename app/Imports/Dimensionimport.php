<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\HR\Attendance;
use App\Models\HR\Employee;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Http\Request;

class Dimensionimport implements ToCollection , WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
        return $collection;

        // foreach ($collection as $key => $value) {
            

        //     // $Employee = Employee::where(['device_id'=>request()->post('deviceId')])->first();

        //     // $emp_id = $Employee->id;
        //     // $device_id = request()->post('deviceId');
        //     // $location_lat = '0';
        //     // $location_long = '0';
        //     // $checkout_location_lat = '0';
        //     // $checkout_location_long = '0';
        //     // $is_mobile = '0';
        //     // $attendance_date = date("Y-m-d",strtotime(request()->post('date')));
        //     // $checkin = request()->post('checkin');
        //     // $checkout = request()->post('checkout');
        //     // $checkintype = 'In';
        //     // $checkouttype = 'Out';
          
        //     // $attendanceImport = new Attendance();
        //     // $attendanceImport->emp_id = $emp_id;
        //     // $attendanceImport->machine_id = $device_id;
        //     // $attendanceImport->location_lat = $location_lat;
        //     // $attendanceImport->location_long = $location_long;
        //     // $attendanceImport->checkout_location_lat = $checkout_location_lat;
        //     // $attendanceImport->checkout_location_long = $checkout_location_long;
        //     // $attendanceImport->is_mobile = $is_mobile;
        //     // $attendanceImport->attendance_date = $attendance_date;
        //     // $attendanceImport->checkin = $checkin;
        //     // $attendanceImport->checkout = $checkout;
        //     // $attendanceImport->checkintype = $checkintype;
        //     // $attendanceImport->checkouttype = $checkouttype;

        //     // $attendanceImport->save();

        // }
       
    }
}