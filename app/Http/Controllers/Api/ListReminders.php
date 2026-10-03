<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignReminder;
use Illuminate\Http\Request;
use App\Models\ListAssignLeads;
use App\Models\ListNotInterested;
use App\Models\CustomerActivity;
use App\Models\ListMyLead;
use App\Models\HR\Employee;
use App\Models\TranasferShareLeads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LeadImportDatabase;
use App\Exports\SuperDatabaseExport;
use App\Models\Zongcalling;
use App\Models\ProjectAdd;


use Carbon\Carbon;
use DB;

use Validator;

class ListReminders extends Controller
{
    public function ListRemindersData(Request $request){
        
        
        $orderbY = $request->orderby;
        $orderType = $request->ordertype;
        $perPage = $request->perpage;
        $agentid = $request->agentid;
        $teamObj = $request->teamObj ?? "";
        $ReminderStatus = $request->ReminderStatus ?? "";
        $SalesConsul = $request->SalesConsul ?? "";
        $ReminderPriority = $request->ReminderPriority ?? "";

        if(is_null($orderbY)){ $orderbY = "id"; }else{
            $orderbY = $orderbY;
        }

        if(is_null($orderType)){ $ordertype = 'desc'; }else{
            $ordertype = $orderType;
        }
        
        if(is_null($perPage)){
            
            $listAssignCount = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderbY , $ordertype)->count();
            
            $PerPage = $listAssignCount;
            $totalRec = $listAssignCount;
            
        }else{
     
            if($perPage){ $PerPage = $perPage; }else{
                $PerPage = 5;
            }
        
        }
        
        if(is_null($agentid) && empty($teamObj) && empty($ReminderStatus) && empty($ReminderPriority)){
            
            $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = AssignReminder::orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = AssignReminder::orderBy($orderbY , $ordertype)->count();
            
        }else if($agentid =='all' && empty($teamObj) && empty($ReminderStatus) && empty($ReminderPriority)){
            
            $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = AssignReminder::orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = AssignReminder::orderBy($orderbY , $ordertype)->count();
            
        }else if($agentid == 'all' && !empty($teamObj) && empty($ReminderStatus) && empty($ReminderPriority) && empty($SalesConsul)){
          
            $myArray2 = explode(',', $teamObj);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereHas('getAssignLeads', function($q){
                $q->where('shiftStatus', '!=', 2);
            })->whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->whereHas('getAssignLeads', function($q){
                $q->where('shiftStatus', '!=', 2);
            })->whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
            $query->orderBy('id', 'desc')->get();
            } ,])->whereHas('getAssignLeads', function($q){
                $q->where('shiftStatus', '!=', 2);
            })->whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->count();
            
        }else if($agentid == 'all' && !empty($teamObj) && empty($ReminderStatus) && empty($ReminderPriority) && !empty($SalesConsul)){
          
            $myArray2 = explode(',', $teamObj);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereHas('getAssignLeads', function($q){
                $q->where('shiftStatus', '!=', 2);
            })->whereIn('agentid',$myArray)->where(['agentid'=>$SalesConsul])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereHas('getAssignLeads', function($q){
                $q->where('shiftStatus', '!=', 2);
            })->whereIn('agentid',$myArray)->where(['agentid'=>$SalesConsul])->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereHas('getAssignLeads', function($q){
                $q->where('shiftStatus', '!=', 2);
            })->whereIn('agentid',$myArray)->where(['agentid'=>$SalesConsul])->orderBy($orderbY , $ordertype)->count();
            
        }else if($agentid == 'all' && !empty($teamObj) && empty($ReminderStatus) && !empty($ReminderPriority)){
            
            $myArray2 = explode(',', $teamObj);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = AssignReminder::whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = AssignReminder::whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->orderBy($orderbY , $ordertype)->count();
            
        }else if($agentid == 'all' && !empty($teamObj) && !empty($ReminderStatus) && empty($ReminderPriority)){
            
            $myArray2 = explode(',', $teamObj);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            
            if($ReminderStatus == 'Today' && empty($SalesConsul)){
               
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereHas('getAssignLeads', function($q){
                    $q->where('shiftStatus', '!=', 2);
                })->whereIn('agentid',$myArray)->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereIn('agentid',$myArray)->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->whereHas('getAssignLeads', function($q){
                    $q->where('shiftStatus', '!=', 2);
                })->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereIn('agentid',$myArray)->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->whereHas('getAssignLeads', function($q){
                    $q->where('shiftStatus', '!=', 2);
                })->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Today' && !empty($SalesConsul)){
                
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->where(['agentid'=>$SalesConsul])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->whereHas('getAssignLeads', function($q){
                    $q->where('shiftStatus', '!=', 2);
                })->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->where(['agentid'=>$SalesConsul])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->whereHas('getAssignLeads', function($q){
                    $q->where('shiftStatus', '!=', 2);
                })->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->where(['agentid'=>$SalesConsul])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->whereHas('getAssignLeads', function($q){
                    $q->where('shiftStatus', '!=', 2);
                })->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Tomorrow' && empty($SalesConsul)){
                
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereIn('agentid',$myArray)->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::whereIn('agentid',$myArray)->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::whereIn('agentid',$myArray)->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Tomorrow' && !empty($SalesConsul)){
                
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->where(['agentid'=>$SalesConsul])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::where(['agentid'=>$SalesConsul])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::where(['agentid'=>$SalesConsul])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Overdue' && empty($SalesConsul)){
                
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->whereHas('getAssignLeads', function($q){
                    $q->where('shiftStatus', '!=', 2);
                })->whereIn('agentid',$myArray)->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->whereHas('getAssignLeads', function($q){
                    $q->where('shiftStatus', '!=', 2);
                })->whereIn('agentid',$myArray)->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->whereHas('getAssignLeads', function($q){
                    $q->where('shiftStatus', '!=', 2);
                })->whereIn('agentid',$myArray)->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Overdue' && !empty($SalesConsul)){
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->where(['agentid'=>$SalesConsul])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->where(['agentid'=>$SalesConsul])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->where(['agentid'=>$SalesConsul])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->count();
                
            }else{
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->count();
                
            }
            
           
            
        }else if($agentid == 'all' && !empty($teamObj) && !empty($ReminderStatus) && !empty($ReminderPriority)){
            
            $myArray2 = explode(',', $teamObj);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            if($ReminderStatus == 'Today' && empty($SalesConsul)){
                
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::whereDoesntHave('getCallingImpress')->whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::whereDoesntHave('getCallingImpress')->whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Today' && !empty($SalesConsul)){
                
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->where(['agentid'=>$SalesConsul])->where(['priority'=>$ReminderPriority])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::where(['agentid'=>$SalesConsul])->where(['priority'=>$ReminderPriority])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::whereDoesntHave('getCallingImpress')->where(['agentid'=>$SalesConsul])->where(['priority'=>$ReminderPriority])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Tomorrow' && empty($SalesConsul)){
                
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Tomorrow' && !empty($SalesConsul)){
                
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->where(['agentid'=>$SalesConsul])->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::where(['agentid'=>$SalesConsul])->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::where(['agentid'=>$SalesConsul])->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Overdue' && empty($SalesConsul)){
                
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::whereDoesntHave('getCallingImpress')->whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::whereDoesntHave('getCallingImpress')->whereIn('agentid',$myArray)->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Overdue' && !empty($SalesConsul)){
                
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->where(['agentid'=>$SalesConsul])->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->where(['agentid'=>$SalesConsul])->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->where(['agentid'=>$SalesConsul])->where(['priority'=>$ReminderPriority])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->count();
                
            }else{
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::whereIn('agentid',$myArray)->orderBy($orderbY , $ordertype)->count();
                
            }
            
           
            
        }else{
          
           
           if($ReminderStatus == 'Today' && empty($SalesConsul)){
                
                 $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->where(['agentid'=>$agentid])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::where(['agentid'=>$agentid])->where(['priority'=>$ReminderPriority])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::where(['agentid'=>$agentid])->where(['priority'=>$ReminderPriority])->where(DB::raw('DATE(reminderSet)'), date('Y-m-d'))->orderBy($orderbY , $ordertype)->count();
    
            }else if($ReminderStatus == 'Tomorrow' && empty($SalesConsul)){
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->where(['agentid'=>$agentid])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::where(['agentid'=>$agentid])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::where(['agentid'=>$agentid])->whereDate('reminderSet', '=', Carbon::tomorrow())->orderBy($orderbY , $ordertype)->count();
                
            }else if($ReminderStatus == 'Overdue' && empty($SalesConsul)){
                
                $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
                } ,])->whereDoesntHave('getCallingImpress')->where(['agentid'=>$agentid])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->paginate($PerPage);
                
                $listAssignTotalPages = AssignReminder::whereDoesntHave('getCallingImpress')->where(['agentid'=>$agentid])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
                
                $listAssignCount = AssignReminder::whereDoesntHave('getCallingImpress')->where(['agentid'=>$agentid])->whereDate('reminderSet', '<', Carbon::today())->orderBy($orderbY , $ordertype)->count();
                
            }else{
                
                 $listAssign = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereHas('getCallingImpress', function($q){
                   // Query the name field in status table
                   $q->where('leadid', '!=', ''); // '=' is optional
            })->where(['agentid'=>$agentid])->orderBy($orderbY , $ordertype)->paginate($PerPage);
            
            $listAssignTotalPages = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereHas('getCallingImpress', function($q){
                   // Query the name field in status table
                   $q->where('leadid', '!=', ''); // '=' is optional
            })->where(['agentid'=>$agentid])->orderBy($orderbY , $ordertype)->paginate($PerPage)->total();
            
             $listAssignCount = AssignReminder::with(['CustomerLeadsTbl','getleads_home','getEmployeeDetails','getAssignLeads','getCallingImpress' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->whereHas('getCallingImpress', function($q){
                   // Query the name field in status table
                   $q->where('leadid', '!=', ''); // '=' is optional
            })->where(['agentid'=>$agentid])->orderBy($orderbY , $ordertype)->count();
                
                
            }
            
           
            
        }
        
        $countListassign = count($listAssign);
        $TOtalPerPage = ceil($listAssignTotalPages/$PerPage);
       

      
        if(count($listAssign) > 0){
            
          $data = $this->RemindernrenderList($listAssign);
            
            return response()->json(['code'=>'200','success'=>true,'message'=>'Record Found','data'=>$listAssign,'orderBy'=>$orderbY,'orderType'=>$ordertype,'perPage'=>$PerPage,'pageNo'=>$request->page,'totalRecords'=>$listAssignTotalPages,'TotalPerPage'=>$TOtalPerPage]);
        }else{
            return response()->json(['code'=>'403','error'=>false,'message'=>'Record Not Found','data'=>'','orderBy'=>'','orderType'=>'','perPage'=>'','pageNo'=>'','totalRecords'=>0,'total'=>0]);
        }
       

    }
    
    public function RemindernrenderList(Object $apiResponse)
    {
    $data = [];
    
    foreach ($apiResponse as $key => $value) {
    
    array_push($data, (object) [
    "id" => $value->id,
    "created_at" => $value->created_at,
    "updated_at" => $value->updated_at,
    "deleted_at" => $value->deleted_at,
    // "clientName"=>$value->getleads_home == '' || $value->getleads_home = null ? '' : $value->getleads_home->clientName,
    // "clientPhone"=>$value->getleads_home == '' || $value->getleads_home = null ? '' : $value->getleads_home->clientPhone,
    // "clientProject"=>$value->getleads_home == '' || $value->getleads_home = null ? '' : $value->getleads_home->clientProject,
   
    ]);
    }
    
    return $data;
    }
    
    
    public function ListAnalytics(Request $request)
    {
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "10";
        $page = $request->page ?? "1";
        $q = $request->q ?? "";
        
        $agentId = $request->agentId;

        if (!is_null($agentId) && $agentId != 'all') {

            $AssignLeadsTotal = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$agentId])->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();
            
            $MyLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No','agentid'=>$agentId])->count();
            
             //Dorment Leads Count
            $DormentLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes','agentid'=>$agentId])->count();
            
            //Hot Leads Count
            $HotCountAll = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No','agentid'=>$agentId])->count();
            
            //Not Interested COunt
            $NotinterestedCountAll = ListNotInterested::where(['agentid'=>$agentId])->count();
            
            //Customer Activity Count
            $cusACtivityCountAll = CustomerActivity::where(['agentid'=>$agentId])->count();
            
            
            $week = []; 
            for ($i=0; $i <7 ; $i++) {
                
                $AssignLeadsTotalWeek = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$agentId])->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->whereDate('leadsassigndatabase.publishAgentDate',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                 $MyLeadCountWeek = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No','agentid'=>$agentId])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $HotCountWeek = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No','agentid'=>$agentId])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $NotinterestedCountWeekly = ListNotInterested::where(['agentid'=>$agentId])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $cusACtivityCountWeekly = CustomerActivity::where(['agentid'=>$agentId])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $DormentLeadCountWeekly = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes','agentid'=>$agentId])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $week[] = array(
                 'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
                 'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
                 'assignleadweekly'=>$AssignLeadsTotalWeek,
                 'myleadweekly'=>$MyLeadCountWeek,
                 'hotleadweekly'=>$HotCountWeek,
                 'notinterestedweekly'=>$NotinterestedCountWeekly,
                 'activityweekly'=>$cusACtivityCountWeekly,
                 'dormentweekly'=>$DormentLeadCountWeekly,
                );
            }
            
            
             $TransferLeadsAssign = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'0'])->count();
            
            $TransferMyLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'My Lead'])->count();
            
            $TransferHotLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'hot'])->count();
            
            $TransferNotinterestedLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'2'])->count();
            
            $weeks = []; 
            for ($i=0; $i <7 ; $i++) {
                
                $TransferLeadsAssignWeekly = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->whereDate('publishAgentDate',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $TransferMyLeadsWeekly = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'My Lead'])->whereDate('customer_leads_tbls.created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $TransferHotLeadsWeekly = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'hot'])->whereDate('customer_leads_tbls.created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $TransferNotinterestedLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'2'])->where(['shiftStatus'=>'2'])->whereDate('leadsassigndatabase.publishAgentDate',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $weeks[] = array(
                 'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
                 'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
                 'transferassignweekly' => $TransferLeadsAssignWeekly,
                 'transfermyleadsweekly' => $TransferMyLeadsWeekly,
                 'transferhotleadsweekly' => $TransferHotLeadsWeekly,
                 'transfernotinterestedleadsweekly' => $TransferNotinterestedLeads,
                 
                );
            }
            
            
            
        }else if(empty($agentId)){
            
            $AssignLeadsTotal = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();
            
            //My Leads Count
            $MyLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->count();
            //Dorment Leads Count
            $DormentLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->count();
            
            //Hot Leads Count
            $HotCountAll = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->count();
            
            //Not Interested COunt
            $NotinterestedCountAll = ListNotInterested::count();
            
            //Customer Activity Count
            $cusACtivityCountAll = CustomerActivity::count();
         
            // $AssignLeadWeekly = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->whereDate('publishAgentDate', $lastWeek)->get();
            
             $AssignLeadWeekly = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('*')->whereBetween('leadsassigndatabase.publishAgentDate',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            

            $week = []; 
            for ($i=0; $i <7 ; $i++) {
                
                $AssignLeadsTotalWeek = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->whereDate('leadsassigndatabase.publishAgentDate',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                 $MyLeadCountWeek = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $HotCountWeek = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $NotinterestedCountWeekly = ListNotInterested::whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $cusACtivityCountWeekly = CustomerActivity::whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $DormentLeadCountWeekly = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $week[] = array(
                 'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
                 'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
                 'assignleadweekly'=>$AssignLeadsTotalWeek,
                 'myleadweekly'=>$MyLeadCountWeek,
                 'hotleadweekly'=>$HotCountWeek,
                 'notinterestedweekly'=>$NotinterestedCountWeekly,
                 'activityweekly'=>$cusACtivityCountWeekly,
                 'dormentweekly'=>$DormentLeadCountWeekly,
                );
            }
            
            
            $TransferLeadsAssign = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'0'])->count();
            
            $TransferMyLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'My Lead'])->count();
            
            $TransferHotLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'hot'])->count();
            
            $TransferNotinterestedLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'2'])->count();
            
            $weeks = []; 
            for ($i=0; $i <7 ; $i++) {
                
                $TransferLeadsAssignWeekly = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->whereDate('publishAgentDate',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $TransferMyLeadsWeekly = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'My Lead'])->whereDate('customer_leads_tbls.created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $TransferHotLeadsWeekly = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'hot'])->whereDate('customer_leads_tbls.created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $TransferNotinterestedLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'2'])->where(['shiftStatus'=>'2'])->whereDate('leadsassigndatabase.publishAgentDate',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $weeks[] = array(
                 'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
                 'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
                 'transferassignweekly' => $TransferLeadsAssignWeekly,
                 'transfermyleadsweekly' => $TransferMyLeadsWeekly,
                 'transferhotleadsweekly' => $TransferHotLeadsWeekly,
                 'transfernotinterestedleadsweekly' => $TransferNotinterestedLeads,
                 
                );
            }
            
        }else if($agentId == 'all'){
            
            $AssignLeadsTotal = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->count();
            
            //My Leads Count
            $MyLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->count();
            //Dorment Leads Count
            $DormentLeadCountAll = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->count();
            
            //Hot Leads Count
            $HotCountAll = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->count();
            
            //Not Interested COunt
            $NotinterestedCountAll = ListNotInterested::count();
            
            //Customer Activity Count
            $cusACtivityCountAll = CustomerActivity::count();
         
            // $AssignLeadWeekly = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->whereDate('publishAgentDate', $lastWeek)->get();
            
             $AssignLeadWeekly = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('*')->whereBetween('leadsassigndatabase.publishAgentDate',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            

            $week = []; 
            for ($i=0; $i <7 ; $i++) {
                
                $AssignLeadsTotalWeek = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->whereDate('leadsassigndatabase.publishAgentDate',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                 $MyLeadCountWeek = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'No'])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $HotCountWeek = ListMyLead::where(['LeadStatus'=>'hot','ReasonStatus'=>'No'])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $NotinterestedCountWeekly = ListNotInterested::whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $cusACtivityCountWeekly = CustomerActivity::whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                 
                 $DormentLeadCountWeekly = ListMyLead::where(['LeadStatus'=>'My Lead','DormentStatus'=>'Yes'])->whereDate('created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $week[] = array(
                 'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
                 'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
                 'assignleadweekly'=>$AssignLeadsTotalWeek,
                 'myleadweekly'=>$MyLeadCountWeek,
                 'hotleadweekly'=>$HotCountWeek,
                 'notinterestedweekly'=>$NotinterestedCountWeekly,
                 'activityweekly'=>$cusACtivityCountWeekly,
                 'dormentweekly'=>$DormentLeadCountWeekly,
                );
            }
            
            
            $TransferLeadsAssign = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'0'])->count();
            
            $TransferMyLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'My Lead'])->count();
            
            $TransferHotLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'hot'])->count();
            
            $TransferNotinterestedLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'2'])->count();
            
            $weeks = []; 
            for ($i=0; $i <7 ; $i++) {
                
                $TransferLeadsAssignWeekly = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->whereDate('publishAgentDate',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $TransferMyLeadsWeekly = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'My Lead'])->whereDate('customer_leads_tbls.created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $TransferHotLeadsWeekly = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->join('customer_leads_tbls','customer_leads_tbls.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'1','LeadStatus'=>'hot'])->whereDate('customer_leads_tbls.created_at',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $TransferNotinterestedLeads = TranasferShareLeads::join('leadsassigndatabase','leadsassigndatabase.leadid','=','tranasfer_share_leads.leadid')->where(['shiftStatus'=>'2'])->where(['shiftStatus'=>'2'])->whereDate('leadsassigndatabase.publishAgentDate',Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'))->count();
                
                $weeks[] = array(
                 'format' => Carbon::now()->startOfWeek()->addDay($i)->format('l'),
                 'datetime' => Carbon::now()->startOfWeek()->addDay($i)->format('Y-m-d'),
                 'transferassignweekly' => $TransferLeadsAssignWeekly,
                 'transfermyleadsweekly' => $TransferMyLeadsWeekly,
                 'transferhotleadsweekly' => $TransferHotLeadsWeekly,
                 'transfernotinterestedleadsweekly' => $TransferNotinterestedLeads,
                 
                );
            }
            
        }
        
        
        $data = array(
          
          'leadPie'=>array(
            'assignleads' => $AssignLeadsTotal,
            'myleads' => $MyLeadCountAll,
            'dormentleads' => $DormentLeadCountAll,
            'hotleads' => $HotCountAll,
            'notinterested' => $NotinterestedCountAll,
            'activity' => $cusACtivityCountAll,
           ),
           'leadGraphs'=>array(
            'assignleadweekly'=>$week,
            ),
           
            'sharetransfer'=>array(
                'transferAssign'=>$TransferLeadsAssign,
                'transfermyleads'=>$TransferMyLeads,
                'transfernotinterested'=>$TransferNotinterestedLeads,
                'transferhotleads'=>$TransferHotLeads,
             ),
             'transferGraphsView'=>array(
                'data'=>$weeks,
                ),
            
            
        );
        
        
        return response()->json(['code'=>'200','error'=>false,'message'=>'Record Found','data'=>$data]);
     

    }
    
    
    public function DailyLeadsReporting(Request $request){
        
        $weeks = []; 
        
         $empName = Employee::where(['department_id'=>'1'])->get();
         
         $TotalAssignLead = 0;
          $TotalAssignWeeklyLead = 0;
          $TotalProjectsLead = 0;
         
         foreach($empName as $data){
             
          $AssignLeadsTotalToday = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->where(['agentid'=>$data->id])->whereDate('leadsassigndatabase.publishAgentDate',Carbon::now())->count();
          
          $TotalAssignLead += $AssignLeadsTotalToday;
          
          $AssignLeadsTotalWeeks = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->where(['agentid'=>$data->id])->whereDate('leadsassigndatabase.publishAgentDate',Carbon::yesterday()->format('Y-m-d'))->count();
          
          $TotalAssignWeeklyLead += $AssignLeadsTotalWeeks;
          
        //   $dataxs = ListAssignLeads::join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->select('leadsassigndatabase.leadid','leadsimportdatabase.id','leadsassigndatabase.agentid')->where(['agentid'=>$data->id])->whereDate('leadsassigndatabase.publishAgentDate',Carbon::yesterday()->format('Y-m-d'))->groupBy('clientProject')
        //     ->selectRaw('count(*) as total, clientProject')
        //     ->get();
            
            // $chat = LeadImportDatabase::groupBy('clientProject')->get();
            $chat = DB::table('leadsimportdatabase')
                    ->join('leadsassigndatabase', 'leadsassigndatabase.leadid', '=', 'leadsimportdatabase.id')
                    ->where(['leadsassigndatabase.agentid'=>$data->id])
                    ->whereDate('leadsassigndatabase.publishAgentDate',Carbon::now())
                     ->select(DB::raw('count(clientProject) as user_count , clientProject'))
                     ->groupBy('clientProject')
                     ->get();
                     
             foreach($chat as $itm){
                 
                 $TotalProjectsLead +=$itm->user_count;
                 
             }
            
            
            // $dataxs = DB::table('leadsassigndatabase')
            // ->join('leadsimportdatabase', 'leadsassigndatabase.leadid', '=', 'leadsimportdatabase.id')
            // ->select('leadsassigndatabase.*','leadsassigndatabase.*', DB::raw('sum(clientProject) as sum'))
            // ->groupBy('clientProject')
            // ->get();
           
           $weeks[] = array(
             'emp_name' => $data->emp_name,
             'assignToday' =>$AssignLeadsTotalToday,
             'yesterDayLeads' => $AssignLeadsTotalWeeks,
             'dataxs'=>$chat,
            );
            
         }
        // for ($i=0; $i <7 ; $i++) {
            
            
            
            
        
        // }
        
         return response()->json(['code'=>'200','error'=>false,'message'=>'Record Found','data'=>$weeks,'TotalAssignLead'=>$TotalAssignLead,'TotalAssignWeeklyLead'=>$TotalAssignWeeklyLead,'TotalProjectsLead'=>$TotalProjectsLead]);
        
    }
    
    public function ListSuperDatabaseReporting(Request $request){
        
        ini_set('memory_limit','-1');
        
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "15";
        $page = $request->page ?? "1";
        $search = $request->q;
        $startDate = $request->fdate;
        $ToDate = $request->tdate;
        $EmpData = $request->EmpData ?? "";
        $SalesConsul = $request->SalesConsul ?? "";
        $ProjectN = $request->ProjectN ?? "";
        $LeadStatys = $request->LeadStatys ?? "";
        $ReminderStatus = $request->ReminderStatus ?? "";
        
        if($startDate !='' && $startDate !='' && empty($EmpData) && empty($SalesConsul) && empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->orderBy('leadsassigndatabase.id', $orderType)->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->whereHas('getEmployee', function($query) use($request) {
                    if($request->q){
                        $query->where('emp_name','LIKE','%'.$request->q.'%');
                    }
                })->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->orderBy('leadsassigndatabase.id', $orderType)->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->whereHas('getEmployee', function($query) use($request) {
                    if($request->q){
                        $query->where('emp_name','LIKE','%'.$request->q.'%');
                    }
                })->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && empty($SalesConsul) && empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
           
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate !='' && $startDate !='' && !empty($EmpData) && empty($SalesConsul) && empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && !empty($SalesConsul) && empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate !='' && $startDate !='' && !empty($EmpData) && !empty($SalesConsul) && empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.agentid'=>$SalesConsul])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.agentid'=>$SalesConsul])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && empty($SalesConsul) && !empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsimportdatabase.clientProject'=>$ProjectN])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsimportdatabase.clientProject'=>$ProjectN])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && !empty($SalesConsul) && !empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate !='' && $startDate !='' && !empty($EmpData) && empty($SalesConsul) && !empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsimportdatabase.clientProject'=>$ProjectN])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsimportdatabase.clientProject'=>$ProjectN])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate !='' && $startDate !='' && !empty($EmpData) && !empty($SalesConsul) && !empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.agentid'=>$SalesConsul])->where(['leadsimportdatabase.clientProject'=>$ProjectN])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.agentid'=>$SalesConsul])->where(['leadsimportdatabase.clientProject'=>$ProjectN])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && empty($EmpData) && !empty($SalesConsul) && empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
            //  $myArray2 = explode(',', $EmpData);
            // $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate !='' && $startDate !='' && empty($EmpData) && !empty($SalesConsul) && empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
            //  $myArray2 = explode(',', $EmpData);
            // $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$SalesConsul])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$SalesConsul])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && empty($EmpData) && !empty($SalesConsul) && !empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
            //  $myArray2 = explode(',', $EmpData);
            // $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$SalesConsul])->where(['leadsimportdatabase.clientProject'=>$ProjectN])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$SalesConsul])->where(['leadsimportdatabase.clientProject'=>$ProjectN])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && empty($EmpData) && empty($SalesConsul) && !empty($ProjectN) && $LeadStatys == '' && empty($search) && empty($ReminderStatus)){
            
            //  $myArray2 = explode(',', $EmpData);
            // $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && empty($SalesConsul) && empty($ProjectN) && $LeadStatys != '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
           
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && !empty($SalesConsul) && empty($ProjectN) && $LeadStatys != '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
           
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$SalesConsul])->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$SalesConsul])->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate !='' && $startDate !='' && !empty($EmpData) && !empty($SalesConsul) && empty($ProjectN) && $LeadStatys != '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
           
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$SalesConsul])->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.agentid'=>$SalesConsul])->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && empty($EmpData) && empty($SalesConsul) && empty($ProjectN) && $LeadStatys != '' && empty($search) && empty($ReminderStatus)){
           
           
            
            //  $myArray2 = explode(',', $EmpData);
            // $myArray = str_replace(array('[',']'),'',$myArray2);
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && empty($EmpData) && empty($SalesConsul) && !empty($ProjectN) && $LeadStatys != '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
           
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && empty($SalesConsul) && !empty($ProjectN) && $LeadStatys != '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
           
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->whereIn('leadsassigndatabase.agentid',$myArray)->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->whereIn('leadsassigndatabase.agentid',$myArray)->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && empty($EmpData) && !empty($SalesConsul) && !empty($ProjectN) && $LeadStatys != '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
           
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && !empty($SalesConsul) && !empty($ProjectN) && $LeadStatys != '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
           
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && empty($EmpData) && !empty($SalesConsul) && empty($ProjectN) && $LeadStatys != '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
           
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && !empty($SalesConsul) && empty($ProjectN) && $LeadStatys != '' && empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
           
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where(['leadsimportdatabase.clientProject'=>$ProjectN])->where(['leadsassigndatabase.shiftStatus'=>$LeadStatys])->whereIn('leadsassigndatabase.agentid',$myArray)->where(['leadsassigndatabase.agentid'=>$SalesConsul])->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && empty($SalesConsul) && empty($ProjectN) && $LeadStatys == '' && !empty($search) && empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
           
          
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->where('leadsimportdatabase.clientPhone','LIKE','%'.$search.'%')->orWhere('leadsimportdatabase.clientName','LIKE','%'.$search.'%')->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->where('leadsimportdatabase.clientPhone','LIKE','%'.$search.'%')->orWhere('leadsimportdatabase.clientName','LIKE','%'.$search.'%')->whereIn('leadsassigndatabase.agentid',$myArray)->orderBy('leadsassigndatabase.id', $orderType)->count();
            
        }else if($startDate =='' && $startDate =='' && !empty($EmpData) && empty($SalesConsul) && empty($ProjectN) && $LeadStatys == '' && empty($search) && !empty($ReminderStatus)){
            
             $myArray2 = explode(',', $EmpData);
            $myArray = str_replace(array('[',']'),'',$myArray2);
            
            if($ReminderStatus == 'touch'){
                
                $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->has('getreminder')->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->has('getreminder')->orderBy('leadsassigndatabase.id', $orderType)->count();
                
            }else if($ReminderStatus == 'untouch'){
                
                $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->doesntHave('getreminder')->orderBy('leadsassigndatabase.id', $orderType)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->doesntHave('getreminder')->orderBy('leadsassigndatabase.id', $orderType)->count();
                
            }else{
                $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->whereIn('leadsassigndatabase.agentid',$myArray)->orderBy('leadsassigndatabase.id', $orderType)->count();
            }
            
            
            
        }else{
           
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->orderBy('leadsassigndatabase.id', $orderType)->whereHas('getEmployee', function($query) use($request) {
                    if($request->q){
                        $query->where('emp_name','LIKE','%'.$request->q.'%')
                                ->orWhere('leadsimportdatabase.clientPhone','LIKE','%'.$request->q.'%');
                    }
                })->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee','getreminder' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->orderBy('leadsassigndatabase.id', $orderType)->whereHas('getEmployee', function($query) use($request) {
                    if($request->q){
                        $query->where('emp_name','LIKE','%'.$request->q.'%');
                    }
                })->count();
            
            
        }
        
        
            
        
        
        $AssingDtaa = []; 
        
        foreach($AssignLeadsAlllx as $data){
            
            
            $leadStatux = "";
if($data->shiftStatus == 0){
    $leadStatux = "Assign Leads";
}else if($data->shiftStatus == 1){
    $leadStatux = "My Leads";
}else if($data->shiftStatus == 2){
    $leadStatux = "No Interested";
}else{
    $leadStatux="";
}
 
if($data->leadType == 'duplicate'){
    $LeadTypes = $data->leadType;
    
    $condGetId = LeadImportDatabase::join('leadsassigndatabase', 'leadsassigndatabase.leadid', '=', 'leadsimportdatabase.id')->where(['clientPhone'=>$data->clientPhone])->get();
    
    $myDuplocate = [];
    
    foreach($condGetId as $idData){
        $Employee = Employee::where(['id'=>$idData->agentid])->first();
        
        if($idData->shiftStatus == 0){
            $statItem = "Assign Leads";
        }else if($idData->shiftStatus == 1){
            $statItem = "My Leads";
        }else if($idData->shiftStatus == 2){
            $statItem = "Not Interested";
        }
        $myDuplocate[]=array(
          'agentName' => $Employee == null || $Employee == '' ? '' : $Employee->emp_name,
          'LeadTypes' => $statItem,
        );
    }
    
    $agName = $myDuplocate;
}else{
    $LeadTypes = $data->leadType;
    $agName = null;
}


$Zongcalling = Zongcalling::where('phone','LIKE','%'.$data->clientPhone.'%')->latest()->first();

$ListMyLead = ListMyLead::where(['leadid'=>$data->leadid])->first();

if($ListMyLead == '' || $ListMyLead == null){
    $CustomerActivitydata="";
    $CustomerActivity="";
}else{
    $CustomerActivitydata = $ListMyLead->clientActivityDesc;
    $CustomerActivity = CustomerActivity::where(['agentid'=>$data->agentid,'customerId'=>$ListMyLead->id])->latest()->first();

}



$AssingDtaa[]=array(
    "id"=>$data->id,
    "leadid"=>$data->leadid,
    'clientName' => $data->clientName,
    'clientPhone' => $data->clientPhone,
    'clientemail' => $data->clientemail,
    'clientProject' => $data->clientProject,
    'emp_name' => $data->getEmployee == '' || $data->getEmployee == null ? '' : $data->getEmployee->emp_name,
    "getreminder" => $data->getreminder,
    'assignDate' => $data->publishAgentDate,
    'duplicateStatus' => $data->leadType,
    'duplicateEmployeeName' => $agName,
    'leadStatus' => $LeadTypes,
    'nature' => $data->nature,
    'LeadnatureStatus' => $leadStatux,
    "shiftStatus"=>$data->shiftStatus,
    'transferStatus' => '',
    "recording"=>$Zongcalling == '' || $Zongcalling == null ? '' : $Zongcalling->recording,
    "datetime"=>$Zongcalling == '' || $Zongcalling == null ? '' : $Zongcalling->datetime,
    "CustomerActivity"=>$CustomerActivity == '' || $CustomerActivity == null ? $CustomerActivitydata : $CustomerActivity->activityDetails,
    "customerActivityDate"=>$CustomerActivity == '' || $CustomerActivity == null ? $ListMyLead == '' || $ListMyLead == null ? '' : date("Y-m-d h:i:s a",strtotime($ListMyLead->created_at)) : date("Y-m-d h:i:s a",strtotime($CustomerActivity->created_at)),
    'incommingPhone' => $data->getEmployee == '' || $data->getEmployee == null ? '' : $data->getEmployee->incommingPhone,
    'outgoingPhone' => $data->getEmployee == '' || $data->getEmployee == null ? '' : $data->getEmployee->outgoingPhone,
);
        }
        
        return response()->json(['code'=>'200','error'=>false,'message'=>'Record Found','data'=>$AssingDtaa,'orderBy' => $orderBy,
                    'orderType' => $orderType,'perPage' => $perPage,'pageNo' => $page, 'TotalCount'=>$count,]);
    }
    
    
    
    public function ListSuperDatabasesReporting(Request $request){
        
        ini_set('memory_limit','-1');
        
        $orderBy = $request->orderBy ?? "id";
        $orderType = $request->orderType ?? "desc";
        $perPage = $request->perPage ?? "20";
        $page = $request->page ?? "1";
        $search = $request->q;
        $startDate = $request->fdate;
        $ToDate = $request->tdate;
        
        if($startDate !='' && $startDate !=''){
            
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->orderBy('leadsassigndatabase.id', $orderType)->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->whereHas('getEmployee', function($query) use($request) {
                    if($request->q){
                        $query->where('emp_name','LIKE','%'.$request->q.'%');
                    }
                })->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->orderBy('leadsassigndatabase.id', $orderType)->whereBetween(DB::raw('DATE(publishAgentDate)'), [$startDate, $ToDate])->whereHas('getEmployee', function($query) use($request) {
                    if($request->q){
                        $query->where('emp_name','LIKE','%'.$request->q.'%');
                    }
                })->count();
            
        }else{
            $AssignLeadsAlllx = ListAssignLeads::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->orderBy('leadsassigndatabase.id', $orderType)->whereHas('getEmployee', function($query) use($request) {
                    if($request->q){
                        $query->where('emp_name','LIKE','%'.$request->q.'%')
                                ->orWhere('leadsimportdatabase.clientPhone','LIKE','%'.$request->q.'%');
                    }
                })->paginate($perPage);
                
            $count = ListAssignLeads::with(['getEmployee' => function ($query){
                $query->orderBy('id', 'desc')->get();
            } ,])->join('leadsimportdatabase','leadsimportdatabase.id','=','leadsassigndatabase.leadid')->orderBy('leadsassigndatabase.id', $orderType)->whereHas('getEmployee', function($query) use($request) {
                    if($request->q){
                        $query->where('emp_name','LIKE','%'.$request->q.'%');
                    }
                })->count();
        }
        
        
            
        
        
        $AssingDtaa = []; 
        
        foreach($AssignLeadsAlllx as $data){
            
            $leadStatux = "";
            if($data->shiftStatus == 0){
                $leadStatux = "Assign Leads";
            }else if($data->shiftStatus == 1){
                $leadStatux = "My Leads";
            }else if($data->shiftStatus == 2){
                $leadStatux = "No Interested";
            }else{
                $leadStatux="";
            }
            
            if($data->leadType == 'duplicate'){
                $LeadTypes = $data->leadType;
                
                $condGetId = LeadImportDatabase::join('leadsassigndatabase', 'leadsassigndatabase.leadid', '=', 'leadsimportdatabase.id')->where(['clientPhone'=>$data->clientPhone])->get();
                
                $myDuplocate = [];
                
                foreach($condGetId as $idData){
                    $Employee = Employee::where(['id'=>$idData->agentid])->first();
                    
                    if($idData->shiftStatus == 0){
                        $statItem = "Assign Leads";
                    }else if($idData->shiftStatus == 1){
                        $statItem = "My Leads";
                    }else if($idData->shiftStatus == 2){
                        $statItem = "Not Interested";
                    }
                    $myDuplocate[]=array(
                      'agentName' => $Employee == null || $Employee == '' ? '' : $Employee->emp_name,
                      'LeadTypes' => $statItem,
                    );
                }
                
                $agName = $myDuplocate;
            }else{
                $LeadTypes = $data->leadType;
                $agName = null;
            }
            
            $AssingDtaa[]=array(
                'clientName' => $data->clientName,
                'clientPhone' => $data->clientPhone,
                'clientemail' => $data->clientemail,
                'clientProject' => $data->clientProject,
                'emp_name' => $data->getEmployee == '' || $data->getEmployee == null ? '' : $data->getEmployee->emp_name,
                'assignDate' => $data->publishAgentDate,
                'duplicateStatus' => $data->leadType,
                'duplicateEmployeeName' => $agName,
                'leadStatus' => $LeadTypes,
                'nature' => $data->nature,
                'LeadnatureStatus' => $leadStatux,
                'transferStatus' => '',
            );
        }
        
        return response()->json(['code'=>'200','error'=>false,'message'=>'Record Found','data'=>$AssingDtaa,'orderBy' => $orderBy,
                    'orderType' => $orderType,'perPage' => $perPage,'pageNo' => $page, 'TotalCount'=>$count,]);
    }
    
    
    public function ListSuperDatabaseExport(Request $request){
        
        ini_set('memory_limit','-1');
       
        
        $q = $request->q;
        $fdate = $request->fdate;
        $tdate = $request->tdate;
        
        return Excel::download(new SuperDatabaseExport($q,$fdate,$tdate), 'users.xlsx');
    }

    
}
