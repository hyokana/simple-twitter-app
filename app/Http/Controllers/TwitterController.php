<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests;

use App\Twitter;

use DB;

class TwitterController extends Controller {

	function list(Request $req){
		$collectedfriends = $this->getFriends();
		array_push($collectedfriends, Auth::id());
		$totalrows = DB::select('select count(*) totalrows from twitters where user_id in (\'' . implode("', '", $collectedfriends) . '\')')[0]->totalrows;
		$list = DB::select('select twitters.*, users.image, users.name from twitters join users on (users.id = twitters.user_id) where user_id in (\'' . implode("', '", $collectedfriends) . '\') order by created_at desc limit ' . $req->get('limit') . ' offset ' . $req->get('offset'));
		return response()->json(['totalrows' => $totalrows, 'list' => $list]);
	}

	function post(Request $req){
		try{
			if(!$req->get('post'))
				throw new Exception("Status harus diisi.....");

			Twitter::create([
				'user_id' => Auth::id(),
				'twitter' => $req->get('post')
			]);

			return response()->json(['msg' => 'Update status berhasil.']);
		}catch(Exception $e){
			return response()->json(['err' => $e->getMessage()]);
		}
	}

	function getFriends(){
		$friends = DB::select('select * from friendships where user1_id = \'' . Auth::id() . '\' or user2_id = \'' . Auth::id() . '\'');
		$collectedfriends = [];
		foreach ($friends as $val) {
			$push = ($val->user1_id == Auth::id()) ? $val->user2_id : $val->user1_id;
			array_push($collectedfriends, $push);
		}
		return $collectedfriends;
	}

}
