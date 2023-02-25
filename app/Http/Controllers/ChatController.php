<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Chatinput;
use App\Models\Chatoutput;
use App\Models\User;
use Auth;
use App\Models\Keigo;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inputs= DB::table('chatinputs')->get();
        return view("chat.input",compact("inputs"));
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
    $validator = \Validator::make($request->all(), [
    'sentence' => 'required'
  ]);
  // バリデーション:エラー
  if ($validator->fails()) {
    return redirect()
      ->back();
  }

        $result_input= Chatinput::create([
            'sentence' => $request->sentence,
            'user_id' => Auth::user()->id,
        ]);
        $input_id=Chatinput::getAllOrderByUpdated_at(Auth::user()->id)->first()->id;
        #inputテーブルに保存。今入力した人のinput_idを取得

        $pythonPath =  "../app/Python/";
        $command = "python3 ".$pythonPath."test.py 2>error.log {$result_input->sentence}";
        exec($command, $outputs, $return);

        #コマンドを実行
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
            'kanji_rate' => (float) $outputs[2],
            'emoji_rate' => (float) $outputs[4],
        ]);
        #outputテーブルに保存。今入力した人のoutput_idを取得


        $output_id=Chatoutput::getAllOrderByUpdated_at(Auth::user()->id)->first()->id;
        foreach($keigo_lis as $keigo){
            $result_keigo=Keigo::create([
                "output_id"=>$output_id,
                "keigo"=>$keigo,
            ]);
        }
        #敬語テーブルに保存。一文に対して複数ある可能性があるのでfor文で回す。
        $inputs= DB::table('chatinputs')->get();
        return view("chat.input",compact("inputs"));
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

    public function change_girl_words(Request $request){
    $validator = \Validator::make($request->all(), [
    'sentence' => 'required'
  ]);
  // バリデーション:エラー
  if ($validator->fails()) {
    return redirect()
      ->back();
  }
        $girl_words_lis=array("思います"=>"思うよ🤔",
                            "承知しました"=>"OK!!😆",
                            "拝見します"=>"見るね！🤗",
                            "拝見いたします"=>"見るね！🤗",
                            "拝見しました"=>"見たよ!😚",
                            "拝見いたしました"=>"見たよ！🤗",
                            "頂きました"=>"もらったよ!🥰",
                            "頂きます"=>"もらうね!😃",
                            "ですよね"=>"だよね😆～",
                            "お願いいたします"=>"よろしく~😌",
                            "申し訳ありません"=>"すまん😰",
                            "失礼しました"=>"ごめんね😔",
                            "ございます"=>"す"
                            );

        $word=$request->sentence;
        $girl_word=$request->sentence;
        foreach($girl_words_lis as $key=>$value){
            $girl_word=str_replace($key,$value, $girl_word);
        }
        if($word==$girl_word){
            \Session::flash('girl_flash_message', 'ギャル語に変換できません');
        }
        #ギャル語に直すところが長った場合メッセージを出力する
        return redirect(route("chat.index"))->with([
            "girl_word"=>$girl_word,
            "word"=>$word
        ]);
    }

}
