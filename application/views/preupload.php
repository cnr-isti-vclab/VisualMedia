<h2>Upload Media</h2>

<? if(0 && !$user) { ?>
<div class="row">
	<div class="col-12">
<p>In order to upload somethting, you need to login.</p>

<p>You can enter your email and you will receive a link to login (and no need for a password),</p>

<p>or</p>

<p>use the social login (4dscience, google, etc).</p>
	</div>
</div>
<? } ?>

<div class="row">
	<div class="mt-4 col-lg-6">
		<h3><img src="/images/3d32.png"> 3D model</h3>
		<p>3D representations produced with 3D scanners or photogrammetry are extremely high-resolution and hard to visualize at interactive rate. This service produces a web page that supports interactive visualization of your data, after converting it into an efficient multiresolution encoding.</p>
		<a href="/upload/3d" class="upload btn btn-info"><i class="fas fa-upload"></i> Upload</a>
		<a href="/info/3d" class="btn btn-secondary float-right"><i class="fas fa-question"></i> Help</a>

	</div>

	<div class="mt-4 col-lg-6">
		<h3><img src="/images/rti32.png"> Relightable images</h3>
		<p>Relightable images (called Reflection Transformation Images, RTI, or Polynomial Texture Maps, PTM) are becoming an increasingly used media. This service closes a current gap, giving support for easy publication on the web and interactive visualization of RTI images.</p>
		<a href="/upload/rti" class="upload btn btn-info"><i class="fas fa-upload"></i> Upload</a>
		<a href="/info/rti" class="btn btn-secondary float-right"><i class="fas fa-question"></i> Help</a>

	</div>

	<div class="mt-4 col-lg-6">
		<h3><img src="/images/img32.png"> Large images</h3>
		<p>High-resolution images are a commodity resource in archaeology. Unfortunately, they are most often disseminated and published on the web by using low-resolution versions (a single 40Mpixel images is 120MB in uncompressed format and around 10MB when lossy compressed).</p>
		<a href="/upload/img" class="upload btn btn-info"><i class="fas fa-upload"></i> Upload</a>
		<a href="/info/img" class="btn btn-secondary float-right"><i class="fas fa-question"></i> Help</a>

	</div>


	<div class="mt-4 col-lg-6">
		<h3><img src="/images/album32.png"> Image set</h3>
		<p>Users may have a set of images to publish, where all of them are related to the same artwork or specimen. In this case, instead of uploading them one by one, we offer the "Image Set" data type, which allows: to specify metadata only once, for the entire set; to use a specific interface at visualization time.</p>
		<a href="/upload/album" class="upload btn btn-info"><i class="fas fa-upload"></i> Upload</a>
		<a href="/info/album" class="btn btn-secondary float-right"><i class="fas fa-question"></i> Help</a>

	</div>
</div>

<? if(!$user) { ?>
<script>
$('.upload').click(function(e) {
	$('#login').modal('toggle');
	e.preventDefault();
});
</script>
<? } ?>


