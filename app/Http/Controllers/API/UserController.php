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
            'AUSER_NAME'   => request('AUSER_NAME'),
            'ASTATUS_FG'   => '1',
            'password'     =>request('UPASSWORDS')
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
                                    
        $allMovefrom=DB::table('HRV_VISTFRM')
        ->where('HRV_VISTFRM.ASTATUS_FG','=',1)
        ->get();
        
        return response()->json([
                                    'success'=>true, 
                                    'clients'=>$allClients,
                                    'projects'=>$allProject,
                                    'employee'=>$allEmployee,
                                    'moveTitle'=>$allMovefrom

                                     
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

      print_r($input);exit;
      //  echo $input[0]['CLIENTS_ID'];exit;   
        
        $PRIMARY_IDD=DB::select(DB::raw("SELECT FNC_primekey('SEQ_ATI_EMP_MOVEMENT','1') NEXT_PK from dual"));
        $PRIMARY_ID = $PRIMARY_IDD[0]->NEXT_PK;
        DB::table('HR_MOVT_REC')->insert([
                                        'MOVTREC_ID' => $PRIMARY_ID,                                       
                                        'CLIENTS_ID' => $input[0]->CLIENTS_ID,
                                        'PROJECT_ID' => $input[0]->PROJECT_ID

                                      ]);
        return response()->json([
                                    'success'=>true 
                                ]);
         
    } 

}