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
		<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
		<script src="https://use.fontawesome.com/a4c255239f.js"></script>
		<script src="js/scripts.js"></script>
	</head>
	<body class="<?php echo $class; ?>">
		<div class="container">
			<!--<h1>Timers</h1>-->

			<?php if ( $error ) : ?>

				<div class="warn"><?php echo $error; ?></div>

			<?php endif; ?>

			<?php echo $content; ?>
			<?php global $step;
			if ( $step !== 'reset' &&
				$step !== 'show-wordpress' ) : ?>
				<a class="reset-link" href="?step=reset" title="Reset">
					<span class="fa fa-chain-broken"></span>
				</a>
			<?php endif; ?>
		</div>
	</body>
</html>
