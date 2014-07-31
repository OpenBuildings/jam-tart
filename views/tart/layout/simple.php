<!DOCTYPE html>
<html>
	<head>
		<title><?php echo isset($title) ? $title : 'Admin' ?></title>
		<link href="<?=URL::site('/jam-tart/css/bootstrap.min.css')?>" rel="stylesheet" media="screen">
		<link href="<?=URL::site('/jam-tart/css/bootstrap-responsive.min.css')?>" rel="stylesheet" media="screen">
		<link href="<?=URL::site('/jam-tart/css/general.css')?>" rel="stylesheet" media="all">
	</head>
	<body>
		<?php echo Tart_Html::navigation(array(Tart::uri('session', 'new') => 'Login')) ?>
		<div class="visible-desktop nav-offseter"></div>
		<div class="container-fluid">
			<div class="row-fluid">
				<?php echo Tart_Html::notifications(); ?>
				<?php echo $content ?>
			</div>
		</div>
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="<?=URL::site('/jam-tart/js/plugins.min.js')?>"></script>
		<script src="<?=URL::site('/jam-tart/js/bootstrap.min.js')?>"></script>
	</body>
</html>