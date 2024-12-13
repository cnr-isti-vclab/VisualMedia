<div class="row">
	<div class="col-12">
		<h2><?=$collection->title?></h2>

<? if($user && ($collection->userid == $user->id || $user->role == 'admin')) { ?>
			<a href="/collection/manage/<?=$collection->label?>">
				<i style="color:#bbb; display:inline-block; position:relative; top:8px; font-size:18px" class="d-block float-right fas fa-cog"></i>
			</a>
<? } ?>


		<p><?=$collection->description?></p>
	</div>
	<div class="col-12">
		<?=$browsertable?>
	</div>
</div>
