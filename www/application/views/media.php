<div class="row">
	<div class="col-lg-12">
		<div style="width:100%; padding-bottom:66%; position:relative;">
			<iframe id="thumb" allowfullscreen allow="fullscreen" style="position:absolute; top:0px; left:0px; width:100%; height:100%; border-width:0px" src="<?=$media->secretlink?>?standalone"></iframe>
		</div>
	</div>

	<div class="col-12 mt-4">
		<h2>
			<img src="/images/<?=$media->media_type?>32.png">
			<?=$media->title?>
<? if($user && ($media->userid == $user->id || $user->role == 'admin')) { ?>
			<a href="/media/<?=$media->label?>">
				<i style="color:#bbb; display:inline-block; position:relative; top:8px; font-size:18px" class="d-block float-right fas fa-cog"></i>
			</a>
<? } ?>
		</h2>
		<hr/>

		<?=$media->description?>
<? if($media->owner) { ?>
		<p class="small">Copyright: <a href="<?=$media->url?>"><?=$media->owner?></a></p>
<? } ?>



<? if($media->collections) { ?>

	<? if(count($media->collections) == 1) { 
		$collection = $media->collections[0]; ?>
		<h4>Collection: <a href="/collections/<?=$collection->label?>"><?=$collection->title?></h4>
	<? } else { ?>
		<h4>Collections:</br>
		<? foreach($media->collections as $collection) { ?>
			Collection: <a href="/collections/<?=$collection->label?>"><?=$collection->title?><br/>
		<? } ?>
		</h4>
	<? } ?>

<? } else if($media->collection) {?>
		<p><?=$media->collection?></p>
<? } ?>
	</div>
</div>

