<span class="remoteselect-item">
	<a href="<?php echo Tart::uri($item) ?>" class="btn btn-link"><?php echo htmlentities($item->name()) ?></a>
	<a href="#" data-dismiss="remoteselect" class="btn">Remove</a>
	<input type="hidden" name="<?php echo $name ?>" value="<?php echo $id ?>">
</span>
