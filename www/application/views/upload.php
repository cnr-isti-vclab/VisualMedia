<?	$title = array(
		'3d'    => 'Upload a 3d model.',
		'rti'   => 'Upload a relightable image (rti, ptm)',
		'img'   => 'Upload a large image',
		'album' => 'Upload a set of images'
	);
?>
<div class="row">
	<div class="col-12">
		<h3><?=$title[$media_type]?></h3>
	</div>
</div>

<div id="formcontainer" class="mt-4">

<form class="form-horizontal" role="form" id="form" action="#" method="post" enctype="multipart/form-data" accept-charset="UTF-8">

  <input type="hidden" name="userid" value="<?=$user->id?>"/>
  <input type="hidden" name="media_type" value="<?=$media_type?>"/>
  <input type="hidden" name="workspace" value=""/>


<!-- <h3  style="margin-bottom:20px">Information about the digital object</h3> -->

	<div class="form-group row">
		<label for="title" class="col-sm-2 control-label">Title</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="title" name="title" placeholder="A title for the media es. The Pisa Griffin" 
			   value="" required="required">
		</div>
	</div>

  <div class="form-group row">
	<label for="description" class="col-sm-2 control-label">Description</label>
	<div class="col-sm-10">
	  <textarea id="description" name="description" rows="3" style="width:100%; padding:6px 12px 6px 12px" 
	 placeholder="A short description of the media."></textarea>
	</div>
  </div>


  <div class="form-group row">
	<label for="url" class="col-sm-2 control-label">Web resource</label>
	<div class="col-sm-10">
	  <input type="url" class="form-control" id="url" name="url" placeholder="An url for a web page about the object.">
	</div>
  </div>


  <div class="form-group row">
	<label for="label" class="col-sm-2 control-label">Collection/Dataset</label>
	<div class="col-sm-10">
	  <input type="text" class="form-control" id="collection" name="collection" placeholder="The collection the objects belongs to (optional).">
	</div>
  </div>

  <div class="form-group row">
	<label for="owner" class="col-sm-2 control-label">Copyright owner</label>
	<div class="col-sm-10">
	  <input type="text" class="form-control" id="owner" name="owner" placeholder="The copyright owner of the file you are uploading." 
		value="" >
	</div>
  </div>

  <div class="form-group row">
	<div class="offset-sm-2 col-sm-10">
	  <label><input type="checkbox" name="usage" required="required"> I have read and accepted the <a href="/terms" target="new">usage terms</a>.</label>
	</div>
  </div>





<hr/>

  <h3>File selection</h3>

<div class="row">
	<div class="col-sm-10">



<p>Supported formats:
<? switch($media_type) { 
	case '3d'   : echo('a .ply .obj .nxz file and eventually the texture images'); break;
	case 'img'  : echo('a single large image'); break;
	case 'album': echo('a set of images (default ordering taken from file names)'); break;
	case 'rti'  : echo('a .ptm or .rti file, or the contents of the directory created by <a href="http://vcg.isti/cnr.it/relight">relight</a> library');
//			echo('you might also upload the original photos and the .lp  light direction files (see docs).');
			break;
 } ?></p>

<p>The file types accepted area: <?=implode(', ', array_map(function($a) { return '.'.$a; }, $allowed))?>, please check the <a href="/info/<?=$media_type?>">requirements, supported media for details.</a>

	</div>
</div>

<div class="row">
		<div class="col">
			<input type="file" id="fileInput" multiple />
		</div>
		<ul id="fileList"></ul>

	</div> 

	<div class="form-group row">
	<div class="offset-sm-10 col-sm-2">
	  <input type="submit" class="form-control btn btn-success" id="submit" value="Create media"/>
	</div>
  </div>
	</div> <!-- formcontainer -->

</form>
</div>



<script src="/js/simplemde.min.js"></script>
<script>
var simplemde = new SimpleMDE({ element: $('textarea')[0], forceSync: true, hideIcons: ['side-by-side', 'fullscreen'], spellChecker: false, status: false });
</script>

<script src="https://cdn.jsdelivr.net/npm/tus-js-client@latest/dist/tus.js"></script>
<script>

const fileInput = document.getElementById("fileInput");
const createBtn = document.getElementById("submit");
const fileList = document.getElementById("fileList");

let selectedFiles = [];

fileInput.addEventListener("change", () => {
  selectedFiles = Array.from(fileInput.files);
  fileList.innerHTML = "";

  selectedFiles.forEach(file => {
	const li = document.createElement("li");
	li.textContent = file.name;
	fileList.appendChild(li);
  });
});

createBtn.addEventListener("click", async (e) => {
	e.preventDefault();

	let form = document.getElementById('form');
	if(!form.checkValidity()) {
		form.reportValidity();
		return;
	}
	if(selectedFiles.length == 0) {
		alert("Select at least a file");
		return;
	}

	const formData = new FormData(form);
	selectedFiles.forEach((file, i) => { formData.append(`files[]`, file.name); });
	const params = new URLSearchParams(formData);

	const res = await fetch('/media/create', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body: params.toString()
	});

	const result = await res.json();
	if(result.error) {
		alert(result.error);
		return;
	}
	const mediaLabel = result.label;
	const modelId = result.model_id;
	
	selectedFiles.forEach(file => {
		const upload = new tus.Upload(file, {
		endpoint: "/media/upload/file",
		metadata: {
			filename: file.name,
			filetype: file.type,
			media: mediaLabel,
			model: modelId
		},
		onError: error => {
			console.error("Upload failed:", error);
		},
		onProgress: (bytesUploaded, bytesTotal) => {
			console.log(`${file.name}: ${(bytesUploaded / bytesTotal * 100).toFixed(1)}%`);
		},
		onSuccess: () => {
			console.log(`Finished uploading ${file.name}`);
		}
	});

	upload.start();
  });
});
</script>

<!--<script src="/js/dropzone.js"></script> -->
<script>

//Dropzone.options.myAwesomeDropzone = false;
var media_id = null;


/*


var previewNode = document.querySelector("#template");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);


var dropzone = new Dropzone(document.body, { // Make the whole body a dropzone

	timeout: 1000*60*60*8,
	maxFiles: 100,
	maxFilesize: 4000, // 4Gb
	acceptedFiles: "<?=implode(',', array_map(function($a) { return '.'.$a; }, $allowed))?>", 
	url: "/media/upload/file",
	createImageThumbnails: false,
	parallelUploads: 4,
	previewTemplate: previewTemplate,
	autoQueue: false,               // Make sure the files aren't queued until manually added
	previewsContainer: "#previews", 
	clickable: ".fileinput-button"  // Define the element that should be used as click trigger to select files.
});

dropzone.on("totaluploadprogress", function(progress) {
	$("#total-progress .progress-bar").css('width', progress + "%");
});

dropzone.on("sending", function(file, xhr, formData) {
	formData.append("media", media); // Append all the additional input data of your form here!
	$("#total-progress").css('opacity', '1');
});

dropzone.on("complete", function(file) {
	$(file.previewElement).find('button').hide(); 
});

// Hide the total progress bar when nothing's uploading anymore
dropzone.on("queuecomplete", function(progress) {
	if(!media) //a failed file
		return;
	$("#total-progress").css('opacity', '0');
	//start processing and redirect to page of object
	$.getJSON('/media/process/' + media, function(d) {
		if(d.error) {
			alert(d.error);
			return;
		}
		window.location = '/media/' + media;
	}).fail(function() { alert("Network or server problem."); });
	
});

// Setup the buttons for all transfers
// The "add files" button doesn't need to be setup because the config
// `clickable` has already been specified.
$("#actions .start").click(function(e) {

	var form = $('#form')[0]
	if(!form.checkValidity()) {
		form.reportValidity();
		e.preventDefault();
		return;
	}

	//submit form and if successful enqueue files!

	 $.ajax({
		url: '/media/create',
		cache: false,
		contentType: 'application/x-www-form-urlencoded',
		type: 'POST',
		data : $('#form').serialize(),
		success: function(data) {
			if(data.error) {
				alert(data.error);
				return;
			}
			media = data.label;
			dropzone.enqueueFiles(dropzone.getFilesWithStatus(Dropzone.ADDED));
			//disable start upload button!
		},
		error:function(data) {
			alert("Sorry, there was a problem. Contact us.");
		},
		failure: function(data){
			alert("Sorry, there was a problem. Contact us.");
		}
	});
	//check if there are some files
});

document.querySelector("#actions .cancel").onclick = function() {
  dropzone.removeAllFiles(true);
};

*/




<? if(0 && $user && $user->provider == "d4science") { ?>

var currentPath = "/Workspace";

function fillFileTable() {
	var mime = { 
		jpg:  ['picture', 'Picture'],
		jpeg: ['picture', 'Picture'],
		png:  ['picture', 'Picture'],
		tiff: ['picture', 'Picture'],
		tif:  ['picture', 'Picture'],
		ply:  ['file', '3D model'],
		obj:  ['file', '3D model'],
		mtl:  ['file', '3D model'],
		nxs:  ['file', '3D model'],
		nxz:  ['file', '3D model'],
		ptm:  ['lightbulb', 'RTI'],
		zip:  ['compressed', 'Zip'] 
	};
	$.ajax({
		data: { path: currentPath },
//https://workspace-repository.d4science.org/home-library-webapp/rest/List?absPath=/Home/<?=$user->username?>/Workspace",
		url: "/user/listFiles",
		type: "POST",
		success: function(data) {  //xml with files
			console.log("success:", currentPath);
			var str = '';
			if(currentPath != '/Workspace')
				str += '<tr folder=".."><td></td><td>..</td><td></td>';
			var dir = $($.parseXML(data));
			dir.find('entry').each(function(index, elem) {
				var name = $(elem).find('string').text();
				var isdir = $(elem).find('boolean').text() == 'true';
				var ext = name.split('.').pop().toLowerCase();
				if(isdir) {
					var icon = 'folder-open';
					var type = 'Folder';
					var path = 'folder="' + name + '"';
				} else {
					var icon = mime[ext]? mime[ext][0] : 'file';
					var type = mime[ext]? mime[ext][1]:'Unknown';
					var path = 'file="' + name + '"';
				}
				str += '<tr ' + path + ' class="clickable-row"><td><i class="glyphicon glyphicon-' + icon + '"></i></td><td>' + name + '</td><td>'+type+'</td></tr>\n';
			});
			$('#filetable tbody').html(str);
		}
	});
}

fillFileTable();

$('#filetable').on('click', 'tr', function() {
	var tr = $(this);
	var folder = tr.attr('folder');
	var file = tr.attr('file');
	if(folder == "..") {
		currentPath = currentPath.split('/')
		currentPath.pop();
		currentPath = currentPath.join('/');
		fillFileTable();
		return;
	}
	if(folder) {
		currentPath += "/" + folder;
		fillFileTable();
		return;
	}
	tr.addClass('active').siblings().removeClass('active');
});

$('#workspace-upload').click(function() {
	var selected = $('#filetable tr.active');
	if(selected.length == 0)
		return;

	var file = selected.attr('file');
	var ext = file.split('.').pop().toLowerCase();
	if(!['jpg', 'jpeg', 'png', 'tiff', 'tif', 'ply', 'obj', 'mtl', 'nxs', 'nxz', 'rti', 'ptm'].includes(ext)) {
		alert("This file extension is not supported.\n");
		return;
	}
	//fill input and submit query.
	var workspace = $('input[name=workspace]');
	workspace.attr('value', currentPath + '/' + file);

	var data = $('#form').serializeArray();
	var url = '/upload/job1';

	$.ajax({
		url: '/upload/job',
		type: "POST",
		data: $('#form').serializeArray(),
		success: function(a) {
			if(!a || !a.files || !a.files.length) {
				alert("Drat! Something went wrong. Please contact ponchio@isti.cnr.it");
				return;
			}
			if(a.files[0].error) {
				alert(a.files[0].error);
				return;
			}

			return;
			alert("Success! Your job is being processed...");
			$('#label').val('');
			$('#title').val('');
			$('#description').val('');
			//close modal
			$('#filexplorer').modal('toggle');
		},
		error: function() {
			alert("Drat! Something went wrong. Please contact ponchio@isti.cnr.it");
			$('#filexplorer').modal('toggle');
			return;
		}
	});
});

<? } ?>

/*$('#label').on('input', function() {
	var input = $(this);
	var label = input.val();
	if(!/^[a-zA-Z0-9 _]*$/.test(label)) {
		input[0].setCustomValidity("Label '" + label + "' contains non allowed characters, pleas use only a-z_ characters");
		return;
	}
	label = label.toLowerCase();
	label = label.replace(' ', '_');
	$.getJSON('/index.php/upload/testLabel/'+label, function(a) {
	   if(a.exists) {
		 input[0].setCustomValidity("Label '" + label + "' already in use.");
		 input[0].form.reportValidity();
	   } else
		 input[0].setCustomValidity("");
	}).fail(function(a) { var error = a.error(); console.log(error); });
});*/

$('#form').submit(function(e) {
	e.preventDefault();
	return false;
});

</script>
