<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Zongcalling;
use App\Models\ListMyLead;
use App\Models\HR\Employee;
use App\Models\CustomerActivity;
use App\Models\LeadImportDatabase;



use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use DB;

class CallingDataExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    
    public function __construct($type,$agent,$status,$fdate,$edate)
    {
        $this->type = $type;
        $this->agent = $agent;
        $this->status = $status;
        $this->fdate = $fdate;
        $this->edate = $edate;
    }
    
    public function collection()
    {
        
        // $type = $this->type;

        if($this->type !='' && $this->agent !='' && $this->status == '' && $this->fdate == '' && $this->edate == ''){
            
            $callingData = Zongcalling::with(['getCallingDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->get();
            
        }else if($this->type !='' && $this->agent =='' && $this->status == '' && $this->fdate == '' && $this->edate == ''){
            
           $callingData = Zongcalling::with(['getCallingDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['Type'=>$this->type])->get();
            
        }else if($this->agent !='' && $this->type =='' && $this->status == '' && $this->fdate == '' && $this->edate == ''){
            
            $empData = Employee::where(['id'=>$this->agent])->first();
            $implodeCon = array($empData->incommingPhone,$empData->outgoingPhone);
            
            $callingData = Zongcalling::with(['getCallingDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereIn('ext',$implodeCon)->get();
            
        }else if($this->agent =='' && $this->type =='' && $this->status != '' && $this->fdate == '' && $this->edate == ''){
           
            
            $callingData = Zongcalling::with(['getCallingDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->where(['status'=>$status])->get();
            
        }else if($this->agent =='' && $this->type =='' && $this->status == '' && $this->fdate != '' && $this->edate != ''){
            
            
            $callingData = Zongcalling::with(['getCallingDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereBetween(DB::raw('DATE(datetime)'),[$this->fdate,$this->edate])->get();
            
        }else if($this->agent =='' && $this->type !='' && $this->status != '' && $this->fdate != '' && $this->edate != ''){
            
            $callingData = Zongcalling::with(['getCallingDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereBetween(DB::raw('DATE(datetime)'),[$this->fdate,$this->edate])->where(['status'=>$this->status,'Type'=>$this->type])->get();
            
        }else{
            
           $callingData = Zongcalling::with(['getCallingDetails' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->get();
            
        }
        
      
        
        
        
        $newxxs = [];
        $array=[];
        
        foreach($callingData as $data){
            
            if($data->Type == 'Incomming Calls'){
                $empData = Employee::where(['incommingPhone'=>$data->ext])->first();
            }else{
                $empData = Employee::where(['outgoingPhone'=>$data->ext])->first();
            }
            
            // $PhoneMatch = $data->phone;
            
            // $str = ltrim($data->phone, '770');
            
            // $importData = LeadImportDatabase::where(['clientPhone'=>$str])->first();
            // $customerDx = ListMyLead::where('mobileNo','LIKE','%'.$str.'%')->first();
            
            $array[]=array(
                'clientName' => $data->getCallingDetails == '' || $data->getCallingDetails == null ? 'N/a' : $data->getCallingDetails->clientName,
                'clientPhone' => $data->getCallingDetails == '' || $data->getCallingDetails == null ? 'N/a' : $data->getCallingDetails->clientPhone,
                'clientProject' => $data->getCallingDetails == '' || $data->getCallingDetails == null ? 'N/a' : $data->getCallingDetails->clientProject,
                'status' => $data->status,
                'Type' => $data->Type,
                'ext' => $data->ext,
                'phone' => $data->phone,
                'datetime' => $data->datetime,
                'emp_name' => $empData == null || $empData == '' ? 'N/a' : $empData->emp_name,
                // 'empData'=>$empData,
            );
            
            
            // $array[] = Arr::collapse([$AssingDtaass,$newxxs]);
            
        }
          
        return collect($array); 
    }
}
