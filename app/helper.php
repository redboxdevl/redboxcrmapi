<?php

use App\Models\LeadImpression;

//import All Custom Functionality

function GetImpressionCount($agentid,$leadid,$type,$companyId){
    if($leadid == 0){
        $leadImpPhone = LeadImpression::where(['agentid'=>$agentid,'impType'=>$type,'companyId'=>$companyId])->count();
    }else{
        $leadImpPhone = LeadImpression::where(['agentid'=>$agentid,'leadid'=>$leadid,'impType'=>$type,'companyId'=>$companyId])->count();
    }
    
    return $leadImpPhone;
}
function GetLeadStatusCheck($agentid,$leadid){
    $leadImpCond = LeadImpression::where(['agentid'=>$agentid,'leadid'=>$leadid])->get();
    return $leadImpCond;
}


