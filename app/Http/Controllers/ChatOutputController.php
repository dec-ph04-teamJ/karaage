<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Auth;
use App\Models\User;
use App\Models\Chatoutput;
use App\Models\Keigo;


class ChatOutputController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user=User::query()
        ->find(Auth::id());
        $user_inputs=$user
        ->Get_Chats()
        ->orderBy('id','asc')
        ->get();
        $user_outputs=$user
        ->Get_Chat_Scores()
        ->orderBy('input_id','asc')
        ->get();
        #inputテーブルのidとoutputテーブルのinput_idが同じになるようにorderbyで並び替える。
        $count_data=count($user_outputs);
        #Get_Chat_Scoreの関数は配列をわたす
        #配列の長さを取得.show.bladeのfor文で使う

        $user_outputs_keigo_lis=[];
        foreach($user_outputs as $user_output){
            $user_outputs_keigo_lis[]=Keigo::Get_Keigo_from_Outputid($user_output->id);
        }
        #userの文章ごとの敬語リストが入っている[[一つ目の文章の敬語リスト],[二つ目],[三つ目]]

        return view("chatoutput.index",compact("count_data","user_inputs","user_outputs","user_outputs_keigo_lis"));
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
