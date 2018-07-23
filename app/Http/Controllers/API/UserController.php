<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\Ati_sausers; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use DB;
 

/*use App\Http\Requests;
use Auth;
//use DB;
//use Illuminate\Support\Facades\Validator;
use Input;
use Redirect;
use Session;
use PDO;
*/
class UserController extends Controller 
{
public $successStatus = 200;
/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){ 
               
       $userdata = array(
            'AUSER_NAME'   => request('email'),
            'ASTATUS_FG'   => '1',
            'password'     =>request('password')
            );
        if (Auth::attempt($userdata)) {
            $user = Auth::User();
             
            return response()->json([
                            'success'=>true,                                      
                            'details'=>$user
                        ]);

        }                    
    }
    
    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
                    return response()->json(['error'=>$validator->errors()], 401);            
                }
        $input = $request->all(); 
                $input['password'] = bcrypt($input['password']); 
                $user = User::create($input); 
                $success['token'] =  $user->createToken('MyApp')-> accessToken; 
                $success['name'] =  $user->name;
        return response()->json(['success'=>$success], $this-> successStatus); 
    }

    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function empMovementView() 
    { 
        
        $allClients=DB::table('ATI_CLIENTS')
          ->where('ATI_CLIENTS.ASTATUS_FG','=',1)
          ->orderBy('ATI_CLIENTS.CLIENTS_ID','=','ATI_CLIENTS.CLIENTS_ID')
          ->get();

        $allProject=DB::table('ATI_PROJECT')
        ->where('ATI_PROJECT.ASTATUS_FG','=',1)
        ->orderBy('ATI_PROJECT.PROJECT_ID','=','ATI_PROJECT.PROJECT_ID')
        ->get(); 

        $allEmployee=DB::table('HR_EMPLOYEE')
        ->where('HR_EMPLOYEE.ASTATUS_FG','=',1)
        ->orderBy('HR_EMPLOYEE.EMPLOYE_ID','=','HR_EMPLOYEE.EMPLOYE_ID')
        ->get(); 
                                    
        $movementStartVisit=DB::table('HRV_VISTFRM')
        ->where('HRV_VISTFRM.ASTATUS_FG','=',1)
        ->get();

        $movementTitle=DB::table('HRV_OMTITLE')
        ->select('HRV_OMTITLE.*')
        ->where('HRV_OMTITLE.ASTATUS_FG','=','1')
        ->get();        
        $movementDtyp=DB::table('HRV_MVTDTYP')
        ->select('HRV_MVTDTYP.*')
        ->where('HRV_MVTDTYP.ASTATUS_FG','=','1')
        ->get();
        
        return response()->json([
                                    'success'=>true, 
                                    'clients'=>$allClients,
                                    'projects'=>$allProject,
                                    'employee'=>$allEmployee,
                                    'movementStartVisit'=>$movementStartVisit,
                                    'movementTitle'=>$movementTitle,
                                    'movementDtyp'=>$movementDtyp

                                     
                                ]);
         
    } 
     /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function saveEmpMovement(Request $request) 
    { 
       
        $input=  $request->json()->all();
        $PRIMARY_IDD=DB::select(DB::raw("SELECT
        FNC_primekey('SEQ_ATI_EMP_MOVEMENT','1') NEXT_PK from dual"));
        $PRIMARY_ID = $PRIMARY_IDD[0]->NEXT_PK;
        DB::table('HR_MOVT_REC')->insert([
        'MOVTREC_ID' => $PRIMARY_ID,
        'MOVT_LADDR' => $input['ClientAddress'],
        'CLIENTS_ID' => $input['clientName'],
        'A_DURATION' => $input['durationTime'],
        'MOVEMNT_DT' =>date('Y-m-d', strtotime( $input['fromdate'])),
        'EMOVT_DESC' => $input['movementPurpose'],
        'OMTITLE_ID' => $input['movementTitle'],
        'PRESENCEON' => $this->convertSecond($input['presenceOn']),
        'PROJECT_ID' => $input['projectName'],
        'MOVT_ET_DT' => $input['todate'],
        'VISITS_FRM' => $input['startVisit'],
        'MVTDTYP_ID' => $input['durationType'],
        'M_LATITUDE' => $input['latitude'],
        'M_LONGITDE' => $input['longitde']                                       ]);

                $employeeNameArray= explode(",",$input['employeeAllName']);
                //print_r($employeeNameArray);exit;
                foreach ($employeeNameArray as $row) {
                  
                    $PRIMARY_DTL=DB::select(DB::raw("SELECT FNC_primekey('SEQ_ATI_EMP_MOVEMENT_DTL','1') NEXT_PK from dual"));
                    $PRIMARY_ID_DTL = $PRIMARY_DTL[0]->NEXT_PK;
                   DB::table('HR_MOVT_EMP')->insert([
                                                    'MOVTEMP_ID' => $PRIMARY_ID_DTL,
                                                    'MOVTREC_ID' => $PRIMARY_ID,
                                                    'EMPLOYE_ID' => $row,
                                                    'MOVEMNT_DT' => date('Y-m-d', strtotime( $input['fromdate'])), 
                                                  ]);
                   
                 } 
                  
                return response()->json([
                                            'success'=>true,
                                              
                                        ]);
                 
            }
            public static function convertSecond($conSecond){
            $secondCal= date("H:i", strtotime($conSecond));
            $second=strtotime($secondCal)- strtotime('TODAY');
            return $second;
    }
     public function empWiseMovementList($employee_id) 
    { 

        
        $empWiseMovList=DB::select(DB::raw("SELECT distinct(mr.MOVTREC_ID),mr.OMTITLE_ID ,mr.EMOVT_DESC,mr.MOVEMNT_DT,mr.ACTON_TIME,mr.A_DURATION,mr.PRESENCEON,mr.NOOFPERSON,mr.CREATED_BY,
                                            mr.MOVT_SF_DT,mr.MOVT_STIME,mr.MOVT_ET_DT,mr.MOVT_ETIME,mr.MVERIFY_FG,mr.CLIENTS_ID,mr.PROJECT_ID,mr.VISITS_FRM,mr.ASTATUS_FG,cli.CLINT_NAME,pro.PROJT_NAME,omt.OMTTL_NAME,vf.SVIST_NAME
                                            from HR_MOVT_REC mr
                                            left join ati_clients cli on mr.CLIENTS_ID=cli.CLIENTS_ID
                                            left join ati_project pro on mr.PROJECT_ID=pro.PROJECT_ID
                                            left join HRV_OMTITLE omt on mr.OMTITLE_ID=omt.OMTITLE_ID
                                            left join HRV_VISTFRM vf on mr.VISITS_FRM=vf.VISTFRM_ID
                                            left join HR_MOVT_EMP memp on mr.MOVTREC_ID=memp.MOVTREC_ID
                                            where memp.EMPLOYE_ID=$employee_id or mr.CREATED_BY=$employee_id
                                            order by mr.MOVTREC_ID desc"));

         
        return response()->json([
                                    'success'=>true, 
                                    'empWiseMovList'=>$empWiseMovList
                                     
                                ]);
         
    }  

}

