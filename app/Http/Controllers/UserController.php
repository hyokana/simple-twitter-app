<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests;

use Exception;
use File;
use DB;

use App\User;

class UserController extends Controller{

	function index(){
		if (!Auth::check()){
			return redirect('login');
		}else{
			return view('user');
		}
	}

	function getuser(){
		return response()->json(User::find(Auth::id()));
	}

	function login(Request $req){
		try{
			$field = ['email' => 'email', 'password' => 'password'];
			foreach ($field as $field => $name) {
				if(!$req->get($field))
					throw new Exception("Field " . $name . " harus diisi.....");
			}

			if (Auth::attempt(['email' => $req->get('email'), 'password' => $req->get('password')])) {
				return response()->json(['redirect' => '/']);
			}else{
				throw new Exception("Username atau password salah...");
			}
		}catch(Exception $e){
			return response()->json(['err' => $e->getMessage()]);
		}
	}

	function update(Request $req){
		try{
			$field = ['name' => 'nama', 'email' => 'email'];
			$user = User::find(Auth::id());
			foreach ($field as $field => $name) {
				if(!$req->get($field))
					throw new Exception("Field " . $name . " harus diisi.....");
				else
					eval("\$user->" . $field . " = \$req->get('$field');");
			}

			if($req->get('password'))
				$user->password = bcrypt($req->get('password'));

			if($user->save()) {
				Auth::login($user);
				return response()->json(['msg' => 'Penyimpanan berhasil...']);
			}else{
				throw new Exception("Gagal melakukan penyimpanan...");
			}
		}catch(Exception $e){
			return response()->json(['err' => $e->getMessage()]);
		}
	}

	function upload(Request $req){
		try{
			if (!$req->hasFile('image'))
				throw new Exception("File not found...");

			$user = User::find(Auth::id());

			if($user->image && File::exists(public_path($user->image))){
				File::delete($user->image);
			}

			$image = $req->file('image');

			$name = time().'.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('img/');
			$image->move($destinationPath, $name);

			$user->image = 'img/' . $name;
			$user->save();

			return response()->json(['msg' => 'Upload berhasil...']);
		} catch(Exception $e){
			return response()->json(['err' => $e->getMessage()]);
		}
	}

	function register(Request $req){
		try{
			$field = ['name' => 'nama', 'email' => 'email', 'password' => 'password'];
			foreach ($field as $field => $name) {
				if(!$req->get($field))
					throw new Exception("Field " . $name . " harus diisi.....");
			}

			User::create([
				'name' => $req->get('name'),
				'email' => $req->get('email'),
				'password' => bcrypt($req->get('password')),
			]);

			if (Auth::attempt(['email' => $req->get('email'), 'password' => $req->get('password')])) {
				return response()->json(['redirect' => '/']);
			}else{
				throw new Exception("Username atau password salah...");
			}

		}catch(Exception $e){
			return response()->json(['err' => $e->getMessage()]);
		}
	}

	function friends(){
		if (!Auth::check()){
			return redirect('login');
		}else{
			return view('friends');
		}
	}

	function list(Request $req){
		$collectedfriends = $this->getFriends();
		$totalrows = DB::select('select count(*) totalrows from users where id != \'' . Auth::id() . '\'')[0]->totalrows;
		$list = DB::select('select users.id, users.image, users.name from users where id != \'' . Auth::id() . '\' order by name limit ' . $req->get('limit') . ' offset ' . $req->get('offset'));
		return response()->json(['totalrows' => $totalrows, 'list' => $list, 'flist' => $collectedfriends]);
	}

	function add(Request $req){
		try{
			if(!$req->get('id'))
				throw new Exception("User tidak ditemukan...");

			$user = Auth::id();
			$where = $user < $req->get('id') ? 'user1_id = \'' . $user . '\' and user2_id = \'' . $req->get('id') . '\'' : 'user1_id = \'' . $req->get('id') . '\' and user2_id = \'' . $user . '\'' ;
			$friends = DB::select('select * from friendships where ' . $where);

			$user1 = $user < $req->get('id') ? $user : $req->get('id');
			$user2 = $user > $req->get('id') ? $user : $req->get('id');

			if(count($friends) == 0){
				DB::insert('insert into friendships (user1_id, user2_id) values (\'' . $user1 . '\', \'' . $user2 . '\')');
				return response()->json(['msg' => 'Pertemanan berhasil diupdate.']);
			}else{
				throw new Exception("Relasi tidak ditemukan...");
			}
		}catch(Exception $e){
			return response()->json(['err' => $e->getMessage()]);
		}
	}

	function remove(Request $req){
		try{
			if(!$req->get('id'))
				throw new Exception("User tidak ditemukan...");

			$user = Auth::id();
			$where = $user < $req->get('id') ? 'user1_id = \'' . $user . '\' and user2_id = \'' . $req->get('id') . '\'' : 'user1_id = \'' . $req->get('id') . '\' and user2_id = \'' . $user . '\'' ;

			$friends = DB::select('select * from friendships where ' . $where);

			if(count($friends) > 0){
				DB::delete('delete from friendships where ' . $where);
				return response()->json(['msg' => 'Pertemanan berhasil diupdate.']);
			}else{
				throw new Exception("Relasi tidak ditemukan...");
			}
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
