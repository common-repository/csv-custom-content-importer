<?php
	global $errors;

	if ( count( $errors ) > 0): ?>
		<ul>
			<?php foreach ( $errors as $error ): ?>
				<li><?php echo $error; ?></li>
			<?php endforeach; ?>
		</ul>
<?php endif; ?>