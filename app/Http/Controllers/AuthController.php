<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('JwtAuth', ['only' => ['refresh']]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials, ['ttl' => (60*60)*24]);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = JWTAuth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);

    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'confirm_password' =>'required|same:password'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->save();
        event(new Registered($user));

        $user->sendConfirmationEmail();

        return response()->json([
            'message' => 'User registered successfully. Please check your email for confirmation.'
        ], 201);
        
        
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'message' => 'User logged out successfully.'
        ], 200);
    }
    public function forgot(Request $request){
        $exist = $request->validate([
            'email' => 'required|email|exists:users'
        ]);
        if($exist){
            $user = User::where('email', $request->email)->first();
            $this->checkIfEmailVerified($user);
            $token = Str::random(64);
            $insert = DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            if($insert){
                Mail::send('email.reset', ['token'=> $token], function($message) use($request){
                    $message->to($request->email);
                    $message->subject('Reset your password');
                });

                return response()->json([
                    'success' => 'we have emailed you with reset password link'
                ]);
            }
        }else{
            return response()->json([
                'Error' => 'Your email does not exist'
            ]);
        }
    }
    public function reset($token, Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        $validateToken = DB::table('password_resets')->where([
            'token' => $token,
            'email' => $request->email
        ])->first();

        if(!$validateToken){
            return response()->json([
                'Error' => 'Invalid Token'
            ]);
        }
        $created_at = Carbon::parse($validateToken->created_at);
        $now = Carbon::now();  
        if($created_at->diffInMinutes($now)> 10){
            return response()->json([
                'Error' => 'Token expired'
            ]);
        }
       
        $user = User::where('email', $request->email)
        ->update(['password' => Hash::make($request->password)]);
        if($user){
            DB::table('password_resets')->where(['email'=> $request->email])->delete();
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'Success' => 'password updated successfully'
            ]);
        }
    }
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->id);
        if ($user->email_verified_at) {
            return Response()->json([
                'message'=> 'confirmed successfully'
             ]) ;
        }
        $user->email_verified_at = Carbon::now();
        $user->save();
        return Response()->json([
            'message'=> 'confirmed successfully'
         ]) ;

    }
    public function refresh()
    {   
        return response()->json([
            'status' => 'success',
            'user' => JWTAuth::user(),
            'authorisation' => [
                'token' => JWTAuth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
    public function checkIfEmailVerified($user){
        if($user->email_verified_at == NULL){
            $user->sendConfirmationEmail();
            return response()->json([
                'Error' => 'Go verify your email first, we emailed you with confirmation link'
            ]);
        }
    }


}