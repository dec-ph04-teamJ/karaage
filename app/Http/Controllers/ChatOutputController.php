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
        //これはinputから送られてくる情報
        $group=Group::query()
        ->find($group_id);
        //このfindの中にはgroupidが入る
        $group_users=$group
        ->Get_Group_Users()
        ->get();
        //特定のgroupidを持つuserを全件取得する。
        return view("chatoutput.index",compact("group","group_users"));
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
        #同じチームの他の人のメッセージも見ることができる。
        ->Get_User_Contents()
        ->orderBy('created_at','desc')
        ->get();
        #userが入力しているinput情報を取得

        foreach($user_inputs as $user_input){
            $user_outputs_id[]=$user_input->id;
            #input情報のidを取得
        }
        $user_outputs=Chatoutput::Get_Chat_Score($user_outputs_id);
        $count_data=count($user_outputs);
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
