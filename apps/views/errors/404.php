<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');  ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Error <?php echo $code; ?></title>
		<style>
			body {
				background: #efefef;
				font-family: Tahoma, Arial, sans-serif;
				color: #444;
			}
			#wrapper {
				background: #fff;
				width: 65%;
				min-width: 680px;
				margin: 10% auto 0 auto;
				padding: 5px 15px;
				border: 1px solid #b9b9b9;
				box-shadow: 0 0 10px #a9a9a9;
				-webkit-box-shadow: 0 0 10px #a9a9a9;
				-moz-box-shadow: 0 0 10px #a9a9a9;
			}
			h2 {
				color: #4767c4;
				border-bottom: 1px solid #b9b9b9;
				padding-bottom: 15px;
			}
		</style>
	</head>
	<body>
		<div id="wrapper">
			<h2>An error has occured : <?php echo $code; ?></h2>
			<p><?php echo $notice; ?></p>
		</div>
	</body>
</html>