<?php

/** @var \League\OAuth1\Client\Server\User */
$ptt = $args['ptt'];

?>
<h2><?php echo $ptt ? 'Edit' : 'New'; ?> Timer</h2>

<form class="ptt-details-form" method="GET" action="">

	<input type="hidden" name="step" value="ptt-save" />

	<input type="hidden" name="ptt-id" value="<?php echo $ptt ?
		htmlspecialchars( $ptt['id'] ) :
		''; ?>" />

	<input type="hidden" name="ptt[status]" value="publish" />

	<div class="details">
		<label>Task:
			<input type="text" name="ptt[title]"
				value="<?php echo $ptt ?
					htmlspecialchars( $ptt['title']['raw'] ) :
					''; ?>" required />
		</label>

		<label>Description:
			<textarea name="ptt[content]"><?php echo $ptt ?
					nl2br( htmlspecialchars( $ptt['content']['raw'] ) ) :
					''; ?></textarea>
		</label>

		<label>Time:
			<input type="text" name="ptt[pwptt_hours]"
				value="<?php echo $ptt ?
					htmlspecialchars( $ptt['pwptt_hours'] ) :
					''; ?>" placeholder="1.75" required />
		</label>

	</div>

	<button type="submit">Submit</button>

</form>

<?php /*<p>Connected to <code><?php echo htmlspecialchars( $_SESSION['site_base'] ) ?></code>.
	<a class="reset" href="?step=reset">Reset?</a></p>*/ ?>
