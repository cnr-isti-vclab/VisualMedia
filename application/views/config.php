<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/fontawesome-all.min.css">
<style>
html, body, .relight { width:100%; height:100%; padding:0px; margin:0px; }
.container1 { width:100%; height:100%; padding:0px; margin:0px; display:flex; }
.panel { width: 400px; background-color:#555; padding:10px; color:#bbb; height:100%; overflow:auto;}
.relight { }
p { margin-bottom:3px; }
hr { margin: 6px 0px; }


input[type=color] {
    border-radius: 8px;
    height: 24px;
    width: 24px;
    border: none;
    outline: none;
    -webkit-appearance: none;
}

input[type=color]::-webkit-color-swatch-wrapper {
    padding: 0;	
}
input[type=color]::-webkit-color-swatch {
    border: none;
    border-radius: 7px;
}

</style>

</head>

<?
$skin = $options->skin;
$back = $options->background;
#if($media->media_type == '3d')
#	$trackball = $options['trackball'];

$color0 = '#aaaaaa';
$color1 = '#000000';
#if(isset($back->type) && $back->type != 'image') {
#	$colors = $back['value'];

#if($media->media_type == '3d')
#	$space = $options['space'];

if(!isset($back->type))
	$back->type = 'flat';
?>
<body>
	<div class="container1">
		<iframe id="media" allowfullscreen allow="fullscreen" style="border-width:0px" class="relight" src="/<?=$media->media_type?>/<?=$media->label?>?config"></iframe>
		<div class="panel">
			<h4>Configure <a title="Back" class="btn btn-secondary float-right" href="/media/<?=$media->label?>"><i class="fas fa-arrow-left"></i></a></h4>
			<p>Skin:</p>
		<? foreach(array('dark', 'light', 'minimal_dark', 'minimal_light', 'transparent_dark', 'transparent_light') as $s) {?>
			<img width="32" class="skins" data-skin="<?=$s?>" src="/skins/<?=$s?>/home.png">
		<? } ?>
		<hr/>
<!-- part common to all files -->
			<p>Background</p>
			Colors: <input type="color" name="background-0" value="<?=$back->color0?>"> 
				    <input type="color" name="background-1" value="<?=$back->color1?>"> <br/>
			<input type="radio" name="background" value="flat"   <?=$back->type == 'flat'? 'checked':''?> >
				Flat color <br/>

			<input type="radio" name="background" value="linear" <?=$back->type == 'linear'? 'checked':''?>>
				Linear gradient <br/>

			<input type="radio" name="background" value="radial" <?=$back->type == 'radial'? 'checked':''?>>
				Radial gradient <br/>

			<div class="form-group form-inline justify-content-between">
				<div><input type="radio" name="background" value="image"  <?=$back->type == 'image'? 'checked':''?>>
				Image</div>
				<select class="form-control  form-control-sm" name="image">
					<option value="light.jpg" <?=$back->color0 == 'light.jpg'? 'selected':''?>>Light</option>
					<option value="dark.jpg" <?=$back->color1 == 'dark.jpg'? 'selected':''?>>Dark</option>
				</select>
			</div>

<? if($media->media_type != '3d') { ?>
		<hr/>
			<p>Width <small>(Size of a pixel in mm: <?=$options->mm?>).</small></p>
			<input type="number" class="form-control form-control-sm" name="width" value="<?=$options->mm*$media->width?>"/>mm

<? } ?>
<!--			<button type="button" name="measure">Measure</button> <input type="hidden" name="mm" value="">  -->

<!-- -->

<? if($media->media_type == '3d') { 

		$tools = $options['tools']; ?>
			<!-- TRACKBALL -->
		<hr/>
			<p>Trackball</p>
<?			$trackballs = array(
				'TurntablePanTrackball'=>'Turntable Pan', 
				'SphereTrackball'=>'Sphere', 
				'PanTiltTrackball'=>'Pan Tilt', 
				'TurnTableTrackball'=>'Turntable'); ?>

			<select class="form-control form-control-sm" name="trackball">

			<? foreach($trackballs as $b=>$v) { ?>      
			<option value="<?=$b?>" <?=$trackball == $b? 'checked' : ''?>> <?=$v?> </option>
			<? } ?>

			</select><br/>

			<div class="form-group form-inline justify-content-between">
			<button class="btn btn-sm" name="transform"> Set initial position </button>
			<button class="btn btn-sm" name="retransform"> Reset </button>

			<select class="form-control form-control-sm" name="rotate">
				<option value="0" selected>Rotation:</option>
				<option value="yz">Switch Y and Z</option>

				<option value="x90" >X axis  90&deg;</option>
				<option value="x180">X axis 180&deg;</option>
				<option value="x270">X axis 270&deg;</option>

				<option value="y90" >Y axis  90&deg;</option>
				<option value="y180">Y axis 180&deg;</option>
				<option value="y270">Y axis 270&deg;</option>

				<option value="z90" >Z axis  90&deg;</option>
				<option value="z180">Z axis 180&deg;</option>
				<option value="z270">Z axis 270&deg;</option>

			</select>
			</div>

			<div class="form-group form-inline justify-content-between">
			<p>FOV:</p> <input class="form-control form-control-sm" name="fov" type="number" value="<?=$space['cameraFOV']?>"/> 
			</div>


			<hr/>

			<p>Lighting:</p>
			<input type="checkbox" name="lighting" value="1"     <?= (!isset($space['sceneLighting']) || $space['sceneLighting'])?    'checked':''?>> Lighting
			<hr/>

			<p>Buttons:</p>
			<input type="checkbox" name="tools[]" value="lighting"     <?=$tools['lighting']?    'checked':''?>> <img src="/3d/skins/dark/lighting.png" width="24px"> Light on/off</br>
			<input type="checkbox" name="tools[]" value="light"        <?=$tools['light']?       'checked':''?>> <img src="/3d/skins/dark/light.png" width="24px"> Light direction</br>
			<input type="checkbox" name="tools[]" value="color"        <?=$tools['color']?       'checked':''?>> <img src="/3d/skins/dark/color.png" width="24px"> Color</br>
			<input type="checkbox" name="tools[]" value="measure"      <?=$tools['measure']?     'checked':''?>> <img src="/3d/skins/dark/measure.png" width="24px"> Measure</br>
			<input type="checkbox" name="tools[]" value="picking"      <?=$tools['picking']?     'checked':''?>> <img src="/3d/skins/dark/pick.png" width="24px"> Picking</br>
			<input type="checkbox" name="tools[]" value="sections"     <?=$tools['sections']?    'checked':''?>> <img src="/3d/skins/dark/sections.png" width="24px"> Sections</br>
			<input type="checkbox" name="tools[]" value="orthographic" <?=$tools['orthographic']?'checked':''?>> <img src="/3d/skins/dark/orthographic.png" width="24px"> Orthographic</br>

<? } ?>

		<hr/>
			<div class="row">
				<div class="col-6"><button class="btn btn-sm btn-block" name="publish"> Publish </button></div>
				<div class="col-6"><button class="btn btn-sm btn-block" name="reset"> Reset everything </button></div>
			</div>

		</div>

	</div>
</body>
<script src="/js/jquery-3.3.1.min.js"></script>
<script>

function refresh() {
	document.getElementById('media').contentWindow.location.reload();
}

var options = <?=json_encode((array)$options);?>;

$('.skins').click(function() {
	var skin = $(this).attr('data-skin');
	options.skin = skin;
	updateOptions();
});

//$('input[name=background], input[type=color]').click(updateBackground);
$('input[name=background], input[type=color], select[name=image]').change(updateBackground);
$('input[name=width]'       ).change(updateScale);
$('select[name=trackball]'  ).change(updateTrackball);
$('button[name=transform]'  ).click(updateTransform);
$('button[name=retransform]').click(updateRetransform);
$('input[name=lighting]'    ).click(updateLighting);
$('input[name="tools[]"]'   ).click(updateTools);
$('button[name=reset]'      ).click(resetEverything);
$('input[name="fov"]'       ).change(updateFov);
$('select[name="rotate"]'    ).change(updateRotate);

function updateScale() {
	var w = $('input[name=width]').val();
	if(!w) return;
	options.mm = w/<?=$media->width?$media->width:1?>;
	updateOptions();
}

function updateBackground() {
	var back = $('input[name=background]:checked').val();
	if(!back) return;

	var background = { type: back };
	if(back == 'image')
		background.value = $('select[name=image]').val();
	else
		background.value = [ $('input[name=background-0').val(), $('input[name=background-1').val() ];

	options.background = background;
	updateOptions();
}

function updateTrackball() {
	var track = $('select[name=trackball] option:checked').val();
	if(!track) return;

	var trackball = null;
	switch(track) {
	case 'TurnTableTrackball':
		trackball = {
			startPhi: 0.0,
			startTheta: 0.0,
			startDistance: 2.5,
			minMaxPhi: [-180, 180],
			minMaxTheta: [-70.0, 70.0],
			minMaxDist: [0.5, 3.0]
		};
	break;
	case 'PanTiltTrackball': 
		trackball = {
			startPanX: 0.0,
			startPanY: 0.0,
			startAngleX: 0.0,
			startAngleY: 0.0,
			startDistance: 2.5,
			minMaxPanX: [-1.0, 1.0],
			minMaxPanY: [-1.0, 1.0],
			minMaxAngleX: [-70.0, 70.0],
			minMaxAngleY: [-70.0, 70.0],
			minMaxDist: [0.5, 3.0]
		};
	break;
	case 'SphereTrackball':
		trackball = {
			startDistance: 2.5,
			minMaxDist: [0.5, 3.0]
		};
	break;
	case 'TurntablePanTrackball': 
		trackball = {
			startPhi      : 0.0,
			startTheta    : 0.0,
			startDistance : 2.5,
			startPanX     : 0.0,
			startPanY     : 0.0,
			startPanZ     : 0.0,
			minMaxPhi     : [-180, 180],
			minMaxTheta   : [-70.0, 70.0],
			minMaxDist    : [0.5, 3.0],
			minMaxPanX    : [-1.0, 1.0],
			minMaxPanY    : [-1.0, 1.0],
			minMaxPanZ    : [-1.0, 1.0],
		}
	break;
	default: alert('a!');
	}

	options.trackball = { type: track, trackOptions: trackball }
	updateOptions();
}

function updateTransform() {

	var presenter = window.frames[0].presenter;
	var m = presenter.trackball.matrix;
	var scene_radius = 1/presenter.sceneRadiusInv;

	options.transform = { matrix: m };
	options.space.radiusMode = "explicit";
	options.space.explicitRadius = scene_radius*(-m[14]/2.5);
//	console.log(m[14]);
	updateOptions();
}

function updateRotate() {
	var r = $(this).val();
	var m = [];
	switch(r) {
		case '0'   : m = [ 1, 0, 0, 0,   0, 1, 0, 0,   0, 0, 1, 0,   0, 0, 0, 1]; break;

		case 'yz' : m =  [-1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1]; break;

		case 'x90' : m = [ 1, 0, 0, 0,   0, 0, 1, 0,   0,-1, 0, 0,   0, 0, 0, 1]; break;
		case 'x180': m = [ 1, 0, 0, 0,   0,-1, 0, 0,   0, 0,-1, 0,   0, 0, 0, 1]; break;
		case 'x270': m = [ 1, 0, 0, 0,   0, 0,-1, 0,   0, 1, 0, 0,   0, 0, 0, 1]; break;

		case 'y90' : m = [ 0, 0, 1, 0,   0, 1, 0, 0,  -1, 0, 0, 0,   0, 0, 0, 1]; break;
		case 'y180': m = [-1, 0, 0, 0,   0, 1, 0, 0,   0, 0,-1, 0,   0, 0, 0, 1]; break;
		case 'y270': m = [ 0, 0,-1, 0,   0, 1, 0, 0,   1, 0, 0, 0,   0, 0, 0, 1]; break;

		case 'z90' : m = [ 0,-1, 0, 0,   1, 0, 0, 0,   0, 0, 1, 0,   0, 0, 0, 1]; break;
		case 'z180': m = [-1, 0, 0, 0,   0,-1, 0, 0,   0, 0, 1, 0,   0, 0, 0, 1]; break;
		case 'z270': m = [ 0, 1, 0, 0,  -1, 0, 0, 0,   0, 0, 1, 0,   0, 0, 0, 1]; break;

	}
	options.transform = { matrix: m };
	updateOptions();
}

//reset transform
function updateRetransform() {
	options.transform = null;
	delete options.space.radiusMode;
	delete options.space.explicitRadius;
	updateOptions();
}


function updateFov() {
	options.space.cameraFOV = $(this).val();
	updateOptions();
}

function updateTools() {
	var tools = $('input[name="tools[]"]');
	tools.each(function() {
		var value = $(this).val();
		var checked = $(this).is(':checked');
		options.tools[value] = checked;
	});
	updateOptions();
}



function updateLighting() {
	options.space.sceneLighting = $('input[name=lighting]').is(':checked');
	updateOptions();
}

function resetEverything() {
	options = {};
	updateOptions(true);
}

function updateOptions(reload) {

	let json = JSON.stringify(options);
	let xhr = new XMLHttpRequest();
	xhr.open('POST', "/media/update/config/<?=$media->label?>", true);
	xhr.setRequestHeader('Content-Type', 'application/json');
	xhr.send(json);

	xhr.onreadystatechange = (event) => {
			if (xhr.readyState === 4) {
				if (xhr.status === 200) {
					if(reload) {
						window.location.reload();
						return;
					}
					refresh();
				} else {
					if(this.onError) 
						this.onError(xhr.statusText);
					else
						alert('Could not save config.');
				}
			}
		};
}






$('button[name=publish]').click(function(e) {

	var button = $(this);
	var published = (button.attr('data-published') == "1"); //already published

	function publish_done() {
		published = !published; //reflect cuirrent status no

		button.removeClass('btn-warning btn-info');
		button.addClass(published ?'btn-success':'btn-info');
		button.attr('data-published', published? "1":"0");
	}

	var frame = document.querySelector("#media");
	var doc = frame.contentDocument || frame.contentWindow.document;
	var canvas = doc.querySelector('canvas');

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



</script>
</html>
