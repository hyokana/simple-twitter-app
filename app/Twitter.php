<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Twitter extends Model{

	protected $fillable = array('user_id', 'twitter');

	public function user(){
		return $this->belongsTo('App\User');
	}

}
