<?php require_once("./server/utility.php");
if($count > 1): ?>
<br><br>

<div class="center">
	<div class="btn-group">
		<?php if($page > 1): ?>
		<a class="btn btn-outline-success" href="<?= addSearch("page", 1) ?>">&lt;&lt;</a>
		<a class="btn btn-outline-success" href="<?= addSearch("page", $page - 1) ?>">&lt;</a>
		<?php else: ?>
		<a class="btn btn-outline-success disabled">&lt;&lt;</a>
		<a class="btn btn-outline-success disabled">&lt;</a>
		<?php endif; ?>

		<?php
		$start = max(1, $page - 5);
		$end = min($count, $page + 5);

		if($start > 1)
		{
			?>
			<a class="btn btn-outline-success disabled">...</a>
			<?php
		}

		for($i = $start; $i <= $end; $i++)
		{
			if($i != $page)
			{
				?>
				<a class="btn btn-outline-success" href="<?= addSearch("page", $i);?>"><?= $i ?></a>
				<?php
			}
			else
			{
				?>
				<a class="btn btn-success"><?= $i ?></a>
				<?php
			}
		}
	
		if($end < $count)
		{
			?>
			<a class="btn btn-outline-success disabled">...</a>
			<?php
		}
		?>

		<?php if($page < $count): ?>
		<a class="btn btn-outline-success" href="<?= addSearch("page", $page + 1); ?>">&gt;</a>
		<a class="btn btn-outline-success" href="<?= addSearch("page", $count); ?>">&gt;&gt;</a>
		<?php else: ?>
		<a class="btn btn-outline-success disabled">&gt;</a>
		<a class="btn btn-outline-success disabled">&gt;&gt;</a> 
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>