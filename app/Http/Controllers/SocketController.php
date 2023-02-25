<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\User;
use Auth;
class SocketController extends Controller
{
     protected $clients;
}
