<!DOCTYPE html>
<html>
	<head>
		<title><?php echo isset($title) ? $title : 'Admin' ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Bootstrap -->
		<link href="/jam-tart/css/bootstrap.min.css" rel="stylesheet" media="all">
		<link href="/jam-tart/css/bootstrap-responsive.min.css" rel="stylesheet" media="all">
		<link href="/jam-tart/css/bootstrap-fileupload.min.css" rel="stylesheet" media="all">
		<link href="/jam-tart/css/chosen.css" rel="stylesheet" media="all">
		<link href="/jam-tart/css/general.css" rel="stylesheet" media="all">
		
		<?php echo View::factory('tart/layout/header') ?>

	</head>
	<body>
		<?php echo Tart_Html::navigation(); ?>
		<div class="container-fluid">
			<?php echo Tart_Html::notifications(); ?>
			<div class="row-fluid">
				<?php if ($sidebar): ?>
					<div class="span9">
						<?php echo $content ?>
					</div>
					<div class="span3">
						<?php echo $sidebar ?>
					</div>
				<?php else: ?>
					<?php echo $content ?>
				<?php endif ?>
			</div>
		</div>
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<script src="/jam-tart/js/plugins.min.js"></script>
		<script src="/jam-tart/js/bootstrap.min.js"></script>
		<script src="/jam-tart/js/bootstrap-extensions.min.js"></script>
		<script src="/jam-tart/js/general.js"></script>

		<?php echo View::factory('tart/layout/footer') ?>
	</body>
</html>