<style>
a.jumbo-link:link, a.jumbo-link:visited { color: red; }
.jumbotron p { margin-bottom: 0.5em }
</style>
<div class="jumbotron d-flex flex-column justify-content-end" style="background-image:url(/images/tutan.jpg); background-size:cover; position:relative; min-height:300px; padding:0px;">
	<div style="position:absolute; bottom:0px; left:0px; width:100%; height:60%; background: linear-gradient(to bottom, rgba(0,0,0,0) 0%,rgba(0,0,0,0) 1%,rgba(0,0,0,0.2) 100%); "></div>
	<div style="padding:0px 20px; color:white; position:absolute; width:100%;" class="justify-content-between d-flex align-items-end">
		<div style="width:50%">
			<h1>Visual Media Service</h1>
			<p>Create your online showcase for 3D models, images and RTI.</p>
			<p>Powered by <a class="jumbo-link" href="http://3dhop.net">3DHOP</a> and <a class="jumbo-link" href="http://vcg.isti.cnr.it/relight">Relight</a>.</p>
		</div>
		<div class="">
			<p><a class="btn btn-lg btn-info" href="/upload" role="button">Upload &raquo;</a></p>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<p>The Visual Media Service provides easy publication and presentation on the web of complex visual media assets. It is an automatic service that allows to upload visual media files on an server and to transform them into an efficient web format, making them ready for web-based visualization.</p>
	</div>
</div>

<?=$browsertable?>

<div class="row mt-4">
	<div class="col">
		<a href="/browse" class="btn btn-secondary">Browse the whole repository...</a>
	</div>
</div>
