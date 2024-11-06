<div class="row">
<? foreach($media as $m) { ?>

	<div class="col-lg-3 col-md-4 col-sm-6 mt-3">

		<div class="card">
<? if($user && ($m->userid == $user->id || $user->role == 'admin')) { ?>
			<a href="/media/<?=$m->label?>" style="position:absolute; top:0px; right:5px; ">
				<i style="color:#bbb; display:inline-block; position:relative; top:8px; font-size:18px" class="d-block float-right fas fa-cog"></i>
			</a>
<? } ?>
			<a href="/<?=$m->media_type?>/<?=$m->label?>">
				<img class="card-img-top" style="width:100%;" src="/css/loading.gif" data-src="/data/<?=$m->path.$m->thumbnail?>" alt="<?=$m->title?>">
			</a>
			<div class="card-body" style="padding:10px;">
				<a href="/<?=$m->media_type?>/<?=$m->label?>"/>
					<p class="card-title mb-0"><img style="vertical-align:-5px;" height="24px" width="24px" src="/css/loading.gif"  data-src="/images/<?=$m->media_type?>32.png"> <?=$m->title?>
</p>
				</a>
<!--				<p class="card-text"></p> -->
			</div>
		</div>

	</div>

<? } ?>
</div>

