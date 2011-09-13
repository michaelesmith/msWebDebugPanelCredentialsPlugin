<?php echo __('Currently:') ?> <?php echo $sf_user->getGuardUser()->username ?> - <?php echo $sf_user->getGuardUser()->__toString() ?>
<form method="get" action="<?php echo url_for('msWebDebugPanelCredentialsMasquerade') ?>">
	<?php echo __('Change to:') ?>
	<select name="user_id">
		<?php foreach($users as $user){ ?>
			<option value="<?php echo $user->id ?>"><?php echo $user->username ?> - <?php echo $user->__toString() ?></option>
		<?php } ?>
	</select>
	<input type="submit" />
</form>
