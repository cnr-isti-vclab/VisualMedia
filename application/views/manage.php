<?
	$class = null;
	$icon = null;
	switch($media->status) {
	case 'uploading':  $class = 'badge-info';    $icon = 'clock'; break;
	case 'on queue':    $class = 'badge-info';    $icon = 'clock'; break;
	case 'ready':      $class = 'badge-success'; $icon = 'ok'; break;
	case 'processing': $class = 'badge-warning'; $icon = 'cog'; break;
	case 'failed':     $class = 'badge-danger';  $icon = 'exclamation-sign'; break; 
	default: $class = 'badge-danger';  $icon = 'exclamation-sign'; $media->status = 'imported'; break; 
	}

	$formats = array('other' => 'far fa-question-circle', 'unknown'=>'far fa-question-circle', '3d'=>'fas fa-cube', 'img'=>'far fa-image', 'rti'=>'far fa-lightbulb');

	$disabled = $media->status == 'processing'? 'disabled' : '';
 ?>

<div class="row">
	<div class="col-12">
		<h2>
<? if($media->media_type) { ?><img src="/images/<?=$media->media_type?>32.png"> <? } ?>
<? if($media->processed) { ?><a href="<?=$media->link?>">	<?=ucfirst($media->title)?></a>
<? } else { ?>
	<?=$media->title?>
<? } ?>
		 <small style="font-size:50%;" class="badge <?=$class?>"> <?=$media->status?></small>
		</h2>

	</div>
</div>


<? if($media->status == 'success') { ?>
<p>Link to online presentation: <a href="<?=$media->link?>"><?=$media->link?></a></p>
<? } ?>

<? if($media->status == 'success') { ?>
<p><a href="/download/<?=$media->secret?>"><button class="btn btn-primary">
<span class="glyphicon glyphicon-download"></span> Download whole presentation</button></a></p>
<? } ?>

<? if($media->status == 'failed') { ?>
<div class="alert alert-danger" role="alert">
  <h4 class="alert-heading">Ooooopppsss.</h4>
  <p>We failed processing your media!</p>
  <hr>
  <p class="mb-0">Error: <?=strip_tags($media->error)?></p>

		<button <?=$disabled?> id="process" class="btn btn-info" <? if($media->status != 'uploading' && $media->status != 'failed') { ?>disablesd<? } ?>>
				<i class="fas fa-cog"></i> Process again</button>
</div>
<? } ?>

<? if($media->status == 'on queue') {?>

<div class="alert alert-danger" role="alert">
	<h4 class="alert-heading">Your dataset is being processed</h4>
	<p>It might take a while depending on the size of your submission.</p>
	<p>The page will refresh <? if($user->sendemail) { ?>and you will receive an email<? } ?> when the model is ready.</p>
	<? if($user->sendemail) { ?><p>You can turn off emails in your profile</p><? } ?>

</div>
<? } ?>

<div class="row">

<? if ($media->processed) { ?>
	<div class="col-lg-9">
		<div style="width:100%; padding-bottom:66%; position:relative;">
			<iframe id="thumb" allowfullscreen allow="fullscreen" style="position:absolute; top:0px; left:0px; width:100%; height:100%; border-width:0px" src="<?=$media->link?>?standalone"></iframe>
		</div>

		<p class="mt-3">
		Public link: <a href="<?=$media->link?>">https://<?=$_SERVER['HTTP_HOST']?><?=$media->link?></a><br/>
		Full-page link: <a href="<?=$media->link?>?standalone">https://<?=$_SERVER['HTTP_HOST']?><?=$media->link?>?standalone</a><br/>
		Private link to the viewer: <a href="<?=$media->secretlink?>">https://<?=$_SERVER['HTTP_HOST']?><?=$media->secretlink?></a></p>
		<p>Iframe code: <pre>&lt;iframe id="thumb" allowfullscreen allow="fullscreen" 
style="position:absolute; top:0px; left:0px; width:100%; height:100%; border-width:0px"
src="http://visual.ariandne-infrastructure.eu<?=$media->link?>?standalone"&gt;&lt;/iframe&gt;</pre></p>


	</div>
	<div class="col-lg-3 mt-2">
		<a class="btn btn-info btn-block" href="/media/download/<?=$media->label?>">
			<i class="fas fa-save"></i> Download</a>

		<a class="btn btn-info btn-block" href="/media/config/<?=$media->label?>">
			<i class="fas fa-cog"></i> Config</a>

		<button class="btn btn-block <?=$media->publish? 'btn-success':'btn-info'?>" id="publish" data-published="<?=$media->publish?>">
			<i class="fas fa-toggle-<?=$media->publish?'on':'off'?>"></i> <span><?=$media->publish? 'Published':'Publish'?></span></button>


	</div>

<? } ?>

	<div class="col-lg-12 mt-5">

		<div id="formcontainer">
<form class="form-horizontal" role="form" id="metadata" action="#" method="post" enctype="multipart/form-data" 
      accept-charset="UTF-8">

  <input type="hidden" name="label" value="<?=$media->label?>"/>

  <div class="mb-1 row">
    <label for="title" class="col-sm-3">Title</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="title" name="title" placeholder="A title for the media es. The Pisa Griffin" 
      value="<?=$media->title?>" required="required">
    </div>
  </div>

  <div class="mb-1 row">
    <label for="description" class="col-sm-3">Description</label>
    <div class="col-sm-9">
      <textarea class="mb-0 form-control" id="description" name="description" rows="5" style="width:100%; padding:6px 12px 6px 12px" 
     placeholder="A short description of the media."><?=$media->description?></textarea>
    </div>
  </div>

  <div class="mb-1 row">
    <label for="owner" class="col-sm-3 control-label">Web resource</label>
    <div class="col-sm-9">
      <input type="url" class="form-control" id="url" name="url" placeholder="An url for a web page about the object."
		 value="<?=$media->url?>" >
    </div>
  </div>

  <div class="mb-1 row">
    <label for="owner" class="col-sm-3 control-label">Copyright owner</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="owner" name="owner" placeholder="The copyright owner of the file you are uploading." 
        value="<?=$media->owner?>" >
    </div>
  </div>

  <div class="mb-1 row">
    <label for="label" class="col-sm-3 control-label">Collection</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="collection" name="collection" placeholder="The collection the objects belongs to (optional)."
        value="<?=$media->collection?>">
    </div>
  </div>

<!--  <div class="mb-1 row">
    <label for="label" class="col-sm-3 control-label">Publish</label>
    <div class="col-sm-9">
      <p><input type="checkbox" name="publish" <? if($media->publish == 't') {?>checked="checked"<?}?> > Check this if you do want to make this media visible.</p>
    </div>
  </div> -->



  <div class="form-group row">
    <div class="col-sm-12">
      <button <?=$disabled?> id="submit-metadata" type="submit" class="btn btn-success">
		<i class="fas fa-check"></i> Update</button>
			
      <button <?=$disabled?> data-title="<?=$media->title?>" data-removemedia="<?=$media->label?>" style="float:right;" class="btn btn-danger">
		<i class="fas fa-trash"></i> Remove</button>
    </div>
  </div>


</form>
	</div> <!-- formcontrainer -->

	</div> <!-- col  -->


</div>
<? 
$tool_icons = array('Measure'=>'measure.png', 'Picking'=>'pick.png', 'Sections'=>'sections.png', 'Color on/off'=>'color_off.png');
?>

  <hr/>

<style>
td button.btn { padding:3px 9px; }
.table td { padding:6px; }
td.shrink {
	white-space: nowrap;
	width: 1px;
}

td.expand { text-overflow: ellipsis; }
</style>
	<div class="row mt-5">
		<div class="col-12">
			<h4>Files</h4>
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-12">
			<button <?=$disabled?> id="add-file" class="btn btn-primary">
				<i class="fas fa-upload"></i> Add files...</button>

			<button <?=$disabled?> id="process" class="btn btn-info" <? if($media->status != 'uploading' && $media->status != 'failed') { ?>disablesd<? } ?>>
				<i class="fas fa-cog"></i> Reprocess</button>
		</div>
	</div>

	<div class="row">
		<div class="col-12">

	<table id="filetable" class="table table-striped files">
	<thead>
		<tr>
			<th>format</th>
			<th>filename</th>
			<th>size</th>
<!--			<th></th> -->
			<th></th>

			<th><button id="removemultiple" class="btn btn-danger">
			<i class="fas fa-trash"></i></button></th>
		</tr>
	</thead>
	<tbody>
<? foreach($media->files as $f) { ?>
		<tr>

		<td class="shrink"><i style="font-size:32px" class="<?=$formats[$f->format]?>"></i></td>
		<td class="expand"><?=$f->filename?></td>    
		<td class="shrink"><?=human_filesize($f->size)?></td>
<!--		<td class="shrink"><button data-filename="<?=$f->filename?>" data-replacefile="<?=$f->id?>" class="btn btn-warning replacefile">
			<i class="fas fa-sync"></i> Replace</button></td> -->
		<td class="shrink"><button data-filename="<?=$f->filename?>" data-removefile="<?=$f->id?>" class="btn btn-danger removefile">
			<i class="fas fa-trash"></i></button></td>
		<td class="shrink"><input type="checkbox" name="removefile" value="<?=$f->id?>"></td>
		</tr>
<? } ?>

	</tbody>
	</table>
	</div>
</div>

<script>
//Might not work if the table is modified!
$(document).ready(function() {
	var $chkboxes = $('[name=removefile]');
	var lastChecked = null;

	$chkboxes.click(function(e) {
		if(!lastChecked) {
			lastChecked = this;
			return;
		}

		if(e.shiftKey) {
			var start = $chkboxes.index(this);
			var end = $chkboxes.index(lastChecked);

			$chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);

		}

		lastChecked = this;
	});
});
</script>


<form class="dropzone" id="fileform" style="display:none">
	<input type="hidden" name="media" value="aa" />
</form>



<table style="display:none;">
	<tr id="template">

	<td class="shrink"><i class=""></i></td>
	<td class="expand"><p data-dz-name></p><strong class="error text-danger" data-dz-errormessage></strong>
		<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
			<div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress>
		</div>
	</td>
	<td class="shrink" data-dz-size></td>
<!--	<td class="shrink"><button data-filename="" class="btn btn-warning">
		<i class="fas fa-sync"></i> Replace</button></td> -->
	<td class="shrink"><button data-filename="" data-removefile="" class="btn btn-danger">
		<i class="fas fa-trash"></i></button></td>
	</tr>
</table>


</div> <!-- formcontainer -->

<script src="/js/simplemde.min.js"></script>
<script>
var simplemde = new SimpleMDE({ element: $('#metadata textarea')[0], forceSync: true, hideIcons: ['side-by-side', 'fullscreen'], spellChecker: false, status: false });
simplemde.codemirror.on("change", function() { needsUpdate() });
</script>

<style>
#template { display: none; }
</style>
<script src="/js/dropzone.js"></script>
<script>

$('button[data-removemedia]').click(function(e) {
	var line = $(this).closest('tr');
	var media = $(this).attr('data-removemedia');
	var title = $(this).attr('data-title');
	var r = confirm("We are deleting the model '" + title + "'. Proceed?");
	if(!r) return;

	$.getJSON('/media/delete/' + media, function(d) {
		if(d.error) {
			alert(d.error);
			return;
		}
		window.location = '/profile';
	}).fail(function () { alert("Network error"); });
	e.preventDefault();
});

$('#metadata input, #metadata textarea').change(needsUpdate);

var updated = true;

function needsUpdate() {
	$('#submit-metadata').addClass('btn-primary');
	$('#submit-metadata').removeClass('btn-success');
	$('#submit-metadata i').addClass('fa-sync');
	$('#submit-metadata i').removeClass('fa-check');
	updated = false;
}

$('#metadata').submit(function(e) {

	var data = $(this).serializeArray();
	$.post('/media/update', data,
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
		'json').fail(
		function() { alert( "Oooopps, looks like there is a network, or server problem." ); });
			e.preventDefault();
	});


function removefiles(str) {
	$.getJSON('/media/delete/file/' + str, function(d) {
		if(d.error) {
			alert(d.error);
			return;
		}
	}).fail(function() { alert("Network or server problem."); });
	e.preventDefault();
}

$('#filetable').on('click', 'button[data-removefile]', function(e) {
	var line = $(this).closest('tr');
	var fileid = $(this).attr('data-removefile');
	var title = $(this).attr('data-filename');
	var r = confirm("We are deleting the file '" + title + "'. Proceed?");
	if(!r) return;

	$.getJSON('/media/delete/file/' + fileid, function(d) {
		if(d.error) {
			alert(d.error);
			return;
		}
		line.remove();

	}).fail(function() { alert("Network or server problem."); });
	e.preventDefault();
});

$('#removemultiple').click(function() {
	var inputs = [];
	$('input[name=removefile]:checked').each((i, j)=>{ inputs.push($(j).val()); });
	
	var r = confirm("We are deleting " + inputs.length + " files. Proceed?");
	if(!r) return;

	removefiles(inputs.join(','));
});


$('button[data-replacefile]').click(function() {
	alert('todo!');
	return;
	var line = $(this).closest('tr');
	var media = '<?=$media->label?>';

	if(!r) return;

	$.getJSON('/media/delete/' + media, function(d) {
		if(d.error) {
			alert(d.error);
			return;
		}
	}).fail(function() { alert("Network or server problem."); });
});


$('#process').click(function(e) {
	$(this).prop('disabled', true);
	$(this)[0].innerHTML = 'Processing...';
	$.getJSON('/media/process/<?=$media->label?>', function(d) {
		if(d.error) {
			alert(d.error);
			return;
		}
		location.reload();
	}).fail(function() { alert("Network or server problem."); });
});



function resizedataURL(datas, wantedWidth, wantedHeight) {

}


$('#publish').click(function(e) {

	var button = $(this);
	var published = (button.attr('data-published') == "1"); //already published

	function publish_done() {
		published = !published; //reflect cuirrent status no

		button.removeClass('btn-warning btn-info');
		button.addClass(published ?'btn-success':'btn-info');
		button.find('span')[0].innerHTML = (published? "Published": "Publish");
		button.attr('data-published', published? "1":"0");
	}


	if(published) {
		var formData = new FormData();
		formData.append("media", "<?=$media->label?>");

		var xhr = new XMLHttpRequest();
		xhr.open("POST", "/media/unpublish", true);
		xhr.send(formData);
		xhr.onload = publish_done;
		return;
	}

	var frame = document.querySelector("#thumb");
	var doc = frame.contentDocument || frame.contentWindow.document;
	var canvas = doc.querySelector('canvas');
	canvas.setAttribute('crossOrigin','anonymous');

	function b64ToUint8Array(b64Image) {
		var img = atob(b64Image.split(',')[1]);
		var img_buffer = [];
		var i = 0;
		while (i < img.length) {
			img_buffer.push(img.charCodeAt(i));
			i++;
		}
		return new Uint8Array(img_buffer);
	}

	var b64Image = canvas.toDataURL('image/png');
	console.log(b64Image);

	var img = document.createElement('img');

	var w = 300, h = 200;
	img.onload = function() {
		var canvas = document.createElement('canvas');
		var ctx = canvas.getContext('2d');
		canvas.width = w;
		canvas.height = h;

		var grd=ctx.createRadialGradient(w/2,h/2,0,w/2,h/2,w/2);
		grd.addColorStop(0,"#585454");
		grd.addColorStop(1,"#000000");

		ctx.fillStyle=grd;
//		ctx.fillStyle = '#707070';
		ctx.fillRect(0,0,w,h);
		ctx.drawImage(this, 0, 0, w, h);

		var dataURI = canvas.toDataURL('image/jpeg'); //b64 data

		var u8Image  = b64ToUint8Array(dataURI);

		var formData = new FormData();
		formData.append("image", new Blob([ u8Image ], {type: "image/jpeg"}));
		formData.append("media", "<?=$media->label?>");

		var xhr = new XMLHttpRequest();
		xhr.open("POST", "/media/publish", true);
		xhr.send(formData);
		xhr.onload = publish_done;

	};

		img.src = b64Image;
});




//DROPZONE!!!

Dropzone.options.myAwesomeDropzone = false;
Dropzone.autoDiscover = false;

var previewNode = document.querySelector("#template");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);

var dropzone = new Dropzone(document.body, { // Make the whole body a dropzone
/*  accept: function(file, done) {
	console.log(file);
	  done("Naha, you don't.");
	 // done(); to accept.
  }, */
	timeout: 1000*60*60*8,
	maxFiles: 100,
	maxFilesize: 2900, // 2Gb
	acceptedFiles: "<?=implode(',', array_map(function($a) { return '.'.$a; }, $allowed))?>", //.jpg,.png,.tif,.ply,.zip",
	url: "/media/upload/file",
	createImageThumbnails: false,
	parallelUploads: 4,
	previewTemplate: previewTemplate,
	autoQueue: true,               // Make sure the files aren't queued until manually added
	previewsContainer: ".files", 
	clickable: "#add-file",  // Define the element that should be used as click trigger to select files.

	addedfile: function(file) {
		var table = document.createElement("table");
		table.innerHTML = this.options.previewTemplate.trim();
		var tr = $(table).find('tr');
		tr.find('p[data-dz-name]')[0].innerHTML = file.name;
		tr.find('td[data-dz-size]')[0].innerHTML = file.size;
		tr.find('button').prop('disabled', true);
		$('.files').append(tr);
		file.previewElement = tr[0];
		$('#process').prop('disabled', true);
	},
});

dropzone.on("queuecomplete", function(progress) {
	$('#process').prop('disabled', false);
});

dropzone.on("error", function(a, error) {
	alert(error);
//	console.log(a, error);
});

dropzone.on("complete", function(file) {
	if(file.status == "error" || file.accepted == false) {
		$(file.previewElement).remove();
		return;
	}

	try {
		var response = JSON.parse(file.xhr.response);
	} catch(e) {
		alert("There was a problem uploading the file.");
		$(file.previewElement).remove();
		return;
	}
	if(response.error) {
		alert(response.error);
		$(file.previewElement).remove();
		return;
	}
	var button = $(file.previewElement).find('button[data-removefile]');
	button.attr('data-removefile', response.id);
	button.attr('data-filename',  file.name);
	button.prop('disabled', false);
});

dropzone.on("sending", function(file, xhr, formData) {
	formData.append("media", '<?=$media->label?>'); // Append all the additional input data of your form here!
	$("#total-progress").css('opacity', '1');
});


//dropzone.on("complete", function(file) {
//	$(file.previewElement).find('button').hide(); 
//});


<? if($media->status != 'ready') { ?>
function checkStatus() {
	$.get('/media/status/<?=$media->label?>', 
		function(e) {
			console.log(e);
			if(e.status && e.status != '<?=$media->status?>' && updated)
				window.location.reload();
			else
				setTimeout(checkStatus, 5000);
		}
	);
}

checkStatus();
<? } ?>

</script>





