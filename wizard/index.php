<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!--BOOTSTRAP STYLE-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
<!--STYLE-->
<link rel="stylesheet" href="stylesheet/style.css">
<script type="text/javascript" src="js/spidergl.js"></script>
<script src="config.js"></script>

<style>
.wizard-progress {
	display:flex;
	justify-content:center;
}
.wizard-progress li {
	list-style-type: none;
	display:list-item;
	width:200px;
}
.panel > div {
	display: none;
}
.panel .active {
	display: block;
}
.wizard {
	height:100%;
	display:flex;
	flex-direction: column;
}

</style>
</head>

<body>
<div class="wizard">
	<ul class="container wizard-progress">
		<li class="active">
			<a href="#alignment">Alignment</a>
		</li>
		<li>
			<a href="#material">Material &amp; light</a>
		</li>
		<li>
			<a href="#navigation">Navigation</a>
		</li>
		<li>
			<a href="#interface">Interface</a>
		</li>
	</ul>
	<div class="container-fluid" style="padding:0px; margin:0px; display:flex; flex:2;">
		<iframe id="media" allowfullscreen allow="fullscreen" style="border-width:0px" class="vms" src="3d.php"></iframe>
		<div class="panel" style="max-height:100%">
			<div id="alignment" class="active"><? include('alignment.php'); ?></div>
			<div id="material" class=""><? include('material.php'); ?></div>
			<div id="navigation" class=""><? include('navigation.php'); ?></div>
			<div id="interface" class=""><? include('look.php'); ?></div>
		</div>
	</div>
</div>
</body>


<script>
wizardStep(window.location.hash);
function wizardStep(step) {
	step = step.substring(1); //remove #
	for( let div of document.querySelectorAll('.panel > div'))
		div.classList.toggle('active', div.id == step);
}

window.addEventListener('hashchange', function(e) {
	let newHash = (new URL(e.newURL)).hash;
	wizardStep(newHash);	
	e.preventDefault();
})
</script>
