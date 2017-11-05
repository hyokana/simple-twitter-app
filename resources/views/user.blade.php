@extends('layouts.app')

@section('content')

<style>
.p-wrapper{
	min-width: 350px;
	max-width: 777px;
	display: flex;
	margin: auto;
}

.foto{
	width: 250px;
	box-sizing : border-box;
	padding: 0 20px;
	text-align: center;
}

.foto button{
	margin-top: 20px;
	padding: 7px 30px;
	background: #fff;
	border: none;
	box-shadow: -2px 2px 3px 0px rgba(0, 0, 0, 0.50);
}

.detail{
	flex-grow: 1;
	padding: 20px;
	margin: 0 20px;
	background: #EBF1DE;
	box-shadow: -2px 2px 5px 0px rgba(0, 0, 0, 0.50);
}

[name='image']{
	display: none;
}

.btn-container{
	text-align: right;
}

.btn-container button{
	padding: 7px 40px;
	background: #4F81BD;
	color: #fff;
	border: none;
	box-shadow: -2px 2px 3px 0px rgba(0, 0, 0, 0.50);
}

input{
	box-sizing : border-box;
	padding: 8px;
	width: 100%;
	border: 1px solid #D9D9D9;
	margin-bottom: 15px;
	box-shadow: -2px 2px 3px 0px rgba(0, 0, 0, 0.50);
}

</style>

{{ csrf_field() }}

<div class="p-wrapper">
	<div class="col foto">
		<div class="img-wrapper">
			<div class="img-container">
				<img src='{{ asset("img/default-user-image.png") }}' />
			</div>
		</div>
		<button type="button" onclick="openBrowser()">Upload</button>
		<input id="Z" type="file" name="image" accept="image/*" onchange="upload()">
	</div>
	<div class="col detail">
		<input type="text" placeholder="Nama" name="name">
		<span name="name-err"></span>

		<input type="email" placeholder="Email" name="email">
		<span name="email-err"></span>

		<input type="password" placeholder="Password" name="password">

		<div class="btn-container">
			<button type="button" onclick="update()">
				Save
			</button>
		</div>
	</div>
	<clear></clear>
</div>

<script>
	function openBrowser(){
		document.querySelector('[name="image"]').click();
	}

	function upload(){
		if(!confirm("Are you sure to change your profil picture?")){
			document.querySelector('input[type=file]').value = '';
			return false;
		}

		var callback = (rsp) => {
			if(rsp.err)
				alert(rsp.err);
			else
				getData();
		} 

		var param = { 'image': document.querySelector('input[type=file]').files[0] }; 
		httpSend('user/upload', callback, param)
	}

	getData();
	function getData(){
		document.querySelector('input[type=file]').value = '';
		var callback = (rsp) => {
			var field = ["name", "email"]
			for (var i of field){
				document.querySelector('[name="' + i + '"]').value = rsp[i];
			}
				document.querySelector('[name="password"]').value = '';

			if(rsp.image)
				document.querySelector('img').setAttribute("src", rsp.image);
			else
				document.querySelector('img').setAttribute("src", 'img/default-user-image.png');

		}
		var path = 'user/getuser';
		httpSend(path, callback);
	}

	function valid(){
		var field = {name: 'Nama', email: 'Email'}
		var valid = true;
		for(var i in field){
			var check = document.querySelector('input[name=' + i + ']').value;
			document.querySelector('span[name=' + i + '-err]').innerHTML = !check ? "Field " + field[i] + " harus diisi..." : "";
			if(!check)
				valid = false;
		}

		return valid;
	}

	function update(){
		if(!valid('register'))
			return false;

		var callback = (rsp) => {
			if(rsp.err){
				alert(rsp.err);
			}else{
				getData();
				alert('Update data berhasil...')
			}
		} 

		var field = ['name', 'email', 'password']
		var param = {};
		for(var i of field){
			param[i] = document.querySelector('input[name=' + i + ']').value
		}
		httpSend('user/update', callback, param)
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
