<?php
/**
 * Main layout.
 *
 * @package PTTRC
 * @subpackage views
 */
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo htmlspecialchars( $title ); ?></title>
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i" rel="stylesheet">
		<link rel="stylesheet" href="css/style.css" />
		<link href="https://opensource.keycdn.com/fontawesome/4.7.0/font-awesome.min.css" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
		<script src="js/scripts.js"></script>
	</head>
	<body class="<?php echo $class; ?>">
		<div class="container">
			<!--<h1>Timers</h1>-->

			<?php if ( $error ) : ?>

				<div class="warn"><?php echo $error; ?></div>

			<?php endif; ?>

			<?php echo $content; ?>
			<div class="help-buttons-wrapper">
				<?php global $step;
				if ( $step !== 'reset' ) : ?>
					<span class="help-button fa fa-question-circle" title="Help"></span>
					<a class="new-tab-link" href="" title="Open in new Tab" target="_blank">
						<span class="fa fa-window-restore"></span>
					</a>
				<?php endif; ?>
				<?php if ( $step !== 'reset' &&
					$step !== 'show-wordpress' ) : ?>
					<a class="reset-link" href="?step=reset" title="Reset">
						<span class="fa fa-chain-broken"></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</body>
</html>
