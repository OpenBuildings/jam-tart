<!DOCTYPE html>
<html>
	<head>
		<title><?php echo isset($title) ? $title : 'Admin' ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="/jam-tart/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link href="/jam-tart/css/jam-tart.css" rel="stylesheet" media="screen">
		<link href="/jam-tart/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
	</head>
	<body>
		<?php echo Tart_Html::navigation(array(Tart::uri('session', 'new') => 'Login')) ?>
		<div class="container-fluid">
			<div class="row-fluid">
				<?php echo Tart_Html::notifications(); ?>
				<?php echo $content ?>
			</div>
		</div>
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="/jam-tart/js/underscore-min.js"></script>
		<script src="/jam-tart/js/bootstrap.js"></script>
	</body>
</html>