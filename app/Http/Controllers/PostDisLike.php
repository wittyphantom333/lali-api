<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PostDisLike extends Controller
{
    public function postdislike(Request $req,$usersl, $postno){
        $tokenz = $req->bearerToken();
        if(DB::table('users')->where('slno',$usersl)->count()>0){
            if(DB::table('tokendb')->where('token',$tokenz)->count()>0){
                if(DB::table('posts')->where('slno',$postno)->count()>0){
                    $tok_email = DB::table('tokendb')->select('user_email')->where('token',$tokenz)->first();
                    $usrsl_email = DB::table('users')->select('email')->where('slno',$usersl)->first();
                    if($tok_email->user_email == $usrsl_email->email){
                        if(DB::table('post_like')->where('post_slno',$postno)->where('user_email',$usrsl_email->email)->count()>0){
                            DB::table('post_like')->where('post_slno',$postno)->where('user_email',$usrsl_email->email)->delete();
                            DB::table('post_dislike')->insert([
                                'post_slno'=> $postno,
                                'user_email'=> $usrsl_email->email
                            ]);

                            $like = DB::table('posts')->select('dislike_amount')->where('slno',$postno)->first();
                            $like->dislike_amount = $like->dislike_amount+1;
                            DB::table('posts')->where('slno',$postno)->update([
                                'dislike_amount'=>$like->dislike_amount
                            ]);

                            $like = DB::table('posts')->select('like_amount')->where('slno',$postno)->first();
                            $like->like_amount = $like->like_amount-1;
                            DB::table('posts')->where('slno',$postno)->update([
                                'like_amount'=>$like->like_amount
                            ]);

                            $author = DB::table('posts')->select('user_slno')->where('slno',$postno)->first();
                            $commenter = DB::table('users')->select('email')->where('slno',$usersl)->first();
                            if($author->user_slno != intval($usersl )){
                                DB::table('notification')->insert([
                                    'owner_slno' => $author->user_slno,
                                    'commenter_slno' => $usersl,
                                    'commenter_email' => $commenter->email,
                                    'reason' => 'Disliked Your Post.',
                                    'post_slno' => $postno
                                ]);
                            }

                            return response()->json([
                                'message'=>'Success'
                            ],200);

                        }else if(DB::table('post_dislike')->where('post_slno',$postno)->where('user_email',$usrsl_email->email)->count()>0){
                            return response()->json([
                                'message'=>'Failed'
                            ],200);
                        }else{
                            DB::table('post_dislike')->insert([
                                'post_slno'=> $postno,
                                'user_email'=> $usrsl_email->email
                            ]);
                            $like = DB::table('posts')->select('dislike_amount')->where('slno',$postno)->first();
                            $like->dislike_amount = $like->dislike_amount+1;
                            DB::table('posts')->where('slno',$postno)->update([
                                'dislike_amount'=>$like->dislike_amount
                            ]);

                            $author = DB::table('posts')->select('user_slno')->where('slno',$postno)->first();
                            $commenter = DB::table('users')->select('email')->where('slno',$usersl)->first();
                            if($author->user_slno != intval($usersl )){
                                DB::table('notification')->insert([
                                    'owner_slno' => $author->user_slno,
                                    'commenter_slno' => $usersl,
                                    'commenter_email' => $commenter->email,
                                    'reason' => 'Disliked Your Post.',
                                    'post_slno' => $postno
                                ]);
                            }
                            return response()->json([
                                'message'=>'Success'
                            ],200);
                        }
                    }else{
                        return response()->json([
                            'message'=>'Invalid Token According To Serial.'
                        ],200);
                    }
                }else{
                    return response()->json([
                        'message'=>'The Post Might Have Been Removed.'
                    ],200);
                }
            }else{
                return response()->json([
                    'message'=>'You Are Not Logged In.'
                ],200);
            }
        }else{
            return response()->json([
                'message'=>'Invalid Serial Number.'
            ],200);
        }    
    }
}
