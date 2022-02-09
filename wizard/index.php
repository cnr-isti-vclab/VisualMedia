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
	padding-top:10px;
	margin-bottom:4px;
}
.wizard-progress li {
	list-style-type: none;
	display:list-item;
	width:200px;
}
.wizard-progress li a {
	display:flex;
	flex-direction:column;
	align-items:center;
	text-decoration:none;
	color:#212529;
}

.wizard-progress li.active p {
	color:#0d5e6b;
}

.wizard-progress li .dot {
	position:relative;
	background-color:#6c757d;
	width:25px;
	height:25px;
	border-radius:50%
}
.wizard-progress li.active .dot {
	background-color:#17a2b8;
}

.wizard-progress li hr {
	position:relative;
	top:13px;
	margin:0px;
	padding:0px;
	width:200px;

	border-top: 2px solid #6c757d;
	border-color: #6c757d;
	background-color: #6c757d;
	color: #6c757d; 
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



.switch { float:right; }


</style>
<script>
//needed before initializing the various panels.
	Config.url = "update.php";
	Config.frame = "#media";
</script>
</head>

<body>
<div class="wizard">
	<ul class="container wizard-progress">
		<li class="active">
			<hr style="width:50%; position:relative; left:50%"/>
			<a href="#alignment">
				<div class="dot"></div>
				<p>Alignment</p>
			</a>
		</li>
		<li>
			<hr/>
			<a href="#material">
				<div class="dot"></div>
				<p>Material &amp; Light</p>
			</a>
		</li>
		<li>
			<hr/>
			<a href="#navigation">
				<div class="dot"></div>
				<p>Navigation</p>
			</a>
		</li>
		<li>
			<hr/>
			<a href="#interface">
				<div class="dot"></div>
				<p>Interface</p>
			</a>
		</li>
		<li>
			<hr style="width:50%"/>
			<a href="#annotations">
				<div class="dot"></div>
				<p>Annotations</p>
			</a>
		</li>
	</ul>
	<div class="container-fluid" style="padding:0px; margin:0px; display:flex; flex:2;">
		<iframe id="media" allowfullscreen allow="fullscreen" style="border-width:0px" class="vms" src="3d.php"></iframe>
		<div class="panel" style="display:flex; justify-content:space-between; flex-direction:column;">
			<div id="alignment" class="active"><?php include('alignment.php'); ?></div>
			<div id="material" class=""><?php include('material.php'); ?></div>
			<div id="navigation" class=""><?php include('navigation.php'); ?></div>
			<div id="interface" class=""><?php include('look.php'); ?></div>
			<div id="annotations" class=""><?php include('annotations.php'); ?></div>

			<div class="active" style="padding:15px">
				<button class="btn btn-secondary btn-sm btn-block" name="reset_all"> Reset everything </button>
			</div>
		</div>
	</div>
</div>
</body>

<script>
wizardStep(window.location.hash);

function wizardStep(step) {
	if(!step)
		step = '#alignment';
	for(let li of document.querySelectorAll('.wizard-progress li'))
		li.classList.remove('active');
	let li = document.querySelector(`a[href='${step}']`).parentElement;
	li.classList.add('active');
	step = step.substring(1); //remove #
	for( let div of document.querySelectorAll('#alignment, #material, #navigation, #interface, #annotations'))
		div.classList.toggle('active', div.id == step);
}

window.addEventListener('hashchange', function(e) {
	let newHash = (new URL(e.newURL)).hash;
	wizardStep(newHash);
	e.preventDefault();
});

let reset_all = document.querySelector('button[name=reset_all]');
reset_all.addEventListener('click', () => {
	let doReset = confirm("Everything, alignment, material, navigation etc. will be reset to defaults. Are you sure?");
	if(doReset)
		Config.reset()
});
</script>
