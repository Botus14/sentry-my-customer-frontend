<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
}

public function resetPassword(Request $request) {
    try {
        $valid = validator($request->only('old_password', 'new_password', 'confirm_password'), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|different:old_password',
            'confirm_password' => 'required_with:new_password|same:new_password|string|min:6',
                ], [
            'confirm_password.required_with' => 'Confirm password is required.'
        ]);

        if ($valid->fails()) {
            return response()->json([
                        'errors' => $valid->errors(),
                        'message' => 'Faild to update password.',
                        'status' => false
                            ], 200);
        }
//            Hash::check("param1", "param2")
//            param1 - user password that has been entered
//            param2 - old password hash stored
        if (Hash::check($request->get('old_password'), Auth::user()->password)) {
            $user = User::find(Auth::user()->id);
            $user->password = (new BcryptHasher)->make($request->get('new_password'));
            if ($user->save()) {
                return response()->json([
                            'data' => [],
                            'message' => 'Your password has been updated',
                            'status' => true
                                ], 200);
            }
        } else {
            return response()->json([
                        'errors' => [],
                        'message' => 'Wrong password entered.',
                        'status' => false
                            ], 200);
        }
    } catch (Exception $e) {
        return response()->json([
                    'errors' => $e->getMessage(),
                    'message' => 'Please try again',
                    'status' => false
                        ], 200);
    }
}