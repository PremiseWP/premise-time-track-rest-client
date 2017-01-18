<?php
/**
 * New PTT form.
 *
 * @package PTTRC
 * @subpackage views
 */

/** @var \League\OAuth1\Client\Server\User */
$ptt = $args['ptt'];

$clients = $args['taxonomies']['clients'];
$projects = $args['taxonomies']['projects'];
$timesheets = $args['taxonomies']['timesheets'];

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

	<div class="more-fields">
		<a href="#" class="more-link unfold">More</a>

		<div class="ptt-form-clients">
			<label>Clients:</label>

			<?php if ( $clients ) :
				$no_clients = false; ?>
				<ul class="taxonomy-terms-list clients">
				<?php foreach ( (array) $clients as $client ) : ?>

					<li>
						<label>
							<input type="checkbox" name="ptt[clients][]"
								value="<?php echo $client['id']; ?>" class="checkbox"
								<?php if ( $ptt && in_array( $client['id'], $ptt['premise_time_tracker_client'] ) ) echo 'checked'; ?> />
							<?php echo htmlspecialchars( $client['name'] ); ?>
						</label>
					</li>

				<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $clients ) : ?>
				<div class="ptt-client-field-wrapper">
					<a href="#" class="add-new-client-link unfold">Add a new client</a>
			<?php endif; ?>

				<input type="text" name="ptt[clients][new]" value="" />

			<?php if ( $clients ) : ?>
				</div>
			<?php endif; ?>
		</div>

		<hr />

		<label>Projects:
			<div class="new-tag-wrapper">
				<input type="text" name="new-project"
					value="" list="projects" class="new-tag-input" />
				<button class="new-tag-add-button">Add</button>
			</div>

			<?php if ( $projects ) : ?>
				<datalist id="projects">
				<?php foreach ( (array) $projects as $project ) : // Build autocomplete list. ?>

					<option value="<?php echo htmlspecialchars( $project['name'] ); ?>"
						id="<?php echo $project['id']; ?>" />

				<?php endforeach; ?>
				</datalist>
			<?php endif; ?>

			<div class="tags-list projects">
			<?php if ( $projects ) : ?>
				<?php if ( $ptt ) : foreach ( (array) $projects as $project ) : // Build tags list. ?>
					<?php if ( ! in_array( $project['id'], $ptt['premise_time_tracker_project'] ) ) continue; ?>

					<span>
						<button type="button" class="tag-delete-button">
							<span class="remove-tag-icon" aria-hidden="true"></span>
							<span class="screen-reader-text">Remove term</span>
						</button>
						<input type="hidden" name="ptt[projects][]" value="<?php echo $project['id']; ?>" />
						<?php echo htmlspecialchars( $project['name'] ); ?>
					</span>

				<?php endforeach; endif; ?>
			<?php endif; ?>
			</div>

		</label>

		<label>Timesheets:
			<div class="new-tag-wrapper">
				<input type="text" name="new-timesheet"
					value="" list="timesheets" class="new-tag-input" />
				<button class="new-tag-add-button">Add</button>
			</div>

			<?php if ( $timesheets ) : ?>
				<datalist id="timesheets">
				<?php foreach ( (array) $timesheets as $timesheet ) : // Build autocomplete list. ?>

					<option value="<?php echo htmlspecialchars( $timesheet['name'] ); ?>"
						id="<?php echo $timesheet['id']; ?>" />

				<?php endforeach; ?>
				</datalist>
			<?php endif; ?>

			<div class="tags-list timesheets">
			<?php if ( $timesheets ) : ?>
				<?php if ( $ptt ) : foreach ( (array) $timesheets as $timesheet ) : // Build tags list. ?>
					<?php if ( ! in_array( $timesheet['id'], $ptt['premise_time_tracker_timesheet'] ) ) continue; ?>

					<span>
						<button type="button" class="tag-delete-button">
							<span class="remove-tag-icon" aria-hidden="true"></span>
							<span class="screen-reader-text">Remove term</span>
						</button>
						<input type="hidden" name="ptt[timesheets][]" value="<?php echo $timesheet['id']; ?>" />
						<?php echo htmlspecialchars( $timesheet['name'] ); ?>
					</span>

				<?php endforeach; endif; ?>
			<?php endif; ?>
			</div>

		</label>
	</div>

</div>

<button type="submit">Submit</button>

</form>

<?php /*<p>Connected to <code><?php echo htmlspecialchars( $_SESSION['site_base'] ) ?></code>.
	<a class="reset" href="?step=reset">Reset?</a></p>*/ ?>
