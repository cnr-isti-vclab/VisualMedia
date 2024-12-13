<div class="row">
	<div class="col-12">
		<h2>Collection: <a href="/collections/<?=$collection->label?>"><?=$collection->title?></a></h2>
	</div>

	<div class="col-lg-12 mt-5">

		<div id="formcontainer">
<form class="form-horizontal" role="form" id="metadata" action="#" method="post" enctype="multipart/form-data" accept-charset="UTF-8">

	<input type="hidden" name="id" value="<?=$collection->id?>"/>
	<input type="hidden" name="label" value="<?=$collection->label?>"/>

	<div class="mb-1 row">
		<label for="title" class="col-sm-3">Title</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="title" name="title" placeholder="A title for the media es. The Unusual Collection" 
			value="<?=$collection->title?>" required="required">
		</div>
	</div>

	<div class="mb-1 row">
		<label for="label" class="col-sm-3 control-label">Short Description</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="description" name="description" placeholder="Short description."
				value="<?=$collection->description?>" required="required">
		</div>
	</div>

	<div class="mb-1 row">
		<label for="description" class="col-sm-3">Long description</label>
		<div class="col-sm-9">
			<textarea class="mb-0 form-control" id="body" name="body" rows="5" style="width:100%; padding:6px 12px 6px 12px" 
		 placeholder="A long description"><?=$collection->body?></textarea>
		</div>
	</div>

	<div class="mb-1 row">
		<label for="owner" class="col-sm-3 control-label">Web resource</label>
		<div class="col-sm-9">
			<input type="url" class="form-control" id="url" name="url" placeholder="An url for a web page about the collection."
		 value="<?=$collection->url?>" >
		</div>
	</div>

	<div class="form-group row mt-4">
		<div class="col-sm-12">

		<? if($collection->id === NULL) { ?>
			<button id="create-collection" type="submit" class="btn btn-success"><i class="fas fa-check"></i> Create</button>
		<? } else { ?>

			<button id="update-collection" type="submit" class="btn btn-success"><i class="fas fa-check"></i> Update</button>
			<a href="/collection/batch/<?=$collection->label?>" id="batch-collection" class="btn btn-success"><i class="fas fa-check"></i> Batch upload</a>
			<button class="btn <?=$collection->publish? 'btn-success':'btn-info'?>" id="publish" data-published="<?=$collection->publish?>">
			<i class="fas fa-toggle-<?=$collection->publish?'on':'off'?>"></i> <span><?=$collection->publish? 'Published':'Publish'?></span></button>

		<? } ?>
			<button data-title="<?=$collection->title?>" type="button" name="remove" style="float:right;" class="btn btn-danger">
		<i class="fas fa-trash"></i> Remove</button>

		</div>
	</div>

<!-- here goes the list of media in the resource -->


</div>

<? if($collection->id !== NULL) { ?>

<div class="row mt-5">
	<div class="col-12">
	<h4>Media</h4>
	<hr/>

	<table id="mediatable" class="table table-sm table-striped">
	<thead>
		<tr><th></th><th>type</th><th>label</th><th>collection</th><th>status</th><th>creation</th><th>publish</th><th>Copy!</th></tr>
	</thead>
	<tbody>
<? foreach($objects as $j) { ?>
		<tr>
		<td class="shrink"><input type="checkbox" data-media="<?=$j->id?>"  
			<?=$j->collectionid === $collection->id ? 'checked':''?>/></td>
		<td class="shrink"><img src="/images/<?=$j->media_type?>32.png"></td>
		<td class="ex"> <a href="/media/<?=$j->label?>"><?=$j->label?></a></td>
		<td class="ex"> <a href="/media/<?=$j->collection?>"><?=$j->collectionlabel?></a></td>
		<td class="shrink"><?=$j->status?></td>    
		<td class="shrink"><?=date_format(date_create($j->creation), 'Y-m-d')?></td>
		<td class="shrink"><? if($j->publish == 1) { ?><span class="fas fa-check"></span><? } ?></td>
		<td class="shring"><a class="copyconfig" href="/collection/copyConfig/<?=$collection->id?>/<?=$j->id?>"><i class="fas fa-copy"></i></a></td>
		</tr>
<? } ?>
	</tbody>
	</table>
	</div>
</div>
<? } ?>

<script src="/js/pbTable.min.js"></script>
<script>

$('.copyconfig').click(() => {
	let goon = confirm("You are copying the visualization options of this object to all other objects in the collection.\n" +
		"Orientation matrix and initial position will be preserved, everything else will be lost.\n" +
		"Are you sure?");
	if(!goon)
		e.preventDefault();
});

$('input[data-media]').change((e) => {
	let input = $(e.target);
	let media =  $(input).attr('data-media');
	let checked = input.is(':checked');
	let action = checked ? 'add' : 'remove';

	$.post(`/collection/<?=$collection->id?>/${action}/${media}`,
		(d) => {
			if(d.error) {
				alert(d.error);
				$(`input[data-media=${media}]`).attr('checked', !checked);
				return;
			}
		},
		'json').fail(networkProblem);
});


$(document).ready(function(e) {
	$('#mediatable').pbTable({
		selectable: true,
		sortable:true,
		toolbar:{
			enabled:true,
			filterBox:true,
			tags:[
				{ display:'3D', toSearch:'3d' },
				{ display:'RTI', toSearch:'rti' },
				{ display:'Img', toSearch:'img' },
			], 
			buttons:[]
		}
	});

	/*$('#collections').pbTable({
		selectable: true,
		sortable:true,
		toolbar:{ enabled:false }
	}); */
});


$('button[name=remove]').click(function(e) {
	var r = confirm("Are you sure you want to delete this collection?");
	if(!r) return;

	$.getJSON('/collection/delete/<?=$collection->id?>', function(d) {
		if(d.error) {
			alert(d.error);
			return;
		}
		window.location = '/profile';
	}).fail(function () { alert("Network error"); });
	e.preventDefault();
});

let updated = true;

function needsUpdate() {
	$('#submit-metadata').addClass('btn-primary');
	$('#submit-metadata').removeClass('btn-success');
	$('#submit-metadata i').addClass('fa-sync');
	$('#submit-metadata i').removeClass('fa-check');
	updated = false;
}

$('#metadata input, #metadata textarea').change(needsUpdate);


function networkProblem() {
	alert( "Oooopps, looks like there is a network, or server problem." );
}

$('#metadata').submit(function(e) {
	e.preventDefault();

	let data = $(this).serializeArray();
	let id = $('input[name=id]').val();
	if(!id) {
		$.post('/collection/update', data, (d) => {
				window.location = '/profile';
			}, 
			'json').fail(networkProblem);
		return;
	}

	$.post('/collection/update', data,
		function(d) {
			if(d.error) {
				alert(d.error);
				return;
			}
			$('#submit-metadata').removeClass('btn-primary');
			$('#submit-metadata').addClass('btn-success');
			$('#submit-metadata i').removeClass('fa-sync');
			$('#submit-metadata i').addClass('fa-check');
			updated =true;
		},
		'json').fail(networkProblem);

});

$('#publish').click(function(e) {

	var button = $(this);
	var published = (button.attr('data-published') == "1"); //already published

	let url = published? "/collection/unpublish" : "/collection/publish";

	var formData = new FormData();
	formData.append("collection", "<?=$collection->id?>");
	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.send(formData);
	xhr.onload = () => {
		if (xhr.readyState === 4) {
			if (xhr.status === 200) {
				published = !published; //reflect cuirrent status no
				button.removeClass('btn-warning btn-info');
				button.addClass(published ?'btn-success':'btn-info');
				button.find('span')[0].innerHTML = (published? "Published": "Publish");
				button.attr('data-published', published? "1":"0");
			} else {
				alert("There was a problem updating the collection.");
			}
		}
	};
	xhr.onerror = () => { 
		alert("There was a problem updating the collection.");
	}
	return;
});




</script>
