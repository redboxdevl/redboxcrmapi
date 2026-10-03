<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Videorequest extends Model
{
    use HasFactory;
    protected $table ="videorequests";
    
    protected $fillable = [
        'emp_id',
        'video_category_id',
        'project_id',
        'video_format',
        'video_topic',
        'guest_name',
        'other_category',
        'gender',
        'age_group',
        'education',
        'location',
        'video_outline',
        'video_instructions',
        'video_ref_image',
        'video_ref_ppt',
        'video_ref_pdf',
        'video_ref_web',
        'video_ref_video',
        'status',
    ];
    
    
    public function getcategories(){
        return $this->belongsTo(Videocategory::class,'video_category_id','id');
    }
     public function getProjects(){
        return $this->belongsTo('App\Models\Inventory\Project'::class,'project_id','id');
    }
    
     public function getEmployee(){
        return $this->belongsTo('App\Models\HR\Employee'::class,'emp_id','id');
    }
    
    
}
