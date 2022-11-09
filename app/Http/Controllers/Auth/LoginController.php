<?php

namespace App\Http\Controllers\Auth;
use Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Admin;
use URL;
use DB; 
use Carbon\Carbon; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function getadminsignIn(Request $request)
    {
        return view('auth.adminlogin');
    }




    public function adminsignIn(Request $request)
    {
        // dd(url()->current());
        // dd($request);
        $this->validate($request, [
            'email'   => 'required|email',
            'password'  => 'required|min:3'
           ]);

        $details = [ 'email' => $request->email , 'password' => $request->password ];
           if (Auth::guard('admin')->attempt($details)) {

            // $user = Admin::find(1);
            // Auth::setUser($user);
            if(auth()->guard('admin')->user()->is_super_admin == 1){


            }
                      return redirect()->route('index');
        }
           else{
                return redirect()->back()->withInput()->withErrors(['password' => 'password does not exist',
                                                                'email' => 'email does not exist']);
           }
    }
    public function adminsignOut(Request $request) {
        Auth::guard('admin')->logout();


        return redirect('/adminSignIn');
    }


    public function admin_forgot_password()
    {

  return view('auth.admin_forgot_password');
    }

    public function admin_rest_password(Request $request)
{


    $admin = Admin::where('email', $request->email)->first();

    if(!$admin){
 return redirect()->back()->withInput()->withErrors(['email' => 'email does not exist']);
    }
    else{


        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
          ]);
          $tokenData = DB::table('password_resets')
          ->where('email', $request->email)->first();
        $link =config('app.Site_URL');
        $data = [
            'email' => $request->email,
            'name' => $admin->name,
            'link' => $link,
            'token' => $tokenData->token,
        ];

//   return view('emails.reserPassword', compact(['data' ]));
   Mail::to($request->email)->send(new ResetPasswordRequest($data));
   return redirect()->back()->with('success', 'Reset password link has been sent to your email');

    }


}

public function resetPassword($token, $email)
{


    return view('auth.admin_reset_password', compact(['email', 'token' ]));


}

public function reset_Password(Request $request)
{

    $request->validate([
              'password' => 'min:6|alphaNum|required_with:password_confirm|same:password_confirm',
        'password_confirm' => 'required'
    ]);

    $updatePassword = DB::table('password_resets')
    ->where([
      'email' => $request->email,
      'token' => $request->pass_token,
    ])->first();

        if(!$updatePassword){
        return redirect()->back()->with('error', 'Invalid token!');

        }

$user = Admin::where('email', $request->email)
->update(['password' => Hash::make($request->password)]);

DB::table('password_resets')->where(['email'=> $request->email])->delete();

return redirect('/adminSignIn')->with('message', 'Your password has been changed!');
}
public function logout(Request $request) {
    Auth::logout();
    return redirect('/admin');
}
}
