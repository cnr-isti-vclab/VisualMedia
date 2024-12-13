<style>
.truncated {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	width:20%;
}
.table td,.table th {
	padding: .25rem;
	vertical-align: middle;
}
</style>

<div class="row">
	<div class="col-12">
		<h2>Media</h2>

		<table class="table" id="mediatable" style="table-layout: fixed;">
			<thead>
				<th style="width:3%"></th>
				<th style="width:5%">id</th>
				<th style="width:20%">title</th>
				<th style="width:5%">type</th>
				<th style="width:7%">status</th>
				<th style="width:10%">created</th>
				<th style="width:15%">user</th>
				<th style="width:10%">size</th>
				<th style="width:3%">pub.</th>
				<th style="width:3%">pick</th>
			</thead>
			<tbody>
<? foreach($media as $m) { ?>
				<tr>
					<td><a href="/media/config/<?=$m->label?>">
							<i style="color:#bbb; top:4px; font-size:16px" class="d-block float-right fas fa-cog"></i>
						</a></td>
					<td><?=$m->id?></td>
					<td><a href="/media/<?=$m->label?>"><?=$m->title?></a></td>
					<td><?=$m->media_type?></td>
					<td><?=$m->status?></td>
					<td><?=substr($m->creation, 0, 10)?></td>
					<td><?=$m->email?></td>
					<td><?=$m->size?></td>
					<td><input type="checkbox" name="publish" value="<?=$m->id?>" <?=$m->publish? 'checked':''?>></td>
					<td><input type="checkbox" name="picked" value="<?=$m->id?>" <?=$m->picked? 'checked':''?>></td>
				</tr>
	
<? } ?>
			</tbody>
		</table>
	</div>
</div>

<script src="/js/pbTable.min.js"></script>
<script>
$(document).ready(function(e) {
	$('#mediatable').pbTable({
		selectable: true,
		sortable:true,
		toolbar:{
			enabled:true,
			filterBox:true,
			tags:[
				{ display:'3D', toSearch:'3d' },
				{ display:'Img',    toSearch:'img' },
				{ display:'RTI',       toSearch:'rti' },
				{ display:'Album',       toSearch:'album' }
			],
			buttons:[]
		}
	});
});

$('input[name=picked]').on('change', function() {
	var data = [
		{ name:'picked', value:$(this).is(':checked') },
		{ name:'media',  value: $(this).val() }
	];
	$.post('/admin/pick', data, function(d) {
		if(d.error) {
			alert(d.error);
			return;
		}
	});
});

$('input[name=publish]').on('change', function() {
	var data = [
		{ name:'publish', value:$(this).is(':checked') },
		{ name:'media',   value: $(this).val() }
	];
	$.post('/admin/publish', data, function(d) {
		if(d.error) {
			alert(d.error);
			return;
		}
	});
});
</script>
