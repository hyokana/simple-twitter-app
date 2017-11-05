@extends('layouts.app')

@section('content')

<style>

.theader{
	padding: 10px;
	background: #D9D9D9;
	margin-bottom: 15px;
}

input{
	box-sizing : border-box;
	padding: 10px;
	width: 100%;
	border: 1px solid #D9D9D9;
	margin-bottom: 10px;
}

.btn-container{
	text-align: right;
}

.btn-container button, .bottom button{
	padding: 7px 40px;
	background: #4F81BD;
	color: #fff;
	border: none;
}

.lpost, .rpost {
	display: flex;
	align-items: center;
	margin: auto;
	margin-bottom: 15px;
	background: #fff;
	padding: 20px;
	max-width: 777px;
}

.rpost{
	background: #EBF1DE;
	flex-direction: row-reverse;
	text-align: right;
}

.lpost .foto{
	margin-right: 25px;
}

.rpost .foto{
	margin-left: 25px;
}

.foto{
	width: 50px;
}

.img-container{
	border: 2px solid #BFBFBF;
}

.dpost strong{
	margin-bottom: 5px;
}

.dpost p{
	margin: 0;
}

.bottom{
	text-align: center;
}

.bottom button{
	margin-bottom: 15px;
}
</style>

{{ csrf_field() }}
<div class="p-wrapper">
	<div class="theader">
		<input type="text" name="post" placeholder="Update status" onkeyup="checkEnter(event)" />
		<div class="btn-container">
			<button type="button" onclick="post()">
				Update
			</button>
		</div>
	</div>
	<div class="tbody"></div>
	<div class="bottom">
		<button class="loadmore" type="button" onclick="getList()">Load More</button>
	</div>
</div>

<script>

	var totalrows = 0;
	var offset = 0;
	var limit = 10;

	initList();
	function initList(){
		totalrows = 0;
		offset = 0;
		document.querySelector('.tbody').innerHTML = '';
		document.querySelector('[name=post]').value = '';
		getList();
	}

	function getList(){
		var callback = (rsp) => {
			totalrows = rsp.totalrows;
			offset = offset + rsp.list.length;

			if(!(offset < totalrows))
				document.querySelector('.loadmore').setAttribute('disabled', 'disabled');

			fetchingList(rsp);
		}
		var param = { offset: offset, limit: limit }
		httpSend('twitter/list', callback, param);
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
			<div class='dpost'>
			<strong>` + p.name + `</strong><p>` + p.twitter + `</p>
			</div>`
			var div = document.createElement('div');
			div.className = (p.user_id == "{{ Auth::id() }}") ? 'rpost' : 'lpost';
			div.innerHTML = str;
			appendbody.appendChild(div);
		}
	}

	function checkEnter(e){
		if(e.keyCode == 13)
			post();
	}

	function post(){
		var callback = (rsp) => {
			if(rsp.err)
				alert(rsp.err)
			else
				initList();
		}

		var param = {
			post: document.querySelector('[name=post]').value
		}
		if(param.post)
			httpSend('twitter/post', callback, param);
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
