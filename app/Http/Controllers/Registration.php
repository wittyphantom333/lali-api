<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class Registration extends Controller
{
    public function registration(Request $req){

        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'pass' => 'required|regex:/^([a-zA-Z0-9*!@]+){6,50}$/',
            'cpass' => 'required|same:pass',
            'countrys' => 'required|regex:/^([a-zA-Z]+)$/',
            'ages' => 'required|regex:/^([1-9]+){2,3}$/',
            'genders' => 'required|regex:/^([a-zA-Z]+)$/'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }else{
            $mail = $req->input('email');
            $pass = $req->input('pass');
            $countryz = $req->input('countrys');
            $agez = $req->input('ages');
            $genderz = $req->input('genders');

            $acc_exist = DB::table('users')->where('email',[$mail])->count()>0;
            //if account exist
            if($acc_exist){
                return response()->json([
                    'message' => 'Sorry Email Already Exist ... You Can Not Use Same Email Twice.',
                ],200);
            }else{

                //make hash pass
                $pass2 = Hash::make($pass);
                DB::table('users')->insert([
                    'email'=> $mail,
                    'pass'=> $pass2,
                    'country' => $countryz,
                    'age'=> $agez,
                    'gender'=> $genderz,
                    'imglink'=> 'localdisk/something'

        
                ]);//enter inside DB
                //successful
                return response()->json([
                    'message' => 'Registration Successful Please Login With Your Credential For First Time.',
                ],200);
    

            }



        }


        



            

            

            
 
            
        


        

    }
}