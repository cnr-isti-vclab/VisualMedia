<?

function media_type($type) {
	switch($type) {
		case 'nexus': return '3D';
		case 'img': return 'image';
		case 'rti': return 'rti';
	}
}
?>

<style>
.table td,.table th {
	padding: .25rem;
	vertical-align: middle;
}
</style>

<div class="row">
	<h2 class="col-12"><?=$target->username? $target->username : ($target->name? $target->name: $target->email)?> <small>(<?=$user->provider?>)</small></h2>
	<hr/>
	<div class="col-lg-8">
	<form class="form-horizontal" role="form" id="form" action="#" method="post" enctype="multipart/form-data" accept-charset="UTF-8">

		<h4 class="mb-4 mt-4">Information about you (not shown or shared)</h4>
		<div class="form-group row">
			<label for="email" class="col-sm-2 control-label">Email</label>
			<div class="col-sm-10">
				<input type="email" required class="form-control" id="email" name="email" placeholder="Your email, we will write you when the data is processed" 
						 value="<?=$target->email?>" <?=$target->email?'disabled':''?>>
			</div>
		</div>

		<div class="form-group row">
			<label for="name" class="col-sm-2 control-label">Name</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="name" name="name" placeholder="Your name" 
						 value="<?=$target->name?>" required="required">
			</div>
		</div>

		<div class="form-group row">
			<label for="institution" class="col-sm-2 control-label">Institution</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="institution" name="institution" placeholder="The institution you belong to." 
						value="<?=$target->institution?>" >
			</div>
		</div>

		<div class="form-check row">
			<div  class="col-sm-10 offset-sm-2">
				<input type="checkbox" class="form-check-input" id="sendemail" name="sendemail" <?=$target->sendemail==1?'checked':''?>>
				<label class="form-check-label" for="sendemail">Receive email</label>
			</div>
		</div>


		
		<h4 class="mb-4 mt-4">Your visible data</h4>
		<div class="form-group row">
			<label for="email" class="col-sm-2 control-label">Username</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="username" placeholder="Your public username" 
						 value="<?=$target->username?$target->username:strtolower(url_title($target->name))?>" required="required">
			</div>
		</div>


		<button id="submit" style="float:right;" type="submit" class="btn btn-primary">
		<span class="glyphicon glyphicon-ok"></span> Save changes</button>
	</div>

</form>
</div> <!-- row -->

<style>
td.shrink {
	white-space: nowrap;
	width: 1px;
}

td.expand { text-overflow: ellipsis; }
</style>
<? if($collections !== null) { ?>

<div class="row">
	<div class="col-12">
	<div class="d-flex justify-content-between mb-4">
		<h4>Collections</h4>
		<a href="/collection/create" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span>New collection</a>
	</div>



	<table id="collections" class="table table-striped mb-5">
	<thead>
		<tr><th>title</th><th>description</th><th>publish</th><th></th></tr>
	</thead>
	<tbody>
<? foreach($collections as $c) { ?>
		<tr>
		<td><a href="/collections/<?=$c->label?>"><?=$c->title?></a></td>
		<td><?=$c->description?></td>
		<td><? if($c->publish == 't') { ?><span class="glyphicon glyphicon-ok"></span><? } ?></td>
		<td class="shrink"><a href="/collection/manage/<?=$c->label?>"  class="btn btn-sm btn-primary">
			<span class="glyphicon glyphicon-edit"></span> Edit</button></td>
		</tr>
<? } ?>
	</tbody>
	</table>
	</div>
</div>
<? } ?>



<div class="row">
	<div class="col-12">
	<h4>Media</h4>
	<hr/>

	<table id="mediatable" class="table table-striped">
	<thead>
		<tr><th>type</th><th>label</th><th>collection</th><th>status</th><th>creation</th><th>size</th><th>publish</th><th></th></tr>
	</thead>
	<tbody>
<? foreach($objects as $j) { ?>
		<tr>
		<td class="shrink"><img style="float:right;" src="/images/<?=$j->media_type?>32.png"></td>
		<td class="ex"> <a href="/media/<?=$j->label?>"><?=$j->label?></a></td>
		<td class="ex"> <a href="/media/<?=$j->collection?>"><?=$j->collection?></a></td>
		<td class="shrink"><?=$j->status?></td>    
		<td class="shrink"><?=date_format(date_create($j->creation), 'Y-m-d')?></td>
		<td class="shrink"><?='TODO'/*$j->size?human_filesize($j->size):'' */?></td> 
		<td class="shrink"><? if($j->publish == 't') { ?><span class="fas fa-check"></span><? } ?></td>
		<td class="shrink"><button data-title="<?=$j->title?>" data-removemedia="<?=$j->label?>" class="btn btn-danger">
			<i class="fas fa-trash"></i></button></td>
		</tr>
<? } ?>
	</tbody>
	</table>
	</div>
</div>

<script>

document.querySelectorAll('button[data-removemedia]').forEach(button => {
	button.addEventListener('click', function () {
		const line = this.closest('tr');
		const media = this.getAttribute('data-removemedia');
		const title = this.getAttribute('data-title');

		if (!confirm("We are deleting the model '" + title + "'. Proceed?")) return;

		fetch('/media/delete/' + media)
			.then(response => response.json())
			.then(d => {
				if (d.error) {
					alert(d.error);
					return;
				}
				line.remove();
			})
			.catch(err => {
				alert("Error contacting server.");
				console.error(err);
			});
	});
});


$('input[name=username]').on('input', function() {
    var input = $(this);
    var username = input.val();
	if(!/^[a-zA-Z0-9 _]*$/.test(username)) {
		input[0].setCustomValidity("Username '" + username + "' contains non allowed characters, pleas use only a-z_ characters");
		return;
	}
    $.getJSON('/profile/testUsername/'+username, function(a) {
       if(a.exists) {
         input[0].setCustomValidity("Username '" + username + "' already in use.");
         input[0].form.reportValidity();
       } else
         input[0].setCustomValidity("");
    }).fail(function(a) { var error = a.error(); });
});


$('input').change(needsUpdate);

$('#form').submit(function(e) {

	var data = $(this).serializeArray();
	$.post('/profile/update', data, function(d) {
		console.log(d);
		if(d.error) {
			console.log(d.error);
			return;
		}
		$('#submit').removeClass('btn-primary');
		$('#submit').addClass('btn-success');
		$('#submit span').removeClass('fas-sync');
		$('#submit span').addClass('fas-check');
		},
		'json');
	e.preventDefault();
});


function needsUpdate() {
	$('#submit').addClass('btn-primary');
	$('#submit').removeClass('btn-success');
	$('#submit i').addClass('glyphicon-repeat');
	$('#submit i').removeClass('glyphicon-ok');
}

</script>


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
				{ display:'RTI', toSearch:'rti' },
				{ display:'Img', toSearch:'img' },
			],
			buttons:[]
		}
	});

	$('#collections').pbTable({
		selectable: true,
		sortable:true,
		toolbar:{ enabled:false }
	});
});


</script>
