<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Twitter App</title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

	<style>
	body {
		font-family: 'Lato';
		margin: 0;
		background: #F2F2F2;
	}

	.wrapper{
		width: 100%;
	}

	.app-header{
		background: #4BACC6;
		text-align: center;
		padding: 5px;
		color: #fff;
	}
	
	.app-header h3{
		margin: 10px;
	}
	
	.menu {
		text-align: right;
		font-size: 0.9em;
		color: #bfbfbf;
		padding: 0 20px;
	}
	
	.menu li{
		display: inline-block;
		padding-left: 10px;
	}
	
	.img-wrapper{
		padding-top: 100%;
		position: relative;
	}

	.img-container{
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		overflow: hidden;
		border-radius: 100%;
	}

	.img-container img{
		width: 100%;
	}

	.app-body{}

</style>
</head>
<body id="app-layout">
	<div class="wrapper">
		<div class="app-header">
			<h3>Twitter Application</h3>
		</div>
		<div class="app-body">
			@if (Auth::check())
			<div class="menu">
				<ul>
					<li><a href="/">Main</a></li>
					<li><a href="/user">Akun</a></li>
					<li><a href="/friends">Friends</a></li>
					<li><a href="/logout">Logout</a></li>
				</ul>
			</div>
			@endif
			@yield('content')
		</div>
	</div>
</body>
</html>
