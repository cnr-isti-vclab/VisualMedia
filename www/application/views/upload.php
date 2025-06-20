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



<!-- File list 
	<div class="row" class="files" id="previews">

		<div id="template" class="col-12 mt-4">
			<div class="d-flex justify-content-between align-items-end">
				<div>
					<p class="name mb-1" data-dz-name></p>
					<strong class="error text-danger" data-dz-errormessage></strong>
					<p class="size mb-1" data-dz-size></p>
				</div>

				<div class="mb-1">
					<button data-dz-remove class="btn btn-warning cancel">
						<i class="glyphicon glyphicon-ban-circle"></i>
						<span>Cancel</span>
					</button>
					<button data-dz-remove class="btn btn-danger delete">
						<i class="glyphicon glyphicon-trash"></i>
						<span>Delete</span>
					</button>
				</div>
			</div>

			<div>
				<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
					<div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress>
					</div>
				</div>
			</div>
		</div>

	</div> row -->


</div>


<script src="/js/simplemde.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tus-js-client@latest/dist/tus.js"></script>
<script>
var simplemde = new SimpleMDE({ element: $('textarea')[0], forceSync: true, hideIcons: ['side-by-side', 'fullscreen'], spellChecker: false, status: false });

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
	
	selectedFiles.forEach(file => {
		const upload = new tus.Upload(file, {
		endpoint: "/media/upload/file",
		metadata: {
			filename: file.name,
			filetype: file.type,
			media: mediaLabel
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

$('#form').submit(function(e) {
	e.preventDefault();
	return false;
});

</script>
