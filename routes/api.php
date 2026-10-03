<?php

use App\Http\Controllers\Api\AssignLeadsList;
use App\Http\Controllers\Api\ListMyLeads;
use App\Http\Controllers\Api\ListHotLeads;
use App\Http\Controllers\Api\ListNotInterestedLeads;
use App\Http\Controllers\Api\ListCustomerActivity;
use App\Http\Controllers\Api\EmployeeLogin;
use App\Http\Controllers\Api\LeadsController;
use App\Http\Controllers\Api\ListReminders;
use App\Http\Controllers\Api\TransferLeads;
use App\Http\Controllers\Api\SalesInventories;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AssignLeadExportController;
use App\Http\Controllers\Api\KpiReportController;
use App\Http\Controllers\Api\ReminderLogController;
use App\Http\Controllers\Api\ListExportSheetController;
use App\Http\Controllers\Api\SingleCustomerlistController;
use App\Http\Controllers\Api\ListCallingExportSheet;
use App\Http\Controllers\Api\Listsuperdatabasecontroller;



use App\Http\Controllers\Api\HR\DepartmentController;
use App\Http\Controllers\Api\HR\DesignationController;
use App\Http\Controllers\Api\HR\BranchController;
use App\Http\Controllers\Api\HR\CompanyController;
use App\Http\Controllers\Api\HR\EmployeeController;
use App\Http\Controllers\Api\HR\WarningController;
use App\Http\Controllers\Api\HR\TerminationController;
use App\Http\Controllers\Api\HR\PromotionController;
use App\Http\Controllers\Api\HR\LeaveTypeController;
use App\Http\Controllers\Api\HR\LeaveApplicationController;
use App\Http\Controllers\Api\HR\JobPostController;
use App\Http\Controllers\Api\HR\JobCandidateController;
use App\Http\Controllers\Api\HR\NoticeBoardController;
use App\Http\Controllers\Api\HR\TrainingTypeController;
use App\Http\Controllers\Api\HR\TrainingController;
use App\Http\Controllers\Api\HR\LeaveBalanceController;
use App\Http\Controllers\Api\HR\AttendanceController;
use App\Http\Controllers\Api\HR\EmployeeShiftController;
use App\Http\Controllers\Api\HR\HolydaysController;
use App\Http\Controllers\Api\HR\PayrollController;
use App\Http\Controllers\Api\HR\ReportToController;
use App\Http\Controllers\Api\HR\EmployeemanagerController;
use App\Http\Controllers\Api\HR\ItemTypeController;
use App\Http\Controllers\Api\HR\AssetlocationController;
use App\Http\Controllers\Api\HR\CircularpolicyController;
use App\Http\Controllers\Api\HR\CircularcategoryController;
use App\Http\Controllers\Api\HR\Complainmanagement;
use App\Http\Controllers\Api\HR\Level1Controller;
use App\Http\Controllers\Api\HR\DimensionItemLevel1Controller;
use App\Http\Controllers\Api\HR\Level3ItemListController;
use App\Http\Controllers\Api\HR\Level3ItemDimensionListController;



use App\Http\Controllers\Api\Finance\SecurityFilesController;
use App\Http\Controllers\Api\Finance\DocumentsFilesController;
use App\Http\Controllers\Api\Finance\CallingGraphController;

use App\Http\Controllers\Api\Finance\BankListController;
use App\Http\Controllers\Api\Finance\StockRegisterController;
use App\Http\Controllers\Api\Finance\StockOutRegisterController;
use App\Http\Controllers\Api\Finance\PaymentrecieptController;
use App\Http\Controllers\Api\Finance\PaymentPlanController;
use App\Http\Controllers\Api\Finance\PaymentPlanDataController;
use App\Http\Controllers\Api\Finance\AssetRegisterController;
use App\Http\Controllers\Api\Finance\ChequeRegisterController;
use App\Http\Controllers\Api\Finance\ChequeOutwardRegister;
use App\Http\Controllers\Api\Finance\OccupationController;
use App\Http\Controllers\Api\Finance\PostDatedChequeController;
use App\Http\Controllers\Api\Finance\PaymentVoucherController;
use App\Http\Controllers\Api\Finance\PayRecieptController;
use App\Http\Controllers\Api\Finance\SaleFormController;
use App\Http\Controllers\Api\Finance\LeadStatusController;
use App\Http\Controllers\Api\Finance\CallStatusController;
use App\Http\Controllers\Api\Finance\leadimportController;
use App\Http\Controllers\Api\Finance\DimensionController;
use App\Http\Controllers\Api\Finance\GlcodeController;
use App\Http\Controllers\Api\Finance\ListStatusReportController;
use App\Http\Controllers\Api\Finance\ListDashboardController;
use App\Http\Controllers\Api\Finance\PettyCashController;
use App\Http\Controllers\Api\Finance\LeadhistoryController;
use App\Http\Controllers\Api\Finance\LeadtlwhistoryController;
use App\Http\Controllers\Api\Finance\FindashboardController;
use App\Http\Controllers\Api\Finance\CashinflowController;
use App\Http\Controllers\Api\Finance\BankpaymentflowController;
use App\Http\Controllers\Api\Finance\PettycashflowController;
use App\Http\Controllers\Api\Finance\TalktimeHistoryController;
use App\Http\Controllers\Api\Finance\BankDepositController;
use App\Http\Controllers\Api\Finance\BankDepositFlowController;
use App\Http\Controllers\Api\Finance\AssetCompanyflowController;
use App\Http\Controllers\Api\Finance\AssetDepartmentflowController;
use App\Http\Controllers\Api\Finance\AssetTypeflowController;
use App\Http\Controllers\Api\Finance\PurchaserequestformController;
use App\Http\Controllers\Api\Finance\AssetLocationflowController;
use App\Http\Controllers\Api\Finance\BilltypeController;
use App\Http\Controllers\Api\Finance\FinbillsController;
use App\Http\Controllers\Api\Finance\GetpassController;
use App\Http\Controllers\Api\Finance\VehcilePassController;
use App\Http\Controllers\Api\Finance\Chartlevel1Controller;
use App\Http\Controllers\Api\Finance\Chartlevel2Controller;
use App\Http\Controllers\Api\Finance\Chartlevel3Controller;

use App\Http\Controllers\Api\Finance\Dimensionlevel1Controller;
use App\Http\Controllers\Api\Finance\Dimensionlevel2Controller;
use App\Http\Controllers\Api\Finance\Dimensionlevel3Controller;
use App\Http\Controllers\Api\Finance\GenerateMappingController;

use App\Http\Controllers\Api\Finance\BillsummaryflowController;
use App\Http\Controllers\Api\Finance\billdueSummaryFlowController;
use App\Http\Controllers\Api\Finance\vouchertypesController;
use App\Http\Controllers\Api\Finance\VouchertypeflowController;





use App\Http\Controllers\Api\Inventory\LocationController;
use App\Http\Controllers\Api\Inventory\ProjectController;
use App\Http\Controllers\Api\Inventory\ProjectAssetController;
use App\Http\Controllers\Api\Inventory\ProjectCategoryController;
use App\Http\Controllers\Api\Inventory\ProjectInventoryController;
use App\Http\Controllers\Api\Inventory\InventoryHoldController;
use App\Http\Controllers\Api\Inventory\InventorySoldController;
use App\Http\Controllers\Api\Inventory\InventoryfilterdataController;



use App\Http\Controllers\Api\RolesPermission\CrmModuleController;
use App\Http\Controllers\Api\RolesPermission\RoleCategoryController;
use App\Http\Controllers\Api\RolesPermission\CrmModuleFeaturesController;
use App\Http\Controllers\Api\RolesPermission\RolePermissionsModuleController;
use App\Http\Controllers\Api\RolesPermission\EmployeeRoleController;


use App\Http\Controllers\Api\Videorequest\VideoCategoryController;
use App\Http\Controllers\Api\Videorequest\VideoEducationController;
use App\Http\Controllers\Api\Videorequest\VideoRequestController;

use App\Http\Controllers\Api\AffiliateProgram\AffiliateRegisterController;
use App\Http\Controllers\Api\AffiliateProgram\AffiliateLoginController;
use App\Http\Controllers\Api\AffiliateProgram\AffiliateLeadsModuleController;
use App\Http\Controllers\Api\AffiliateProgram\AffiliateActivityModuleController;


use App\Http\Controllers\Api\TaskManagement\TaskController;
use App\Http\Controllers\Api\TaskManagement\TaskChecklistController;
use App\Http\Controllers\Api\TaskManagement\TaskCommentController;
use App\Http\Controllers\Api\TaskManagement\TaskAttachmentController;
use App\Http\Controllers\Api\TaskManagement\TaskHistoryController;

use App\Http\Controllers\Api\WebsiteMaterial\PricingListController;



use App\Http\Controllers\Api\AdminLeadImport;
use App\Http\Controllers\Api\FacebookLeadsController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('login', LoginController::class);
Route::apiResource('singlecustomerlist', SingleCustomerlistController::class);



Route::apiResource('leads', LeadsController::class);

Route::apiResource('kpireport', KpiReportController::class);

Route::apiResource('getReminderLog', ReminderLogController::class);

Route::apiResource('reportto', ReportToController::class);



// MODULE = HR
Route::apiResource('department', DepartmentController::class); //Correct
Route::get('propertytype', [DepartmentController::class,"Getpropertytype"]); //Correct
Route::apiResource('designation', DesignationController::class); //Correct
Route::apiResource('employeeshift', EmployeeShiftController::class); //Correct
Route::apiResource('holydays', HolydaysController::class);
Route::apiResource('branch', BranchController::class); //Correct
Route::get('getduplicateleads', [BranchController::class,"getDuplicateLeads"]);
Route::apiResource('company', CompanyController::class); //Correct
Route::apiResource('employee', EmployeeController::class); //Correct
Route::get('departmentwise/{id}', [EmployeeController::class,"showDepartmentWise"]);

Route::apiResource('warning', WarningController::class); //Correct
Route::apiResource('callinggraphs', CallingGraphController::class); //Correct


Route::apiResource('termination', TerminationController::class); //Correct
Route::apiResource('promotion', PromotionController::class); //Correct 
Route::apiResource('leavetype', LeaveTypeController::class); //Correct 
Route::apiResource('leaveapplication', LeaveApplicationController::class);
Route::apiResource('payroll', PayrollController::class);
Route::apiResource('employeemanagerslist', EmployeemanagerController::class);
Route::apiResource('itemtype', ItemTypeController::class);
Route::apiResource('assetlocation', AssetlocationController::class);
Route::apiResource('circularpolicy', CircularpolicyController::class);
Route::apiResource('circularpolicycategory', CircularcategoryController::class);
Route::apiResource('complainmanagement', Complainmanagement::class);
Route::apiResource('level1Items', Level1Controller::class);
Route::apiResource('dimensionlevelitems', DimensionItemLevel1Controller::class);
Route::apiResource('listitemlevel3', Level3ItemListController::class);
Route::apiResource('listdimensionitemlevel2', Level3ItemDimensionListController::class);


Route::post('employeeleaddistribution', [EmployeemanagerController::class,"employeeleaddistribution"]);

Route::apiResource('assignleadexport', AssignLeadExportController::class);

Route::apiResource('assignleadexport', AssignLeadExportController::class);
Route::get('exportassignleads', [AssignLeadExportController::class,"ListAssignLeadsExport"]);

// MODULE = FINANCE
Route::apiResource('securityfiles', SecurityFilesController::class); //Correct
Route::apiResource('documentlist', DocumentsFilesController::class); //Correct
Route::get('securityfilters', [SecurityFilesController::class,"SecurityFilters"]); //Correct


Route::apiResource('banklist', BankListController::class); //Correct
Route::get('getbanklistdata', [BankListController::class,"getMyBanklist"]);

Route::apiResource('stockregister', StockRegisterController::class); //Correct
Route::apiResource('leadimportreports', leadimportController::class); //Correct
Route::get('exportimportleadsreport', [leadimportController::class,"getAllLeadExport"]);


Route::get('stockfilters', [StockRegisterController::class,"StockFilters"]); //Correct

Route::apiResource('stockoutregister', StockOutRegisterController::class); //Correct
Route::apiResource('paymentreciept', PaymentrecieptController::class); //Correct
Route::apiResource('paymentplan', PaymentPlanController::class); //Correct
Route::apiResource('paymentplandata', PaymentPlanDataController::class); //Correct
Route::apiResource('assetregister', AssetRegisterController::class);
Route::get('assetregisterfilters', [AssetRegisterController::class,"AsssetRegisterFilters"]); //Correct
Route::apiResource('chequeoutwardregister', ChequeOutwardRegister::class);
Route::apiResource('talktimehistory', TalktimeHistoryController::class);

Route::get('chequeoutwardfilters', [ChequeOutwardRegister::class,"ChequeoutwardFilters"]);




Route::apiResource('postdatedcheque', PostDatedChequeController::class);
Route::get('listbanks', [PostDatedChequeController::class,"BankListData"]);
Route::get('postdatedchequefilters', [PostDatedChequeController::class,"PostDatedChequeFilters"]);

Route::apiResource('chequeregister', ChequeRegisterController::class);
Route::apiResource('occupations', OccupationController::class);
Route::apiResource('paymentvoucher', PaymentVoucherController::class);
Route::get('paymentvoucherfilters', [PaymentVoucherController::class,"PaymentVoucherFilters"]);


Route::apiResource('payreciepts', PayRecieptController::class);
Route::apiResource('saleformdata', SaleFormController::class);
Route::apiResource('leadstatusreports', LeadStatusController::class);
Route::apiResource('callstatusreports', CallStatusController::class);
Route::apiResource('listdashboardaction', ListDashboardController::class);
Route::apiResource('leadhistoryaction', LeadhistoryController::class);
Route::apiResource('tlwhistory', LeadtlwhistoryController::class);

Route::apiResource('dimension', DimensionController::class);
Route::apiResource('glcode', GlcodeController::class);
Route::apiResource('liststatusreports', ListStatusReportController::class);
Route::apiResource('pettycash', PettyCashController::class);

Route::post('destroy2/{id}', [PettyCashController::class,"destroy2"]);

Route::apiResource('findashboardlist', FindashboardController::class);
Route::apiResource('cashinflowlist', CashinflowController::class);
Route::apiResource('bankpaymentflowlist', BankpaymentflowController::class);
Route::apiResource('pettycashflowlist', PettycashflowController::class);
Route::apiResource('bankdeposits', BankDepositController::class);
Route::apiResource('bankdepositsflows', BankDepositFlowController::class);
Route::apiResource('assetcompanyflow', AssetCompanyflowController::class);
Route::apiResource('assetdepartmentflow', AssetDepartmentflowController::class);
Route::apiResource('assettypesflow', AssetTypeflowController::class);
Route::apiResource('purchasereqform', PurchaserequestformController::class);
Route::apiResource('purchasereqform', PurchaserequestformController::class);
Route::post('itemdetails', [PurchaserequestformController::class,"itemdetailsDelete"]);
Route::apiResource('assetlocationflow', AssetLocationflowController::class);
Route::apiResource('billstype', BilltypeController::class);
Route::apiResource('finbills', FinbillsController::class);
Route::apiResource('generategatepass', GetpassController::class);
Route::apiResource('vehiclepass', VehcilePassController::class);
Route::apiResource('chartofaccountlevel1', Chartlevel1Controller::class);

Route::apiResource('chartofaccountlevel2', Chartlevel2Controller::class);
Route::apiResource('chartofaccountlevel3', Chartlevel3Controller::class);


Route::apiResource('dimensionlevel1', Dimensionlevel1Controller::class);

Route::apiResource('dimensionlevel2', Dimensionlevel2Controller::class);
Route::apiResource('dimensionlevel3', Dimensionlevel3Controller::class);
Route::apiResource('generatemapping', GenerateMappingController::class);
Route::apiResource('billsummaryflow', BillsummaryflowController::class);
Route::apiResource('billduesummaryflow', billdueSummaryFlowController::class);
Route::apiResource('vouchertypes', vouchertypesController::class);
Route::apiResource('vouchertypesflow', VouchertypeflowController::class);

Route::get('payablebillsfilters', [FinbillsController::class,"payablebillsfilters"]);

Route::get('singlepettyvoucher/{id}', [PettyCashController::class,"pettyVoucherResult"]);


Route::get('paymentrecieptfilters', [PayRecieptController::class,"PaymentRecieptFilters"]);


Route::get('chequelistno/{id}', [ChequeRegisterController::class,'GetChequeListNo']);


// MODULE = WEBSITE MATERIAL
Route::apiResource('pricinglist', PricingListController::class); //Correct


//Correct 
Route::apiResource('jobpost', JobPostController::class); //Correct
Route::apiResource('jobcandidate', JobCandidateController::class); //Correct
Route::apiResource('noticeboard', NoticeBoardController::class); //Correct
Route::apiResource('trainingtype', TrainingTypeController::class); //Correct
Route::apiResource('training', TrainingController::class); //Correct
Route::apiResource('leavebalance', LeaveBalanceController::class); //Correct
Route::apiResource('attendance', AttendanceController::class); //Correct

// MODULE = Inventory
Route::apiResource('location', LocationController::class); //done
Route::apiResource('project', ProjectController::class); //done
Route::post('inventoriesfilters', [ProjectController::class,'Addinventoriesfilters']); //done
Route::get('listinventoriesfilters', [ProjectController::class,'getInventoriesFilters']); //done
Route::apiResource('projectasset', ProjectAssetController::class);
Route::apiResource('projectcategory', ProjectCategoryController::class); //done
Route::apiResource('projectinventory', ProjectInventoryController::class); //done
Route::get('projectwiseblock', [ProjectInventoryController::class,'getProjectWiseBlock']);

Route::get('blockwiseplot', [ProjectInventoryController::class,'getBlockWisePlot']);

Route::get('plotwisesize', [ProjectInventoryController::class,'getPlotWiseSize']);

Route::get('customersolddata', [ProjectInventoryController::class,'getCustomerSold']);

Route::get('customersoldetails', [ProjectInventoryController::class,'getCustomerSoldDetails']);
Route::get('exportInventoriesreport', [ProjectInventoryController::class, 'ListInventoriesExport']);

// Module Permission
Route::apiResource('crmmodule', CrmModuleController::class); //done
Route::apiResource('rolecategory', RoleCategoryController::class); //done
Route::apiResource('modulefeature', CrmModuleFeaturesController::class); //done
Route::apiResource('rolepermission', RolePermissionsModuleController::class); //done
Route::apiResource('employeerole', EmployeeRoleController::class); //done


Route::apiResource('listinventoryfilterdata', InventoryfilterdataController::class);

Route::get('getPropertyNo', [InventoryfilterdataController::class,'getPropertyNo']);
Route::get('getPropertyDetails', [InventoryfilterdataController::class,'getPropertyDetails']);

// MODULE = VideoRequestForm
Route::apiResource('videocategory', VideoCategoryController::class); //done
Route::apiResource('education', VideoEducationController::class); //done
Route::apiResource('videorequest', VideoRequestController::class); //done
Route::post('videoformstatusupdate', [VideoRequestController::class, 'VideoFormUpdate']);

Route::apiResource('inventoryhold', InventoryHoldController::class); //done
Route::apiResource('inventorysold', InventorySoldController::class); //done
Route::get('inventorysoldtesting', [InventorySoldController::class,'InventorySoldTest']); //done
Route::post('fileStatus', [InventorySoldController::class,'FileStatus']); //done

Route::post('inventoryUnhold', [InventorySoldController::class,'inventoryUnhold']); //done
Route::post('inventoryCancelled', [InventorySoldController::class,'inventoryCancelled']); //done

Route::get('exportdealdonereport', [InventorySoldController::class, 'ListInventoriesSoldExport']);

// MODULE = AffiliateRegistration
Route::apiResource('affiliateprogram', AffiliateRegisterController::class); //done
Route::apiResource('affiliatelogin', AffiliateLoginController::class); //done
Route::apiResource('affiliateleads', AffiliateLeadsModuleController::class); //done
Route::apiResource('affiliateactivity', AffiliateActivityModuleController::class); //done


// MODULE = TaskModule
Route::apiResource('task', TaskController::class); //done
Route::get('selftasklist', [TaskController::class,'getSelfTask']); //done
Route::post('taskchangeLog', [TaskController::class,'taskchangeLog']); //done
Route::post('taskchecklistlog', [TaskController::class,'taskchecklistLog']); //done


Route::apiResource('taskchecklist', TaskChecklistController::class); //done
Route::apiResource('taskcomment', TaskCommentController::class); //done
Route::apiResource('taskattachement', TaskAttachmentController::class); //done
Route::apiResource('taskhistory', TaskHistoryController::class); //done
Route::post('taskstatus', [AssignLeadsList::class,'TaskStatusChange']); //done
Route::post('taskassigned', [AssignLeadsList::class,'TaskAssignedChange']); //done
Route::post('taskpriority', [AssignLeadsList::class,'TaskPriorityChange']); //done

Route::post('ZongDataUpdate', [AssignLeadsList::class,'ZongDataUpdate']);

Route::apiResource('facebookleads', FacebookLeadsController::class); //done


Route::post('optverification', [AffiliateRegisterController::class,'OtpVerification']); //done

Route::get('listrolepermission', [AssignLeadsList::class, 'getRolePermission']);


Route::post('forgotpassword', [EmployeeController::class,'ForgotPassword']); //Correct
Route::post('employeeStatus', [EmployeeController::class,'employeeStatus']); //Correct

Route::post('resetpassword', [EmployeeController::class,'ResetPassword']);

Route::get('listassignleads', [AssignLeadsList::class, 'assignList']);
Route::get('listassignleadstestapi', [AssignLeadsList::class, 'assignListTestApi']);
Route::get('listwhatsappactivity', [AssignLeadsList::class, 'WHatsappACtivity']);

Route::get('listemployementtype', [AssignLeadsList::class, 'getEmployementType']);

Route::get('listholydays', [AssignLeadsList::class, 'getHolyDays']);

Route::get('listempshift', [AssignLeadsList::class, 'getEmpShift']);

Route::post('assignreminder', [AssignLeadsList::class, 'assignReminders']);
Route::get('callingimpression', [AssignLeadsList::class, 'assignCallingImpression']);
Route::post('assignnotinterested', [AssignLeadsList::class, 'assignNotInterested']);
Route::post('assignleadadd', [AssignLeadsList::class, 'assignAddLeads']);
Route::post('assignleadtransfer', [AssignLeadsList::class, 'assignTransferLeads']);
Route::post('leadsDeleteData', [AssignLeadsList::class, 'leadsDeleteData']);
Route::post('leadsTransferData', [AssignLeadsList::class, 'leadsTransferData']);

Route::post('leadimpression', [AssignLeadsList::class, 'assignLeadImpression']);
Route::get('assigncountry', [AssignLeadsList::class, 'getCountryApi']);
Route::get('assigncity', [AssignLeadsList::class, 'getCountryCityWiseApi']);
Route::get('assignprojects', [AssignLeadsList::class, 'getCityProjectsWiseApi']);
Route::get('assignprotype', [AssignLeadsList::class, 'getProtypeProjectsWiseApi']);
Route::get('assignnature', [AssignLeadsList::class, 'getNatureTypeWiseApi']);

Route::get('projectlist', [AssignLeadsList::class, 'getProjectsListApi']);

Route::get('listvirginleads', [AssignLeadsList::class, 'assignVirginList']);
Route::get('listformdata', [AssignLeadsList::class, 'assignClientStatusList']);

Route::get('listemployee', [AssignLeadsList::class, 'EmployeeListData']);
Route::get('listnotinterestedreason', [AssignLeadsList::class, 'NotInterestedReasonOpt']);

Route::get('listzonecalling', [AssignLeadsList::class, 'getZongCallingData']);
Route::post('facebookchat', [AssignLeadsList::class, 'facebookchatFunc']);

Route::get('listvirginleadsexport', [AssignLeadsList::class, 'ListVirginLeadsExport']);

Route::get('listmyleads', [ListMyLeads::class, 'listMyLeads']);
Route::get('singlemyleads/{id}', [ListMyLeads::class, 'singleMylist']);

Route::post('myleadstatushot', [ListMyLeads::class, 'listMyLeadsStatusHot']);
Route::post('myleadactivity', [ListMyLeads::class, 'listMyLeadsActivity']);
Route::post('myleadactivityReminder', [ListMyLeads::class, 'listMyLeadsActivityReminder']);
Route::get('myleadactivitytype', [ListMyLeads::class, 'getActivityTypeApi']);
Route::get('myleadactivityparenttype', [ListMyLeads::class, 'getActivityTypeParnetApi']);
Route::get('myleadactivityoutcome', [ListMyLeads::class, 'getActivityOutcomeApi']);
Route::post('myleadshareleads', [ListMyLeads::class, 'listMyLeadsShareLeads']);
Route::post('myleadsnotinterested', [ListMyLeads::class, 'listMyLeadsNotinterested']);



Route::get('listhotleads', [ListHotLeads::class, 'listHotLeads']);
Route::get('listnotinterestedleads', [ListNotInterestedLeads::class, 'listNotInterestedLeads']);
Route::get('listactivity', [ListCustomerActivity::class, 'ActivityList']);
Route::get('listdashboardcount', [EmployeeLogin::class, 'DashboardSalesCount']);
Route::post('notification', [EmployeeLogin::class, 'GetNotificationFirebase']);


Route::get('listreminders', [ListReminders::class, 'ListRemindersData']);

Route::get('listanalytics', [ListReminders::class, 'ListAnalytics']);
Route::get('listdailyleadsreporting', [ListReminders::class, 'DailyLeadsReporting']);
Route::get('listsuperdatabasereporting', [ListReminders::class, 'ListSuperDatabaseReporting']);
Route::get('listsuperdatabasesreportings', [ListReminders::class, 'ListSuperDatabasesReporting']);

Route::apiResource('listsuperdatabasereportings', Listsuperdatabasecontroller::class); //done
Route::get('listsuperdatabaseexport', [ListExportSheetController::class, 'ListSuperDatabaseExport']);
Route::get('listcallingdataexport', [ListCallingExportSheet::class, 'index']);


Route::get('listransferleads', [TransferLeads::class, 'listTransferLeads']);
Route::get('listtransferexport', [TransferLeads::class, 'ListTransferExport']);

Route::get('listinventories', [SalesInventories::class, 'listInventoriesData']);

Route::get('importleads',[AdminLeadImport::class,'index']);
Route::post('importleads',[AdminLeadImport::class,'leadSaveImport']);
Route::post('importattendance',[AdminLeadImport::class,'AttendanceSaveImport']);
Route::post('assignconsultantleads',[AdminLeadImport::class,'assignconsultantleadsData']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
