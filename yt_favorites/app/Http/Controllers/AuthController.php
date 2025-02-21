<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request){

        $email = $request->input('email');
        $password =$request->input('password');
    
        if(User::where('email', $email)->exists()){
            return response('Email already exists', 400);
        }    

        
        try{
            $user =User::create(['email'=>$email,'password'=>$password]);

            if($user){

                $token= JWTAuth::fromUser($user);

                return response()->json([
                    'message' => 'User created successfully!!',
                    'token' => $token,
                ],201);

            }   

        }catch(Error $e){
            return response()->json(['message'=>$e],400);
        }
    
    }

    public function login(Request $request){
        

        $email = $request->input('email');
        $password = $request->input('password');
        $user = User::where(['email'=>$email])->first();

        if($user){
            if(Hash::check($password,$user->password)){

            $token = JWTAuth::fromUser($user);
            return response()->json(['message'=> 'Logging in!', 'token' => $token],200);

            }
            else{
                return response()->json(['message' => 'Wrong password!'],400);
            }
        }
        else{
            
            return response()->json(['message' => 'Email does not exist in our database!'],400);
        }
    }
    
    public function logout(Request $request){
        
        $token= $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token not provided.'], 400); 
        }
         try {
            JWTAuth::invalidate(JWTAuth::parseToken($token));
            return response()->json(['message' => 'Logged out successfully!'], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Invalid token.'], 400);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to log out.'], 500);
    }
    }


}
