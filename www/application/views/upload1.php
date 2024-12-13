<!doctype html>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="/js/dropzone.js"></script>
</head>
<body>


<div class="container">

	<div class="row">
		<div class="col">
			<form action="/upload/job" class="dropzone" style="display:none">
				<input type="hidden" name="additionaldata" value="1" />
			</form>
		</div>
	</div> <!-- row -->

	<div id="actions" class="row">
		<div class="col-lg-7">
			<span class="btn btn-success fileinput-button">
				<i class="glyphicon glyphicon-plus"></i>
				<span>Add files...</span>
			</span>
			<button type="submit" class="btn btn-primary start">
				<i class="glyphicon glyphicon-upload"></i>
				<span>Start upload</span>
			</button>
			<button type="reset" class="btn btn-warning cancel">
				<i class="glyphicon glyphicon-ban-circle"></i>
				<span>Cancel upload</span>
			</button>
		</div>

		<div class="col-lg-5">
			<!-- The global file processing state -->
			<span class="fileupload-process">
				<div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
					<div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
				</div>
			</span>
		</div>
	</div>



<!-- HTML heavily inspired by http://blueimp.github.io/jQuery-File-Upload/ -->
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

	</div> <!-- row -->

</div> <!-- container -->

</body>
<script>
Dropzone.options.myAwesomeDropzone = false;

var previewNode = document.querySelector("#template");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);

var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
/*  accept: function(file, done) {
	console.log(file);
	  done("Naha, you don't.");
	 // done(); to accept.
  }, */
	maxFiles: 100,
	maxFileSize: 2000000000, // 2Gb
	acceptedFiles: "<?=$allowed)?>",
	url: "/upload/job",
	createImageThumbnails: false,
	parallelUploads: 4,
	previewTemplate: previewTemplate,
	autoQueue: false,               // Make sure the files aren't queued until manually added
	previewsContainer: "#previews", 
	clickable: ".fileinput-button"  // Define the element that should be used as click trigger to select files.
});


// Update the total progress bar
myDropzone.on("totaluploadprogress", function(progress) {
  document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
});

myDropzone.on("sending", function(file) {
  // Show the total progress bar when upload starts
  document.querySelector("#total-progress").style.opacity = "1";
  // And disable the start button
//  file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
});

// Hide the total progress bar when nothing's uploading anymore
myDropzone.on("queuecomplete", function(progress) {
  document.querySelector("#total-progress").style.opacity = "0";
});

// Setup the buttons for all transfers
// The "add files" button doesn't need to be setup because the config
// `clickable` has already been specified.
document.querySelector("#actions .start").onclick = function() {

$('#form').find(':file').click(function(e) {
  var form = $(this).closest('form');
  form.find(':submit').click();
  if(!form[0].checkValidity())
    e.preventDefault();
});

  myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
};
document.querySelector("#actions .cancel").onclick = function() {
  myDropzone.removeAllFiles(true);
};

//var myDropzone = new Dropzone("#maghi", { url: "/file/post", createImageThumbnails: false});
//maxFiles
//maxFilesize
//acceptedFiles: ".jpg,.png,.ply,"
//addRemoveLinks: true
//autoProcessQueue: true
</script>
</html>
