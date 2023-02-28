<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use Ratchet\MessageComponentInterface;

use Ratchet\ConnectionInterface;

use App\Models\User;

use App\Models\Post;

use App\Models\Chat_request;

use App\Models\Chatinput;

use App\Models\Chatoutput;

use App\Models\Keigo;

use Auth;
class SocketController extends Controller implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        Log::error('on open.');
        $querystring = $conn->httpRequest->getUri()->getQuery();

        parse_str($querystring, $queryarray);

        if(isset($queryarray['token']))
        {
            User::where('token', $queryarray['token'])->update([ 'connection_id' => $conn->resourceId, 'user_status' => 'Online' ]);

            $user_id = User::select('id')->where('token', $queryarray['token'])->get();

            $data['id'] = $user_id[0]->id;

            $data['status'] = 'Online';

            foreach($this->clients as $client)
            {
                if($client->resourceId != $conn->resourceId)
                {
                    $client->send(json_encode($data));
                }
            }

        }


    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {

        Log::error("msg ");
        Log::error("msg -> {$msg}");
        Log::error('on message.');
        if(preg_match('~[^\x20-\x7E\t\r\n]~', $msg) > 0)
        {
            //receiver image in binary string message

            $image_name = time() . '.jpg';

            file_put_contents(public_path('images/') . $image_name, $msg);

            $send_data['image_link'] = $image_name;

            foreach($this->clients as $client)
            {
                if($client->resourceId == $conn->resourceId)
                {
                    $client->send(json_encode($send_data));
                }
            }
        }


        $data = json_decode($msg);
        if(isset($data->type))
        {
            if($data->type == 'request_load_unconnected_user')
            {
                $user_data = User::select('id', 'name', 'user_status', 'user_image')
                                    ->where('id', '!=', $data->from_user_id)
                                    ->orderBy('name', 'ASC')
                                    ->get();

                $sub_data = array();

                foreach($user_data as $row)
                {
                    $sub_data[] = array(
                        'name'      =>  $row['name'],
                        'id'        =>   $row['id'],
                        'status'    =>  $row['user_status'],
                        'user_image'=>  $row['user_image']
                    );
                }

                $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();

                $send_data['data'] = $sub_data;

                $send_data['response_load_unconnected_user'] = true;

                foreach($this->clients as $client)
                {
                    if($client->resourceId == $sender_connection_id[0]->connection_id)
                    {
                        $client->send(json_encode($send_data));
                    }
                }
            }

            if($data->type == 'request_search_user')
            {
                $user_data = User::select('id', 'name', 'user_status', 'user_image')
                                    ->where('id', '!=', $data->from_user_id)
                                    ->where('name', 'like', '%'.$data->search_query.'%')
                                    ->orderBy('name', 'ASC')
                                    ->get();

                $sub_data = array();

                foreach($user_data as $row)
                {

                    $chat_request = Chat_request::select('id')
                                    ->where(function($query) use ($data, $row){
                                        $query->where('from_user_id', $data->from_user_id)->where('to_user_id', $row->id);
                                    })
                                    ->orWhere(function($query) use ($data, $row){
                                        $query->where('from_user_id', $row->id)->where('to_user_id', $data->from_user_id);
                                    })->get();

                    /*
                    SELECT id FROM chat_request 
                    WHERE (from_user_id = $data->from_user_id AND to_user_id = $row->id) 
                    OR (from_user_id = $row->id AND to_user_id = $data->from_user_id)
                    */

                    if($chat_request->count() == 0)
                    {
                        $sub_data[] = array(
                            'name'  =>  $row['name'],
                            'id'    =>  $row['id'],
                            'status'=>  $row['user_status'],
                            'user_image' => $row['user_image']
                        );
                    }

                    
                }

                $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();

                $send_data['data'] = $sub_data;

                $send_data['response_search_user'] = true;

                foreach($this->clients as $client)
                {
                    if($client->resourceId == $sender_connection_id[0]->connection_id)
                    {
                        $client->send(json_encode($send_data));
                    }
                }
            }

            if($data->type == 'request_chat_user')
            {
                $chat_request = new Chat_request;

                $chat_request->from_user_id = $data->from_user_id;

                $chat_request->to_user_id = $data->to_user_id;

                $chat_request->status = 'Pending';

                $chat_request->save();

                $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();

                $receiver_connection_id = User::select('connection_id')->where('id', $data->to_user_id)->get();

                foreach($this->clients as $client)
                {
                    if($client->resourceId == $sender_connection_id[0]->connection_id)
                    {
                        $send_data['response_from_user_chat_request'] = true;

                        $client->send(json_encode($send_data));
                    }

                    if($client->resourceId == $receiver_connection_id[0]->connection_id)
                    {
                        $send_data['user_id'] = $data->to_user_id;

                        $send_data['response_to_user_chat_request'] = true;

                        $client->send(json_encode($send_data));
                    }
                }
            }

            if($data->type == 'request_load_unread_notification')
            {
                $notification_data = Chat_request::select('id', 'from_user_id', 'to_user_id', 'status')
                                        ->where('status', '!=', 'Approve')
                                        ->where(function($query) use ($data){
                                            $query->where('from_user_id', $data->user_id)->orWhere('to_user_id', $data->user_id);
                                        })->orderBy('id', 'ASC')->get();

                /*
                SELECT id, from_user_id, to_user_id, status FROM chat_requests
                WHERE status != 'Approve'
                AND (from_user_id = $data->user_id OR to_user_id = $data->user_id)
                ORDER BY id ASC
                */

                $sub_data = array();

                foreach($notification_data as $row)
                {
                    $user_id = '';

                    $notification_type = '';

                    if($row->from_user_id == $data->user_id)
                    {
                        $user_id = $row->to_user_id;

                        $notification_type = 'Send Request';
                    }
                    else
                    {
                        $user_id = $row->from_user_id;

                        $notification_type = 'Receive Request';
                    }

                    $user_data = User::select('name', 'user_image')->where('id', $user_id)->first();

                    $sub_data[] = array(
                        'id'            =>  $row->id,
                        'from_user_id'  =>  $row->from_user_id,
                        'to_user_id'    =>  $row->to_user_id,
                        'name'          =>  $user_data->name,
                        'notification_type' =>  $notification_type,
                        'status'           =>   $row->status,
                        'user_image'    =>  $user_data->user_image
                    );
                }

                $sender_connection_id = User::select('connection_id')->where('id', $data->user_id)->get();

                foreach($this->clients as $client)
                {
                    if($client->resourceId == $sender_connection_id[0]->connection_id)
                    {
                        $send_data['response_load_notification'] = true;

                        $send_data['data'] = $sub_data;

                        $client->send(json_encode($send_data));
                    }
                }
            }

            if($data->type == 'request_process_chat_request')
            {
                Chat_request::where('id', $data->chat_request_id)->update(['status' => $data->action]);

                $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();

                $receiver_connection_id = User::select('connection_id')->where('id', $data->to_user_id)->get();

                foreach($this->clients as $client)
                {
                    $send_data['response_process_chat_request'] = true;

                    if($client->resourceId == $sender_connection_id[0]->connection_id)
                    {
                        $send_data['user_id'] = $data->from_user_id;
                    }

                    if($client->resourceId == $receiver_connection_id[0]->connection_id)
                    {
                        $send_data['user_id'] = $data->to_user_id;
                    }

                    $client->send(json_encode($send_data));
                }
            }

            if($data->type == 'request_connected_chat_user')
            {
                $condition_1 = ['from_user_id' => $data->from_user_id, 'to_user_id' => $data->from_user_id];

                $user_id_data = Chat_request::select('from_user_id', 'to_user_id')
                                            ->orWhere($condition_1)
                                            ->where('status', 'Approve')
                                            ->get();

                /*
                SELECT from_user id, to_user_id FROM chat_requests 
                WHERE (from_user_id = $data->from_user_id OR to_user_id = $data->from_user_id) 
                AND status = 'Approve'
                */

                $sub_data = array();

                foreach($user_id_data as $user_id_row)
                {
                    $user_id = '';

                    if($user_id_row->from_user_id != $data->from_user_id)
                    {
                        $user_id = $user_id_row->from_user_id;
                    }
                    else
                    {
                        $user_id = $user_id_row->to_user_id;
                    }

                    $user_data = User::select('id', 'name', 'user_image', 'user_status', 'updated_at')->where('id', $user_id)->first();

                    if(date('Y-m-d') == date('Y-m-d', strtotime($user_data->updated_at)))
                    {
                        $last_seen = 'Last Seen At ' . date('H:i', strtotime($user_data->updated_at));
                    }
                    else
                    {
                        $last_seen = 'Last Seen At ' . date('d/m/Y H:i', strtotime($user_data->updated_at));
                    }

                    $sub_data[] = array(
                        'id'    =>  $user_data->id,
                        'name'  =>  $user_data->name,
                        'user_image'    =>  $user_data->user_image,
                        'user_status'   =>  $user_data->user_status,
                        'last_seen'     =>  $last_seen
                    );


                }

                $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();

                foreach($this->clients as $client)
                {
                    if($client->resourceId == $sender_connection_id[0]->connection_id)
                    {
                        $send_data['response_connected_chat_user'] = true;

                        $send_data['data'] = $sub_data;

                        $client->send(json_encode($send_data));
                    }
                }
            }

            if($data->type == 'request_send_message')
            {
                //save chat message in mysql


                $chat = new Post;

                $chat->from_user_id = $data->from_user_id;

                $chat->to_user_id = $data->to_user_id;

                $chat->post_message = $data->message;

                $chat->message_status = 'Not Send';



///----------------------------------------



                $result_input=new Chatinput;
                $result_input->sentence=$data->message;
                $result_input->user_id=$data->from_user_id;
                $result_input->save();
                //$send_data["sentence"]=$data->message;
                //$send_data["user_id"]=$data->from_user_id;



                $input_id=Chatinput::getAllOrderByUpdated_at($data->from_user_id)->first()->id;
                #inputテーブルに保存。今入力した人のinput_idを取得


                $pythonPath =  "./app/python/";
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


                $result_output=new Chatoutput;
                $result_output->input_id=$input_id;
                $result_output->user_id=$data->from_user_id;
                $result_output->score=(float) $outputs[0];
                $result_output->kanji_rate=(float) $outputs[2];
                $result_output->emoji_rate=(float) $outputs[4];
                $result_output->save();
                               
                //$send_data[""]=$data->message;
                //$send_data["user_id"]=$data->from_user_id;


                Log::error('save Chatinput.6');

                $output_id=Chatoutput::getAllOrderByUpdated_at($data->from_user_id)->first()->id;
                foreach($keigo_lis as $keigo){
                    $result_keigo=new Keigo;
                    $result_keigo->output_id=$output_id;
                    $result_keigo->keigo=$keigo;
                    $result_keigo->save();
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
                            "お願致します"=>"よろしく~😌",
                            "申し訳ありません"=>"すまん😰",
                            "失礼しました"=>"ごめんね😔",
                            "ございます"=>"す",
                            "参上します"=>"行くよん!😆",
                            "お伺いします"=>"行くよん!😆",
                            "おうかがいします"=>"行くよん!😆",
                            "お待ちしております"=>"待ってるね!😆",
                            "またのお越しを"=>"また来るの",
                            "失礼いたしました"=>"ごめんね😔",
                            "失礼致しました"=>"ごめんね😔",
                            "失礼致します"=>"ごめんね😔",
                            "失礼いたします"=>"ごめんね😔",
                            "申し訳ございません"=>"すまん😰",
                            "お待ちしております"=>"待ってるね!😆",
                            "かしこまりました"=>"分かった!😆",
                            "なぜですか"=>"なんで?🤔",
                            "どうしてでしょうか"=>"なんで?🤔",
                            "存じ上げております"=>"知ってるよ!😆",
                            "存じております"=>"知ってるよ!😆",
                            "ご無沙汰しております"=>"久しぶり~!!😆",
                            "ご教示ください"=>"教えて！😃",
                            "ご教授ください"=>"教えて！😃",
                            "ご教授下さい"=>"教えて！😃",
                            "ご教示下さい"=>"教えて！😃",
                            "。"=>"!!!!!!"
                            );

        $girl_word=$data->message;
        foreach($girl_words_lis as $key=>$value){
            $girl_word=str_replace($key,$value, $girl_word);
        }
        Log::error('save Chatinput.7');
                    ///-----------------------------------------



                
                // TODO 1 敬語バリデーションを行って、画面に戻したいメッセージを返す
                
                if(false){
                    $send_data['user_id'] = $data->to_user_id;

                    $send_data['warning'] = "点数が低すぎます";

                    $send_data['response_to_user_keigo_warinng'] = true;
    
                    $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();
   
                    foreach($this->clients as $client)
                    {
                        if($client->resourceId == $sender_connection_id[0]->connection_id )
                        {
                            $client->send(json_encode($send_data));
                        }
                    }
                }elseif($result_output->score<42){
                    
                    // TODO 2 文章をギャルっぽく変換

                    $chat->save();


                    $post_message_id = $chat->id;

                    $receiver_connection_id = User::select('connection_id')->where('id', $data->to_user_id)->get();
    
                    $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();

                    Post::where('id', $post_message_id)->update(['post_message' =>$girl_word]);
    
                    foreach($this->clients as $client)
                    {
                        if($client->resourceId == $receiver_connection_id[0]->connection_id || $client->resourceId == $sender_connection_id[0]->connection_id)
                        {
                            $send_data['post_message_id'] = $post_message_id;
                            
                            $send_data['message'] = $girl_word;
    
                            $send_data['from_user_id'] = $data->from_user_id;
    
                            $send_data['to_user_id'] = $data->to_user_id;

                            $send_data['warning'] = "文章堅いんじゃない??ギャル語にするよ~😆";

                            $send_data['response_to_user_keigo_warinng'] = true;
    

    
                            if($client->resourceId == $receiver_connection_id[0]->connection_id)
                            {
                                Post::where('id', $post_message_id)->update(['message_status' =>'Send']);
    
                                $send_data['message_status'] = 'Send';
                            }else
                            {
                                $send_data['message_status'] = 'Not Send';
                            }
    
                            $client->send(json_encode($send_data));
                        }
                    }
                }

            }

            if($data->type == 'request_chat_history')
            {
                $chat_data = Post::select('id', 'from_user_id', 'to_user_id', 'post_message', 'message_status')
                                    ->where(function($query) use ($data){
                                        $query->where('from_user_id', $data->from_user_id)->where('to_user_id', $data->to_user_id);
                                    })
                                    ->orWhere(function($query) use ($data){
                                        $query->where('from_user_id', $data->to_user_id)->where('to_user_id', $data->from_user_id);
                                    })->orderBy('id', 'ASC')->get();
                /*
                SELECT id, from_user_id, to_user_id, post_message, message status 
                FROM chats 
                WHERE (from_user_id = $data->from_user_id AND to_user_id = $data->to_user_id) 
                OR (from_user_id = $data->to_user_id AND to_user_id = $data->from_user_id)
                ORDER BY id ASC
                */

                Log::error("history: {$chat_data}");

                $send_data['chat_history'] = $chat_data;

                $receiver_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();

                foreach($this->clients as $client)
                {
                    if($client->resourceId == $receiver_connection_id[0]->connection_id)
                    {
                        $client->send(json_encode($send_data));
                    }
                }

            }

            if($data->type == 'update_chat_status')
            {
                //update chat status

                Post::where('id', $data->post_message_id)->update(['message_status' => $data->post_message_status]);

                $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();

                foreach($this->clients as $client)
                {
                    if($client->resourceId == $sender_connection_id[0]->connection_id)
                    {
                        $send_data['update_message_status'] = $data->post_message_status;

                        $send_data['post_message_id'] = $data->post_message_id;

                        $client->send(json_encode($send_data));
                    }
                }
            }

            if($data->type == 'check_unread_message')
            {
                $chat_data = Post::select('id', 'from_user_id', 'to_user_id')->where('message_status', '!=', 'Read')->where('from_user_id', $data->to_user_id)->get();

                /*
                SELECT id, from_user_id, to_user_id FROM chats 
                WHERE message_status != 'Read'
                AND from_user_id = $data->to_user_id
                */

                $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get(); //send number of unread message

                $receiver_connection_id = User::select('connection_id')->where('id', $data->to_user_id)->get(); //send message read status

                foreach($chat_data as $row)
                {
                    Post::where('id', $row->id)->update(['message_status' => 'Send']);

                    foreach($this->clients as $client)
                    {
                        if($client->resourceId == $sender_connection_id[0]->connection_id)
                        {
                            $send_data['count_unread_message'] = 1;

                            $send_data['post_message_id'] = $row->id;

                            $send_data['from_user_id'] = $row->from_user_id;
                        }

                        if($client->resourceId == $receiver_connection_id[0]->connection_id)
                        {
                            $send_data['update_message_status'] = 'Send';

                            $send_data['post_message_id'] = $row->id;

                            $send_data['unread_msg'] = 1;

                            $send_data['from_user_id'] = $row->from_user_id;
                        }

                        $client->send(json_encode($send_data));
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        Log::error('on close.');
        $this->clients->detach($conn);

        $querystring = $conn->httpRequest->getUri()->getQuery();

        parse_str($querystring, $queryarray);

        if(isset($queryarray['token']))
        {
            User::where('token', $queryarray['token'])->update([ 'connection_id' => 0, 'user_status' => 'Offline' ]);

            $user_id = User::select('id', 'updated_at')->where('token', $queryarray['token'])->get();

            $data['id'] = $user_id[0]->id;

            $data['status'] = 'Offline';

            $updated_at = $user_id[0]->updated_at;

            if(date('Y-m-d') == date('Y-m-d', strtotime($updated_at))) //Same Date, so display only Time
            {
                $data['last_seen'] = 'Last Seen at ' . date('H:i');
            }
            else
            {
                $data['last_seen'] = 'Last Seen at ' . date('d/m/Y H:i');
            }

            foreach($this->clients as $client)
            {
                if($client->resourceId != $conn->resourceId)
                {
                    $client->send(json_encode($data));
                }
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Log::error("An error has occurred: {$e->getMessage()} \n");
        $conn->close();
    }
}
