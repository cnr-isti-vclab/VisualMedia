<div class="row">
<div class="col">
<h3>About</h3>

<p>The Visual Media Service provides easy publication and presentation on the web of complex visual media assets. It is an automatic service that allows to upload visual media files on an  server and to transform them into an efficient web format, making them ready for web-based visualization.</p>


<p>The Visual Media Service was born as part of <a href="https://ariadne-infrastructure.eu/">FP7 EU-INFRA Ariadne project</a>, aimed at providing an integrated data infrastructure for archaeological research. The work has been then extended in the framework of the <a href="http://www.parthenos-project.eu/">H2020 PARTHENOS project</a>  (technological update of the system to new data encoding and new interactive visualization algorithms)  and <a href="https://eoscpilot.eu">H2020 EOSC Pilot</a>  (incorporation of user authentication, enabling integration with D4Science VRE, extensive user tests).
</p>

<p>This service was developed by:  <a href="http://vcg.isti.cnr.it">Visual Computing Lab - ISTI - CNR</a></p>

<p>We gratefully acknowledge partial funding from the <a href="https://ariadne-infrastructure.eu/" target="_blank" rel="noopener">ARIADNE</a> and <a href="https://www.h2iosc.cnr.it/" target="_blank" rel="noopener">H2IOSC</a> projects, whose support has been instrumental in expanding and maintaining the Visual Media Service.</p>

<p>Note: the Visual Media Server is NOT to be intended as an archive or a repository. It is an instrument to allow scholars/professionals to publish on the web large visual data and to share them with colleagues, to support cooperative work. We do provide a browsing feature, but only with the scope of showing what users have submitted so far (consider that we had more than 400 single submissions, but most of them are not "public" due to confidentiality reasons requested by the owners, e.g. because those data are part of unpublished current research).
<br/>
As an example of an archival service which provides features for presentation of 3D data, please check the <a href="http://archaeologydataservice.ac.uk/archives/view/amarna_leap_2011/downloads.cfm?obj=yes&obj_id=38819&CFID=50014&CFTOKEN=33B9F7CC-99E6-479F-BF16E64832956B80">ADS archive</a>, that is also based on 3DHOP.
</p>

<h3>Contacts</h3>

<div style="float: right; margin: -35px 0 10px 20px; text-align: right;">
	<img src="/images/co-funded-h2020-horiz_en.svg" alt="European Union" style="max-width: 160px; width: 100%; height: auto; display: inline-block;"/>
	<img src="/images/ariadne_logo_0.png" alt="ARIADNE" style="max-width: 120px; width: 100%; height: auto; display: inline-block; margin-left: 12px;"/>
	<img src="/images/h2iosc_logo.png" alt="H2IOSC" style="max-width: 140px; width: 100%; height: auto; display: inline-block; margin-left: 12px;"/>
</div>
<? 		$contact = ADMIN_EMAIL; ?>
<p>Please send comments or queries to:   <a href="mailto:<?=$contact?>"><?=$contact?></a></p>



<hr/>
<h3>Changelog</h3>
<h5 class="text-info">September 2025.</h5>
<ul>
	<li>Integrated H2IOSC single sign-on, extending the authentication options for institutional users.</li>
	<li>Hardened the editing workflow with better audit trails and safer versioning of published assets.</li>
</ul>

<h5 class="text-info">July 2025.</h5>
<ul>
	<li>Streamlined 3D model editing tools and fixed regressions in the viewer configuration panels.</li>
	<li>Introduced mesh-processing utilities in the Python pipeline to support automated cleanup steps.</li>
	<li>Surfaced viewer version information directly in the UI to simplify troubleshooting.</li>
</ul>

<h5 class="text-info">June 2025.</h5>
<ul>
	<li>Swapped the legacy upload handler with the TUS resumable protocol for large file reliability.</li>
	<li>Upgraded the Docker Python image with Nexus and PyMeshLab while standardising on linux/amd64 builds.</li>
	<li>Improved developer ergonomics by fixing path constants and supplying default symlinks.</li>
</ul>

<h5 class="text-info">April 2025.</h5>
<ul>
	<li>Added DeepZoom generation and RTI relight tooling to the processing stack.</li>
	<li>Refactored environment configuration, splitting secrets and aligning credentials with production best practices.</li>
	<li>Documented the REST API with an initial OpenAPI draft and refreshed deployment guides.</li>
</ul>

<h5 class="text-info">November 2024</h5>
<ul>
	<li>Completed Dockerization</li>
</ul>

<h5 class="text-info">November 2018.</h5>
<ul>
	<li>Added rotation for 2D media</li>
	<li>Added RTI normals visualization</li>
</ul>

<h5 class="text-info">September 2018, major refit.</h5>
<ul>
	<li>Login: passwordless, google, d4science
	<li>Multiple files upload and management
	<li>Processing errors display
	<li>Relight RTI viewer
	<li>Online view configuration.
	<li>Image set
	<li>Thumbnails
</ul>

<h5 class="text-info">Dicember 2017, updated and maintenance</h5>
<ul>
	<li>Added "info" button to display additional data of the models and color on/off
	<li>New version of 3DHOP and Nexus (improved performances expecially for textured models)
	<li>Https supported (allows iframes for secure websites)
	<li>Decent mobile media visualization
	<li>Bugfixes (mostly in uploading)
</ul>

<h5 class="text-info">October 2016, tiff bugfix!</h5>
<ul>
	<li>Tiff (especially lidar) image processing was plagued by tifflib support, and has been fixed.
	<li>In case some other problem resurfaces, please write me, thank you.
</ul>

<h5 class="text-info">February 2016, new release!</h5>
<ul>
	<li>We added new functionalities, check the help pages for details.
		<ul>
			<li>3d model compression</li>
			<li>point clouds</li>
			<li>textured models</li>
			<li>presentation customization</li>
		</ul>
</ul>
</div>
</div>
