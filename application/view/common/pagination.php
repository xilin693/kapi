<p class="pagination">
	<?php if ($previous_page): ?>
		<a href="<?php echo str_replace('{page}', $previous_page, $urls) ?>">&laquo;&nbsp;<?php echo '前页'; ?></a>
	<?php else: ?>
		&laquo;&nbsp;<?php echo '前页'; ?>
	<?php endif ?>


	<?php if ($total_pages < 13): /* « 上一页  1 2 3 4 5 6 7 8 9 10 11 12  下一页 » */ ?>

		<?php for ($i = 1; $i <= $total_pages; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong><?php echo $i ?></strong>
			<?php else: ?>
				<a href="<?php echo str_replace('{page}', $i, $urls) ?>"><?php echo $i ?></a>
			<?php endif ?>
		<?php endfor ?>

	<?php elseif ($current_page < 9): /* « 上一页  1 2 3 4 5 6 7 8 9 10 … 25 26  下一页 » */ ?>

		<?php for ($i = 1; $i <= 10; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong><?php echo $i ?></strong>
			<?php else: ?>
				<a href="<?php echo str_replace('{page}', $i, $urls) ?>"><?php echo $i ?></a>
			<?php endif ?>
		<?php endfor ?>

		&hellip;
		<a href="<?php echo str_replace('{page}', $total_pages - 1, $urls) ?>"><?php echo $total_pages - 1 ?></a>
		<a href="<?php echo str_replace('{page}', $total_pages, $urls) ?>"><?php echo $total_pages ?></a>

	<?php elseif ($current_page > $total_pages - 8): /* « 上一页  1 2 … 17 18 19 20 21 22 23 24 25 26  下一页 » */ ?>

		<a href="<?php echo str_replace('{page}', 1, $urls) ?>">1</a>
		<a href="<?php echo str_replace('{page}', 2, $urls) ?>">2</a>
		&hellip;

		<?php for ($i = $total_pages - 9; $i <= $total_pages; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong><?php echo $i ?></strong>
			<?php else: ?>
				<a href="<?php echo str_replace('{page}', $i, $urls) ?>"><?php echo $i ?></a>
			<?php endif ?>
		<?php endfor ?>

	<?php else: /* « 上一页  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  下一页 » */ ?>

		<a href="<?php echo str_replace('{page}', 1, $urls) ?>">1</a>
		<a href="<?php echo str_replace('{page}', 2, $urls) ?>">2</a>
		&hellip;

		<?php for ($i = $current_page - 5; $i <= $current_page + 5; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong><?php echo $i ?></strong>
			<?php else: ?>
				<a href="<?php echo str_replace('{page}', $i, $urls) ?>"><?php echo $i ?></a>
			<?php endif ?>
		<?php endfor ?>

		&hellip;
		<a href="<?php echo str_replace('{page}', $total_pages - 1, $urls) ?>"><?php echo $total_pages - 1 ?></a>
		<a href="<?php echo str_replace('{page}', $total_pages, $urls) ?>"><?php echo $total_pages ?></a>

	<?php endif ?>


	<?php if ($next_page): ?>
		<a href="<?php echo str_replace('{page}', $next_page, $urls) ?>"><?php echo '后页'; ?>&nbsp;&raquo;</a>
	<?php else: ?>
		<?php echo '后页'; ?>&nbsp;&raquo;
	<?php endif ?>

</p>