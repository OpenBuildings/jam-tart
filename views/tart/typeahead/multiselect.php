<span class="thumbnail span3 remoteselect-item">
	<input type="hidden" name="<?php echo $name ?>" value="<?php echo $item->id() ?>">
	<div>
		<a href="<?php echo Tart::uri($item) ?>" class="btn btn-link"><?php echo $item->name() ?></a>
	</div>
	
	<a href="#" data-dismiss="remoteselect" class="btn">Remove</a>
</span>