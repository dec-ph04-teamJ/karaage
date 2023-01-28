<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Auth;
use App\Models\User;
use App\Models\Chatoutput;

class ChatOutputController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group_id=1;
        $group=Group::query()
        ->find($group_id);
        //$group_idはinputから送られてくる情報
        //findの中にはgroupidが入る。

        $group_users=$group
        ->Get_Group_Users()
        ->get();
        #chatoutputモデルに関数を作った。
        #特定のgroupidを持つuserを全件取得する。

        $users_score_lis=array();
        foreach($group_users as $group_user){
            $user_outputs=$group_user
            ->Get_Chat_Score_from_Userid()
            ->get();
            #ここで特定のuserのoutput情報を取得。一人のユーザーに対して複数のoutputが考えられる。
            $user_score_lis=array();
            foreach($user_outputs as $user_output){
                $user_score_lis[]=$user_output->score;
            }
            if(count($user_score_lis)!=0){
                $user_score_mean=array_sum($user_score_lis)/count($user_score_lis);
                $users_score_lis[]=$user_score_mean;
            }
            else{
                $users_score_lis[]=0;
            }
        }
#user_score_lisにはgroupにいる人の平均点が入っている。
        return view("chatoutput.index",compact("group","group_users","users_score_lis"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_inputs=User::query()
        ->find($id)
        ->Get_User_Contents()
        ->orderBy('created_at','desc')
        ->get();
        #同じチームの他の人のメッセージも見ることができる。
        #ログインuserが入力しているinput情報を取得

        foreach($user_inputs as $user_input){
            $user_outputs_id[]=$user_input->id;
        }
        #71行目のgetで取得した値はforeachによってループして取得する。
        #input情報のidを取得。$user_outputs_idという配列に保存

        $user_outputs=Chatoutput::Get_Chat_Score_from_Inputid($user_outputs_id);
        $count_data=count($user_outputs);
        #Get_Chat_Scoreの関数は配列をわたす
        #配列の長さを取得.show.bladeのfor文で使う
        return view("chatoutput.show",compact("user_outputs","user_inputs","count_data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
