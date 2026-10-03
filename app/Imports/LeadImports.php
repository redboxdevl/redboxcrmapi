<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\LeadImportDatabase;
use App\Models\Leadcampaign;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Http\Request;

class LeadImports implements ToCollection , WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
        $nature = request()->post('nature');
        // $leadSourceId = request()->post('leadSourceId');
        // $leadProid = request()->post('leadProid');
        // $creatorId = request()->post('creatorId');
        // $supervisorId = request()->post('supervisorId');
        // $adSpend = request()->post('adSpend');
        // $cpl = request()->post('cpl');
        // $campaignName = request()->post('campaignName');
        $companyId = request()->post('companyId');
        
        $existingData = LeadImportDatabase::pluck('clientPhone')->toArray();

        foreach ($collection as $key => $value) {

            $name = $value['name'];
            $email = $value['email'];
            $phone = $value['phone'];
            $project = $value['project'];
            $designation = $value['designation'];
            $gender = $value['gender'];
            
            $duplicateMatch = "";
            
            if(in_array($phone,$existingData)){
                $duplicateMatch="duplicate";
            }else{
                 $duplicateMatch="unique";
            }
            
            // $duplicateMatch = LeadImportDatabase::where(['clientPhone'=>$phone,'companyId'=>$companyId])->first();
            
            
            $leadImport = new LeadImportDatabase();
            
            $leadImport->clientName = $name;
            $leadImport->clientPhone = $phone;
            $leadImport->clientemail = $email == '' || $email == null ? '@' : $email;
            $leadImport->clientProject = $project;
            $leadImport->clientDesignation = $designation == '' || $designation == null ? '-' : $designation;
            $leadImport->leadGender = $gender == '' || $gender == null ? '-' : $gender;
            $leadImport->nature = $nature;
            $leadImport->leadStatus = 'inactive';
            $leadImport->leadSourceId = '-';
            $leadImport->leadProid = '-';
            $leadImport->creatorId =  '-';
            $leadImport->supervisorId =  '-';
            $leadImport->adSpend =  '-';
            $leadImport->cpl =  '-';
            $leadImport->campaignName =  '-';
            $leadImport->leadType = $duplicateMatch;
            $leadImport->companyId = $companyId;
            
            $leadImport->save();
            
        }
        
        
        // $Leadcampaign = new Leadcampaign();
        
        // $Leadcampaign->leadSourceId = $leadSourceId == '' || $leadSourceId == null ? '-' : $leadSourceId;
        // $Leadcampaign->proid = $leadProid == '' || $leadProid == null ? '-' : $leadProid;
        // $Leadcampaign->creatorId = $creatorId == '' || $creatorId == null ? '-' : $creatorId;
        // $Leadcampaign->supervisorId = $supervisorId == '' || $supervisorId == null ? '-' : $supervisorId;
        // $Leadcampaign->adSpent = $adSpend == '' || $adSpend == null ? '-' : $adSpend;
        // $Leadcampaign->CPL = $cpl == '' || $cpl == null ? '-' : $cpl;
        // $Leadcampaign->CampaignName = $campaignName == '' || $campaignName == null ? '-' : $campaignName;
        
        // $Leadcampaign->save();
       
    }
}
