<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chatinput;
use App\Models\Chatoutput;
use App\Models\User;
use Auth;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use App\Controllers\ChatInputController;


class ChatInputController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $sentences = Chatinput::getAllOrderByUpdated_at();
        // return view('chatinput.store', compact('tweets');)
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
        
        
        $result_input= Chatinput::create([
            'sentence' => $request->sentence,
            'user_id' => Auth::user()->id,
        ]);

        $input_id=Chatinput::getAllOrderByUpdated_at(Auth::user()->id)->first()->id;
        $pythonPath =  "../app/Python/";
        $command = "python3 ".$pythonPath."test.py 2>error.log {$result_input->sentence}";
        // コマンドを実行
        exec($command, $outputs, $return);

        $keigo_lis=[];
        $outputs_keigo=explode("'",$outputs[1]);
        $outputs_keigo_count=count($outputs_keigo);
        for($count=0;$count<$outputs_keigo_count;$count++){
            if($count%2==1){
                $keigo_lis[]=$outputs_keigo[$count];
            }
        }
        $keigo_lis=array_unique($keigo_lis);
        #配列でpythonから渡される場合pythonで使われていた[]や""も文字列に含まれるのでそれを削除する。
        #また重複している敬語を削除したものがkeigo_lisに格納されている。

        $result_output= Chatoutput::create([
            'input_id' => $input_id,
            'user_id' => Auth::user()->id,
            "score" => (float) $outputs[0],
        ]);

        return view('chatoutput.show', compact('result_input', 'result_output'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
