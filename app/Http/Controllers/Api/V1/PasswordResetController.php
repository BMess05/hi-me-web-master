<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\model\DeviceData;
use App\model\PasswordReset;
use Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function passwordreset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $ret = array('success' => '0', 'message' => $validator->messages()->first());
            return json_encode($ret);
        }
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return json_encode(array('success' => '0', 'message' =>  "We can't find a user with that e-mail address."));
        }

        //$token = str_random(64);
        $token  = Str::random(64);

        $passwordData = PasswordReset::where('email', $user->email)->first();

        if ($passwordData) {
            $passwordReset = PasswordReset::where(['email' => $user->email])->update(array('token' => $token));
        } else {
            $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => $token
                ]
            );
        }

        $resetLink = \url('password/reset/') . '/' . $token . '?email=' . urlencode($user->email);
        //$resetLink = 'http://'.$request->getHttpHost().'/public/index.php/password/reset' . '/' . $token . '?email=' . urlencode($user->email);
        $body = 'Click here to reset your password:' . $resetLink;

        // Mail::raw($body, function ($m) use ($user) {
        //     $m->from('testsoftradix@gmail.com', 'cstop');
        //     $m->to($user->email, $user->name)->subject('Your Password Reset Link');
        // });

        Mail::send('emails.contact',
            ['link' => $resetLink, 'name' => $user->name],
            function ($m) use ($user) {
                $m->from(env('MAIL_FROM_ADDRESS'), 'Hi-Me');
                $m->to($user->email)->subject('Forgot Password');
            }
        );

        if (Mail::failures()) {
            return response()->Fail('Sorry! Please try again latter');
        } else {
            $ret = array('success' => '1', 'message' => 'Email has been sent on you email id.');
        }
        return json_encode($ret);
    }
}
