<?php $form = Jam::form($session, 'tart_general') ?>
<div class="fluid-row">
	<div class="span6 offset3">
		<?php echo Form::open(Tart::uri('session', 'new'), array('class' => 'form-horizontal')) ?>
			<div class="control-group">
				<div class="controls">
					<h3>Login</h3>
				</div>
			</div>
			<?php echo $form->row('input', 'email') ?>
			<?php echo $form->row('password', 'password') ?>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox">
						<?php echo $form->checkbox('remember_me') ?>
						Remember me
					</label>
				</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn">Login</button>
			</div>
		<?php echo Form::close(); ?>
	</div>
</div>