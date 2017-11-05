@extends('layouts.app')

@section('content')

<style>
.form-container{
	margin: auto;
	max-width: 30%;
	min-width: 350px;
	margin-top: 40px;
}

h2{
	margin: 15px 0;
	color: #BFBFBF;
}

input{
	box-sizing : border-box;
	padding: 10px;
	width: 100%;
	border: 1px solid #D9D9D9;
}

.btn-container{
	text-align: center;
}

.btn-container button{
	padding: 7px 40px;
	background: #4BACC6;
	color: #fff;
	border: none;
	margin-bottom: 15px;
}

hr{
	margin-top: 40px;
	width: 90%;
	min-width: 350px;
	border: none;
	height: 3px;
	background-color: #BFBFBF;
}

span[name$="-err"]{
	color: red;
	margin-bottom: 15px;
	font-size: 0.9em;
	margin-top: 5px;
	display: block;
}

</style>

{{ csrf_field() }}

<div class="form-container login">
	<h2>LOGIN</h2>

	<input type="email" placeholder="Email" name="email">
	<span name="email-err"></span>

	<input type="password" placeholder="Password" name="password">
	<span name="password-err"></span>

	<div class="btn-container">
		<button type="button" onclick="login()">
			Login
		</button>
	</div>
</div>

<hr>

<div class="form-container register">
	<h2>REGISTER</h2>

	<input type="email" placeholder="Email" name="email">
	<span name="email-err"></span>

	<input type="text" placeholder="Nama" name="name">
	<span name="name-err"></span>

	<input type="password" placeholder="Password" name="password">
	<span name="password-err"></span>

	<div class="btn-container">
		<button type="button" onclick="register()">
			Register
		</button>
	</div>
</div>


<script>
	function login(){
		if(!valid('login'))
			return false;

		httpSend('login')
	}

	function register(){
		if(!valid('register'))
			return false;

		httpSend('register')
	}

	function valid(form){
		var field = {email: 'Email', password: 'Password'}
		if(form == 'register')
			field.name = 'Nama';

		var valid = true;
		for(var i in field){
			var check = document.querySelector('.' + form + ' input[name=' + i + ']').value;
			document.querySelector('.' + form + ' span[name=' + i + '-err]').innerHTML = !check ? "Field " + field[i] + " harus diisi..." : "";
			if(!check)
				valid = false;
		}

		return valid;
	}

	function httpSend(form){
		var field = {email: 'Email', password: 'Password'}
		if(form == 'register')
			field.name = 'Nama';

		var formData = new FormData(); 
		for(var i in field){
			formData.append(i, document.querySelector('.' + form + ' input[name=' + i + ']').value);
		}
		formData.append("_token", document.querySelector('input[name=_token]').value);

		var xmlHttp = new XMLHttpRequest();
		xmlHttp.onreadystatechange = function(){
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
				var resp = JSON.parse(xmlHttp.responseText);
				if(resp.err)
					alert(resp.err);
				else
					window.location.href = resp.redirect;
			}
		}
		xmlHttp.open("post", form); 
		xmlHttp.send(formData);
	}
</script>

@endsection
