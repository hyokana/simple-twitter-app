@extends('layouts.app')

@section('content')

<style>

.lf{
	display: flex;
	align-items: center;
	max-width: 777px;
	margin: auto;
	margin-bottom: 15px;
	background: #fff;
	padding: 20px;
}

.lf .foto{
	margin-right: 25px;
}

.foto{
	flex-basis: 50px;
}

.img-container{
	border: 2px solid #BFBFBF;
}

.df{
	flex-grow: 1;
	margin-right: 15px;
}

.df strong{
	margin-bottom: 5px;
}

.bottom{
	text-align: center;
}

.bottom button{
	margin-bottom: 15px;
	padding: 7px 40px;
	background: #4F81BD;
	color: #fff;
	border: none;
}

.rem, .add{
	padding: 7px 10px;
	background: #4F81BD;
	border: none;
	color: #fff;
}

.rem{
	background: #ff6600;
} 

</style>

{{ csrf_field() }}
<div class="p-wrapper">
	<div class="tbody"></div>
	<div class="bottom">
		<button class="loadmore" type="button" onclick="getList()">Load More</button>
	</div>
</div>

<script>

	var totalrows = 0;
	var offset = 0;
	var limit = 5;
	var friendlist = [];

	initList();
	function initList(){
		totalrows = 0;
		offset = 0;
		document.querySelector('.tbody').innerHTML = '';
		getList();
	}

	function getList(){
		var callback = (rsp) => {
			totalrows = rsp.totalrows;
			friendlist = rsp.flist;
			console.log(rsp);
			offset = offset + rsp.list.length;

			if(!(offset < totalrows))
				document.querySelector('.loadmore').setAttribute('disabled', 'disabled');

			fetchingList(rsp);
		}
		var param = { offset: offset, limit: limit }
		httpSend('friends/list', callback, param);
	}

	function fetchingList(rsp){
		var appendbody = document.querySelector('.tbody');
		for(var p of rsp.list){
			var str = `<div class='foto'>
			<div class="img-wrapper">
			<div class="img-container">
			<img src='{{ asset("` + p.image + `") }}' />
			</div>
			</div>
			</div>
			<div class='df'><strong>` + p.name + `</strong></div>` +
			(friendlist.indexOf((p.id).toString()) > -1 ? `<button class='rem' onclick='addFriend(` + p.id + `, false)'>Remove</button>` : `<button class='add' onclick='addFriend(` + p.id + `)'>Add</button>`)
			var div = document.createElement('div');
			div.className = 'lf';
			div.innerHTML = str;
			appendbody.appendChild(div);
		}
	}

	function addFriend(id, add = true){
		var callback = (rsp) => {
			if(rsp.err){
				alert(rsp.err);
			}else{
				alert(rsp.msg);
				initList();
			}
		}

		var param = {
			id: id
		}
		if(param.id)
			httpSend('friends/' + (add ? 'add' : 'remove'), callback, param);
	}

	function httpSend(path, callback, param = {}){
		var formData = new FormData(); 
		for(var i in param){
			formData.append(i, param[i]);
		}
		formData.append("_token", document.querySelector('input[name=_token]').value);

		var xmlHttp = new XMLHttpRequest();
		xmlHttp.onreadystatechange = function(){
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
				var resp = JSON.parse(xmlHttp.responseText);
				callback(resp);
			}
		}
		xmlHttp.open("post", path); 
		xmlHttp.send(formData);
	}
</script>

@endsection
