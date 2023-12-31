<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Solve extends Controller
{
    public function solve(Request $req, $usersl, $probslno){
        $tokenz = $req->bearerToken();
        if(DB::table('users')->where('slno',$usersl)->count()>0){
            if(DB::table('tokendb')->where('token',$tokenz)->count()>0){
                $user_mail = DB::table('users')->select('email')->where('slno',$usersl)->first();
                $token_mail = DB::table('tokendb')->select('user_email')->where('token',$tokenz)->first();

                if($user_mail->email == $token_mail->user_email){

                    $probslnum = intval($probslno);// turning string post value to int
                    if(DB::table('posts')->where('slno',$probslnum)->count()>0){
                        $posts = DB::table('posts')->select('*')->where('slno',$probslnum)->first();// post serial no will be at button link
                        if ($posts) {
                            $user = DB::table('users')->select('email','imglink')->where('slno', $posts->user_slno)->first();
                            if ($user) {
                                $posts->author = $user->email;
                                $posts->image = $user->imglink;
                            }
                        }
                        if(DB::table('comments')->where('post_slno',$posts->slno)->count()>0){

                            $comments = DB::table('comments')->select('*')->where('post_slno',$posts->slno)->orderBy('slno','desc')->get();

                            if($comments->count()==1){
                                $comments = DB::table('comments')->select('*')->where('post_slno',$posts->slno)->first();
                                $users = DB::table('users')->select('email','imglink')->where('slno',$comments->comment_user_slno)->first();
                                $comments->author = $users->email;
                                $comments->image = $users->imglink;
                            }else{
                                foreach($comments as $comnt){
                                    $users = DB::table('users')->select('email','imglink')->where('slno',$comnt->comment_user_slno)->first();
    
                                    $comnt->author = $users->email;
                                    $comnt->image = $users->imglink;
                                }
                            }
                            
                        }else{
                            $comments = [];
                        }


                        return response()->json([
                            'message'=>'Successful',
                            'postDetail'=> $posts,
                            //add comment if exist
                            'comments' => $comments
                        ],200);

                    }else{
                        return response()->json([
                            'message' => 'Post Do Not Exist.'
                        ],200);
                    }
                    

                }else{
                    return response()->json([
                        'message' => 'Token Is Invalid According To Serial No.'
                    ],200);
                }
            }else{
                return response()->json([
                    'message' => 'User Not Logged In.'
                ],200);
            }
        }else{
            return response()->json([
                'message' => 'Invalid User Serial No.'
            ],200);
        }
    }
}
