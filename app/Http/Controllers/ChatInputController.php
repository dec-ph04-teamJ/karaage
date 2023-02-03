<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chatinput;
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
        //
        
        $result = Chatinput::create([
            'sentence' => $request->sentence,
            'user_id' => Auth::user()->id,
        ]);
        
        // $sentence = $request->get('sentence');

        // ddd($sentence);
        return view('chatinput.store', compact('result'));
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
