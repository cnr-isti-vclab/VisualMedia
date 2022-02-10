<?php $data = file_get_contents('options.json');
$options = json_decode($data);
?>

<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta content="charset=UTF-8"/>
<title>3DHOP</title>
<!--STYLESHEET-->
<link type="text/css" rel="stylesheet" href="stylesheet/3dhop.css"/>
<link type="text/css" rel="stylesheet" href="stylesheet/3dhop_panels.css"/>
<link type="text/css" rel="stylesheet" href="stylesheet/3dhop_navcube.css"/>
<!--SPIDERGL-->
<script type="text/javascript" src="js/spidergl.js"></script>
<!--JQUERY-->
<script type="text/javascript" src="js/jquery.js"></script>
<!--PRESENTER-->
<script type="text/javascript" src="js/presenter.js"></script>
<!--3D MODELS LOADING AND RENDERING-->
<script type="text/javascript" src="js/nexus.js"></script>
<script type="text/javascript" src="js/ply.js"></script>
<!--TRACKBALLS-->
<script type="text/javascript" src="js/trackball_turntable.js"></script>
<script type="text/javascript" src="js/trackball_turntable_pan.js"></script>
<script type="text/javascript" src="js/trackball_pantilt.js"></script>
<script type="text/javascript" src="js/trackball_sphere.js"></script>
<!--UTILITY-->
<script type="text/javascript" src="js/init.js"></script>

<!--BOOTSTRAP STYLE-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">

<!--TOASRT-->
<link type="text/css" rel="stylesheet" href="stylesheet/toastr.min.css" rel="stylesheet"/>
<script type="text/javascript" src="js/toastr.min.js"></script>

<style>
html { overflow:hidden; }

#draw-canvas {
<?php 
$type = $options->background->type;
$color0 = $options->background->color0;
$color1 = $options->background->color1;
$image = $options->background->image;
switch($type) {
	case 'flat':   echo("background-color: {$color0}\n"); break;
	case 'linear': echo("background: linear-gradient($color0, rgba(0, 0, 0, 0));\n background-color:$color1;\n"); break;
	case 'radial': echo("background: radial-gradient($color0, rgba(0, 0, 0, 0));\n background-color:$color1;\n"); break;
	case 'image':  echo("background-image: url(skins/backgrounds/$image); background-size: cover;\n"); break;
}
?>
}

.mouse {
	pointer-events: auto;
}

</style>

</head>
<body>
<div id="3dhop" class="tdhop" onmousedown="if (event.preventDefault) event.preventDefault()"><div id="tdhlg"></div>

<!--TOOLBAR-->
 <div id="toolbar"></div>
<!--TOOLBAR-->

<!--MEASURE-->
 <div id="measure-box" class="output-box">Measured length<hr/><span id="measure-output" class="output-text" onmousedown="event.stopPropagation()">0.0</span></div>
<!--MEASURE-->

<!--POINT PICKING-->
 <div id="pickpoint-box" class="output-box">XYZ picked point<hr/><span id="pickpoint-output" class="output-text" onmousedown="event.stopPropagation()">[ 0 , 0 , 0 ]</span></div>
<!--POINT PICKING-->

<!--SECTIONS-->
 <div id="sections-box" class="output-box">
	<table class="output-table" onmousedown="event.stopPropagation()">
	<tr>
		<td>Plane</td><td>Position</td><td>Flip</td>
	</tr>
	<tr>
		<td><img   id="xplane_on"    title="Disable X Axis Section" src="skins/icons/sectionX_on.png" onclick="sectionxSwitch()" style="position:absolute; visibility:hidden; border:1px inset;"/>
			<img   id="xplane"       title="Enable X Axis Section"  src="skins/icons/sectionX.png"  onclick="sectionxSwitch()"/><br/></td>
		<td><input id="xplaneSlider" class="output-input"  type="range"    title="Move X Axis Section Position"/></td>
		<td><input id="xplaneFlip"   class="output-input"  type="checkbox" title="Flip X Axis Section Direction"/></td></tr>
	<tr>
		<td><img   id="yplane_on"    title="Disable Y Axis Section" src="skins/icons/sectionY_on.png" onclick="sectionySwitch()" style="position:absolute; visibility:hidden; border:1px inset;"/>
			<img   id="yplane"       title="Enable Y Axis Section"  src="skins/icons/sectionY.png"  onclick="sectionySwitch()"/><br/></td>
		<td><input id="yplaneSlider" class="output-input"  type="range"    title="Move Y Axis Section Position"/></td>
		<td><input id="yplaneFlip"   class="output-input"  type="checkbox" title="Flip Y Axis Section Direction"/></td></tr>
	<tr>
		<td><img   id="zplane_on"    title="Disable Z Axis Section" src="skins/icons/sectionZ_on.png" onclick="sectionzSwitch()" style="position:absolute; visibility:hidden; border:1px inset;"/>
			<img   id="zplane"       title="Enable Z Axis Section"  src="skins/icons/sectionZ.png"  onclick="sectionzSwitch()"/><br/></td>
		<td><input id="zplaneSlider" class="output-input"  type="range"    title="Move Y Axis Section Position"/></td>
		<td><input id="zplaneFlip"   class="output-input"  type="checkbox" title="Flip Z Axis Section Direction"/></td>
	</tr>
	</table>
	<table class="output-table" onmousedown="event.stopPropagation()" style="text-align:right;">
	<tr>
		<td>Show planes<input id="showPlane" class="output-input" type="checkbox" title="Show Section Planes" style="bottom:-3px;"/></td>
		<td>Show edges<input  id="showBorder" class="output-input" type="checkbox" title="Show Section Edges" style="bottom:-3px;"/></td>
	</tr>
	</table>
 </div>
<!--SECTIONS-->

<!-- INFO -->
 <div id="cover" onclick="$('#cover').css('display', 'none'); helpSwitch(false);">
  <div class="h-100 row align-items-center">
	 <div class="panel my-0 mx-auto" id="help_panel">
	  <h5 class="mt-3">Info</h5>
	  <hr/>
	  <h6 class="mb-3">Controls</h6>
	  <div class="row my-2">
		<div class="col">Rotate</div>
		<div class="col">Zoom</div>
		<div class="col">Pan</div>
		<div class="w-100 my-2"></div>
		<div class="col"><img src="skins/icons/left.png" width="30"/></div>
		<div class="col"><img src="skins/icons/wheel.png" width="30"/></div>
		<div class="col"><img src="skins/icons/right.png" width="30"/></div>
		<div class="w-100 my-2"></div>
		<div class="col">Left Button<br/> + Move</div>
		<div class="col">Mouse Wheel</div>
		<div class="col">Right Button<br/> + Move</div>
	  </div>
	 </div>
  </div>
 </div>
<!-- INFO -->

<div id="panel_widgets" class="" style="position:absolute; right:0px; top:0; pointer-events: none;">
	<div id="compass" class="m-2 d-none">
		<center>
			<canvas class="mouse" id="compassCanvas" style="width:100; height:100;" onclick="compassClick()"/>
		<center>
	</div>
	<div id="navCube" class="m-2 d-none">
		<center>
			<div class="cubeScene mouse">
			  <div class="cube">
				<div class="cube__face cube__face--front" onclick="viewFrom('front');">FRONT</div>
				<div class="cube__face cube__face--back" onclick="viewFrom('back');">BACK</div>
				<div class="cube__face cube__face--right" onclick="viewFrom('right');">RIGHT</div>
				<div class="cube__face cube__face--left" onclick="viewFrom('left');">LEFT</div>
				<div class="cube__face cube__face--top" onclick="viewFrom('top');">ABOVE</div>
				<div class="cube__face cube__face--bottom" onclick="viewFrom('bottom');">BELOW</div>
			  </div>
			</div>
		<center>
	</div>	
	<div id="canonicalViews" class="m-2 d-none">			
		<center>
		<table>
		<tr><td></td><td><button id="vtop" class="btn btn-sm btn-secondary w-100 mouse" onclick="viewFrom('top');">ABOVE</button></td><td></td><td></td></tr>
		<tr><td><button class="btn btn-sm btn-secondary w-100 mouse" id="vleft" onclick="viewFrom('left');">LEFT</button></td><td><button class="btn btn-sm btn-secondary w-100 mouse" id="vfront" onclick="viewFrom('front');">FRONT</button></td><td><button class="btn btn-sm btn-secondary w-100 mouse" id="vright" onclick="viewFrom('right');">RIGHT</button></td><td><button class="btn btn-sm btn-secondary w-100 mouse" id="vback" onclick="viewFrom('back');">BACK</button></td></tr>
		<tr><td></td><td><button class="btn btn-sm btn-secondary w-100 mouse" id="vbottom" onclick="viewFrom('bottom');">BELOW</button></td><td></td><td></td></tr> 
		</table>
		</center>
	</div>
</div>
 <canvas id="draw-canvas"></canvas>
</div>
</body>

<script type="text/javascript">
var presenter = null;
let options = <?=json_encode($options, JSON_PRETTY_PRINT)?>;

let trackball = options.trackball;
switch(trackball.type) {
	case 'TurnTableTrackball':    trackball.type = TurnTableTrackball;    break;
	case 'PanTiltTrackball':      trackball.type = PanTiltTrackball;      break;
	case 'SphereTrackball':       trackball.type = SphereTrackball;       break;
	case 'TurntablePanTrackball': trackball.type = TurntablePanTrackball; break;
}

let spots = {};
if (options.spots) {
	spots = createSceneSpots(options.spots, options.space.scaleFactor);
	options.tools.push('hotspot');
}

let tools = { 
	home:     { title: "Home",                  icon: "home.png"},
	zoomin:   { title: "Zoom In",               icon: "zoomin.png"},
	zoomout:  { title: "Zoom Out",              icon: "zoomout.png"},
	lighting: { title: "Disable Lighting",       icon: "lighting.png", 
				title_on: "Enable Lighting",   icon_on: "lighting_off.png", id_on: "lighting_off"},
	light:    { title: "Enable Light Control",  icon: "lightcontrol.png", 
				title_on: "Disable Light Control", icon_on: "lightcontrol_on.png"},
	measure:  { title: "Enable Measure Tool",   icon: "measure.png", 
				title_on: "Disable Measure Tool", icon_on: "measure_on.png"},
	pick:     { title: "Enable PickPoint Mode", icon: "pick.png", 
				title_on: "Disable PickPoint Mod",icon_on: "pick_on.png"},
	sections: { title: "Enable Plane Sections", icon: "sections.png", 
				title_on: "Disable Plane Section", icon_on: "sections_on.png"},
	color:    { title: "Enable Solid Color",    icon: "color.png", 
				title_on: "Disable Solid Color",icon_on: "color_on.png"},
	orthographic: { title: "Orthographic Camera", icon: "orthographic.png", 
				title_on: "Perspective Camera",icon_on: "perspective.png", id_on: "perspective"},
	hotspot: { title: "Show Hotspots", icon: "pin.png", 
				title_on: "Hide Hotspots",icon_on: "pin_on.png"},
	full:     { title: "Full Screen", icon: "full.png", 
				title_on: "Exit Full Screen",icon_on: "full_on.png"},
	help:     { title: "Info about the media", icon: "help.png", 
				title_on: "Info about the media",icon_on: "help_on.png"},
};

let toolbar = document.querySelector('#toolbar');
for(let id in tools) { 
	if(options.tools.includes(id)) {
		let tool = tools[id];
		if(tool.title_on) {
			let img_on = document.createElement('img');
			img_on = document.createElement('img');
			img_on.id = tool.id_on? tool.id_on : id + '_on';
			img_on.setAttribute('title', tool.title_on);
			img_on.style.position = 'absolute';
			img_on.style.visibility = 'hidden';
			img_on.src = `skins/${options.skin}/${tool.icon_on}`;
			toolbar.appendChild(img_on);
		}
		let img = document.createElement('img');
		img.id = id;
		img.setAttribute('title', tool.title);
		img.src = `skins/${options.skin}/${tool.icon}`;
		toolbar.appendChild(img);
		let br = document.createElement('br');
		toolbar.appendChild(br);
	}
}

function setup3dhop() {
	presenter = new Presenter("draw-canvas");

	//presenter.toggleDebugMode();

	//specular color
	let specularColor = 

	presenter.setScene({
		meshes: {
			"mesh_1" : { url: "models/gargoyle.nxs" },
			"sphere" : { url: "models/sphere.ply" },
		},
		modelInstances : {
			"model_1" : { 
				mesh  : "mesh_1",
				useSolidColor : (options.scene[0].startColor=="color")?false:true,
				color : hex2color(options.scene[0].solidColor),
				specularColor : [0.1 * options.scene[0].specular, 0.1 * options.scene[0].specular, 0.1 * options.scene[0].specular, 32],
				transform: options.scene[0].matrix? {matrix : options.scene[0].matrix} : null
			}
		},
		spots: spots,
		trackball: trackball,
		space: options.space
	});

//--MEASURE--
	presenter._onEndMeasurement = onEndMeasure;
//--MEASURE--

//--POINT PICKING--
	presenter._onEndPickingPoint = onEndPick;
//--POINT PICKING--

//--HOTSPOTS--
	presenter._onEnterSpot = onEnterSpot;
	presenter._onLeaveSpot = onLeaveSpot;
//--HOTSPOTS--

//--SECTIONS--
	sectiontoolInit();
//--SECTIONS--

	// start conditions - interface
	presenter.setSpotVisibility(HOP_ALL, false, false);
	colorSwitch((options.scene[0].startColor=="color")?false:true);
	cameraSwitch((options.space.cameraType=="orthographic")?false:true);
	lightingSwitch();
}

function actionsToolbar(action) {
	if(action=='home') presenter.resetTrackball();
//--FULLSCREEN--
	else if(action=='full') enterFullscreen();
	else if(action=='full_on') exitFullscreen();
//--FULLSCREEN--
//--ZOOM--
	else if(action=='zoomin') presenter.zoomIn();
	else if(action=='zoomout') presenter.zoomOut();
//--ZOOM--
//--LIGHTING--
	else if(action=='lighting') { presenter.enableSceneLighting(false); lightingSwitch(); }
	else if(action=='lighting_off') { presenter.enableSceneLighting(true); lightingSwitch(); }
//--LIGHTING--
//--LIGHT--
	else if(action=='light') { presenter.enableLightTrackball(true); lightSwitch(); }
	else if(action=='light_on') { presenter.enableLightTrackball(false); lightSwitch(); }
//--LIGHT--
//--CAMERA--
	else if(action=='perspective') { presenter.setCameraPerspective(); cameraSwitch(); }
	else if(action=='orthographic') { presenter.setCameraOrthographic(); cameraSwitch(); }
//--CAMERA--
//--COLOR--
	else if(action=='color') { presenter.setInstanceSolidColor(HOP_ALL, true, true); colorSwitch(); }
	else if(action=='color_on') { presenter.setInstanceSolidColor(HOP_ALL, false, true); colorSwitch(); }
//--COLOR--
//--MEASURE--
	else if(action=='measure') { presenter.enableMeasurementTool(true); measureSwitch(); }
	else if(action=='measure_on') { presenter.enableMeasurementTool(false); measureSwitch(); }
//--MEASURE--
//--POINT PICKING--
	else if(action=='pick') { presenter.enablePickpointMode(true); pickpointSwitch(); }
	else if(action=='pick_on') { presenter.enablePickpointMode(false); pickpointSwitch(); }
//--POINT PICKING--
//--SECTIONS--
	else if(action=='sections') { sectiontoolReset(true); sectiontoolSwitch(); }
	else if(action=='sections_on') { sectiontoolReset(false); sectiontoolSwitch(); }
//--SECTIONS--
//--HOTSPOTS--
	else if(action=='hotspot') { presenter.setSpotVisibility(HOP_ALL, true, true); presenter.enableOnHover(true); hotspotSwitch(); }
	else if(action=='hotspot_on') { presenter.setSpotVisibility(HOP_ALL, false, true); presenter.enableOnHover(false); hotspotSwitch(); }
//--HOTSPOTS--
	else if(action=='help') { showPanel('help_panel'); helpSwitch(); } 
}

//--MEASURE--
function onEndMeasure(measure) {
	// measure.toFixed(2) sets the number of decimals when displaying the measure
	// depending on the model measure units, use "mm","m","km" or whatever you have
	$('#measure-output').html(measure.toFixed(2) + " "); 
}
//--MEASURE--

//--PICKPOINT--
function onEndPick(point) {
	// .toFixed(2) sets the number of decimals when displaying the picked point	
	var x = point[0].toFixed(2);
	var y = point[1].toFixed(2);
	var z = point[2].toFixed(2);
	$('#pickpoint-output').html("[ "+x+" , "+y+" , "+z+" ]");
}
//--PICKPOINT--

//--HOTSPOTS--
function createSceneSpots(optionSpots, scaleFactor){
	let spots = {};

	for (let id in optionSpots) {
		spots[id] = {
			mesh            : "sphere",
			color           : optionSpots[id].color,
			alpha           : 0.7,
			alphaHigh       : 0.9,
			transform : { 
				translation : optionSpots[id].pos,
				scale : [optionSpots[id].radius*scaleFactor, optionSpots[id].radius*scaleFactor, optionSpots[id].radius*scaleFactor],
				},
			visible         : optionSpots[id].visible,
		}
	}

	return spots;
}

function onEnterSpot(id) {
	toastr.options.timeOut = 0;
	toastr.info(options.spots[id].title);
}
function onLeaveSpot(id) {
	toastr.remove();
	toastr.options.timeOut = 2000;
}
//--HOTSPOTS--

function closeAllTools(){
	presenter.enableLightTrackball(false);
	lightSwitch();
	presenter.enableMeasurementTool(false);
	measureSwitch();
	presenter.enablePickpointMode(false);
	pickpointSwitch();
	sectiontoolReset();
	sectiontoolSwitch(false);
	presenter.setSpotVisibility(HOP_ALL, false, true); 
	presenter.enableOnHover(false);
	hotspotSwitch();

	presenter.repaint();
}

//--GRID
function addGrid(instance, step) {
	var rad = 1.0 / presenter.sceneRadiusInv;
	var bb = getBBox(instance);
	
	var XC = (bb[0] + bb[3]) / 2.0;
	var YC = bb[4];
	var ZC = (bb[2] + bb[5]) / 2.0;
	
	var gStep,gStepNum;
	if(step===0.0) {
		gStepNum = 15;
		gStep = rad/gStepNum;
	}
	else {
		gStep = step;
		gStepNum = Math.ceil(rad/gStep);
	}
	
	var linesBuffer, grid, gg;
	linesBuffer = [];
	for (gg = -gStepNum; gg <= gStepNum; gg+=1)
	{
			linesBuffer.push([XC + (gg*gStep), YC, ZC + (-gStep*gStepNum)]);
			linesBuffer.push([XC + (gg*gStep), YC, ZC + ( gStep*gStepNum)]);
			linesBuffer.push([XC + (-gStep*gStepNum), YC, ZC + (gg*gStep)]);
			linesBuffer.push([XC + ( gStep*gStepNum), YC, ZC + (gg*gStep)]);
	}
	grid = presenter.createEntity("baseGrid", "lines", linesBuffer);
	grid.color = [0.9, 0.9, 0.9, 0.3];
	grid.zOff = 0.0;
	grid.useTransparency = true;
	presenter.repaint();
}
function removeGrid() {
	presenter.deleteEntity("baseGrid");
}

//--TRACK SPHERE
function addTrackSphere(instance) {
	var rad = 0.7 / presenter.sceneRadiusInv;
	var bb = getBBox(instance);
	
	var XC = (bb[0] + bb[3]) / 2.0;
	var YC = (bb[1] + bb[4]) / 2.0;
	var ZC = (bb[2] + bb[5]) / 2.0;

	var sStep,sStepNum;	
	sStepNum = 32;
	sStep = (2 * Math.PI) / sStepNum;
	
	var linesBuffer, sphere, ii;
	linesBuffer = [];
	for (ii = 0; ii < sStepNum; ii+=1)
	{
		linesBuffer.push([XC + (Math.cos(ii*sStep) * rad), YC, ZC + (Math.sin(ii*sStep) * rad)]);
		linesBuffer.push([XC + (Math.cos((ii+1)*sStep) * rad), YC, ZC + (Math.sin((ii+1)*sStep) * rad)]);
		
		linesBuffer.push([XC , YC + (Math.cos(ii*sStep) * rad), ZC + (Math.sin(ii*sStep) * rad)]);
		linesBuffer.push([XC , YC + (Math.cos((ii+1)*sStep) * rad), ZC + (Math.sin((ii+1)*sStep) * rad)]);
		
		linesBuffer.push([XC + (Math.cos(ii*sStep) * rad), YC + (Math.sin(ii*sStep) * rad), ZC]);
		linesBuffer.push([XC + (Math.cos((ii+1)*sStep) * rad), YC + (Math.sin((ii+1)*sStep) * rad), ZC]);
	}	
	
	sphere = presenter.createEntity("trackSphere", "lines", linesBuffer);
	sphere.color = [0.9, 0.9, 0.9, 0.1];
	sphere.zOff = 0.0;
	sphere.useTransparency = true;
	presenter.repaint();
}
function removeTrackSphere() {
	presenter.deleteEntity("trackSphere");
}

function getBBox(instance) {
	var mname = presenter._scene.modelInstances[instance].mesh;
	var vv = presenter._scene.meshes[mname].renderable.mesh.basev;
	var bbox = [-Number.MAX_VALUE, -Number.MAX_VALUE, -Number.MAX_VALUE, Number.MAX_VALUE, Number.MAX_VALUE, Number.MAX_VALUE];	
	var point,tpoint;
	
	for(var vi=1; vi<(vv.length / 3); vi++){
		point = [vv[(vi*3)+0], vv[(vi*3)+1], vv[(vi*3)+2], 1.0]
		tpoint = SglMat4.mul4(presenter._scene.modelInstances[instance].transform.matrix, point);
		if(tpoint[0] > bbox[0]) bbox[0] = tpoint[0];
		if(tpoint[1] > bbox[1]) bbox[1] = tpoint[1];
		if(tpoint[2] > bbox[2]) bbox[2] = tpoint[2];
		if(tpoint[0] < bbox[3]) bbox[3] = tpoint[0];
		if(tpoint[1] < bbox[4]) bbox[4] = tpoint[1];
		if(tpoint[2] < bbox[5]) bbox[5] = tpoint[2];
	}		
	return bbox;
}

function startupGrid(){
	var vv = presenter._scene.meshes[presenter._scene.modelInstances["model_1"].mesh].renderable.mesh.basev;
	if (typeof vv === 'undefined')
		setTimeout(startupGrid, 50);
	else {
		addGrid("model_1",options.widgets.grid.step);
	}
}

function startupTrackSphere(){
	var vv = presenter._scene.meshes[presenter._scene.modelInstances["model_1"].mesh].renderable.mesh.basev;
	if (typeof vv === 'undefined')
		setTimeout(startupTrackSphere, 50);
	else {
		addTrackSphere("model_1");
	}
}

//-------------------------------------------------------------------------
function viewFrom(direction){
	var distance = 1.4;
	
	let trackType = presenter._scene.trackball.type;
		
    switch(direction) {
        case "front":
			if(trackType === TurntablePanTrackball)
				presenter.animateToTrackballPosition([0.0, 0.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === SphereTrackball)
				presenter.animateToTrackballPosition([[ 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);
            break;
        case "back":
			if(trackType === TurntablePanTrackball)		
				presenter.animateToTrackballPosition([180.0, 0.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === SphereTrackball)
				presenter.animateToTrackballPosition([[-1, 0, 0, 0, 0, 1, 0, 0, 0, 0,-1, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);		
            break;			
        case "top":
			if(trackType === TurntablePanTrackball)		
				presenter.animateToTrackballPosition([0.0, 90.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === SphereTrackball)
				presenter.animateToTrackballPosition([[ 1, 0, 0, 0, 0, 0, 1, 0, 0,-1, 0, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);
            break;
        case "bottom":
			if(trackType === TurntablePanTrackball)
				presenter.animateToTrackballPosition([0.0, -90.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === SphereTrackball)
				presenter.animateToTrackballPosition([[ 1, 0, 0, 0, 0, 0,-1, 0, 0, 1, 0, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);				
            break;
        case "left":
			if(trackType === TurntablePanTrackball)		
				presenter.animateToTrackballPosition([270.0, 0.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === SphereTrackball)
				presenter.animateToTrackballPosition([[ 0, 0,-1, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);				
            break;
        case "right":
			if(trackType === TurntablePanTrackball)		
				presenter.animateToTrackballPosition([90.0, 0.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === SphereTrackball)				
				presenter.animateToTrackballPosition([[ 0, 0, 1, 0, 0, 1, 0, 0,-1, 0, 0, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);
            break;			
    }
}

//-------------------------------------------------------------------------


function onTrackballUpdate(trackState){
	
	if(options.widgets.compass.atStartup)
		updateCompass(sglDegToRad(trackState[0]), sglDegToRad(trackState[1]));

	if(options.widgets.navCube.atStartup)
		updateCube(trackState);
}

// CUBE
function updateCube(trackState) {
	
	let trackType = presenter._scene.trackball.type;
	let transf;
	
	if(trackType === TurntablePanTrackball){
		transf = "translateZ(-100px) rotateX("+ (-trackState[1]) +"deg) rotateY("+ (-trackState[0]) +"deg)";
	}
	else if (trackType === SphereTrackball){
		let m = trackState[0];	
		transf = "translateZ(-100px) matrix3d(" + 
		-m[0]  + ", "  + m[1]  + ", "  + -m[2]  + ", "  + m[3]  + ", " + 
		-m[4]  + ", "  + m[5]  + ", "  + -m[6]  + ", "  + m[7]  + ", " + 
		-m[8]  + ", "  + m[9]  + ", "  + -m[10] + ", "  + m[11] + ", " + 
		-m[12] + ", "  + m[13] + ", "  + -m[14] + ", "  + m[15] + ") rotateY(180deg)";
	}
	
    $('.cube').css({"transform":transf});
}

// COMPASS
function updateCompass(angle, tilt) {
	$('#compassCanvas').attr('width', 100);
	$('#compassCanvas').attr('height',100);
 	var canv = document.getElementById("compassCanvas");
	var ctx = canv.getContext("2d");
	var hh = canv.height;
	var ww = canv.width;
		
	ctx.clearRect(0, 0, canv.width, canv.height);	
    // Save the current drawing state
    ctx.save();
 
    // Now move across and down half the
    ctx.translate(ww/2.0, hh/2.0);
 
    // Rotate around this point
    ctx.rotate(angle);

	ctx.beginPath();
    ctx.arc(0, 0, 35, 0, 2 * Math.PI, false);
    ctx.lineWidth = 4;
    ctx.strokeStyle = '#44337766';
    ctx.stroke();
	
	ctx.font = "28px Verdana";
	ctx.strokeStyle = 'black';
    ctx.lineWidth = 1.5;
	ctx.strokeText("N",-10,-25);	
	ctx.strokeText("S",-10,45);
	ctx.strokeText("E",27,10);
	ctx.strokeText("W",-47,10);	
    ctx.fillStyle = '#ff4444';
    ctx.fillText("N",-10,-25);	
    ctx.fillStyle = '#ffffff';
	ctx.fillText("S",-10,45);
	ctx.fillText("E",27,10);
	ctx.fillText("W",-47,10);	
	
    // Restore the previous drawing state
    ctx.restore();
}
function compassClick(){
	var dirX = (event.offsetX - (event.srcElement.width / 2.0)) / event.srcElement.width;
	var dirY = (event.offsetY - (event.srcElement.height / 2.0)) / event.srcElement.height;
	var len = Math.sqrt((dirX * dirX) + (dirY * dirY));
	dirX = dirX / len;
	dirY = dirY / len;
	var targetA = sglRadToDeg(Math.atan2(dirX, dirY));
	var currpos = presenter.getTrackballPosition();
	targetA = currpos[0] + targetA;
	targetA = targetA < 0 ? ((targetA % 360) + 360) : (targetA % 360);
	targetA = Math.floor((targetA + 45) / 90.0) * 90.0;
	currpos[0] = targetA
	presenter.animateToTrackballPosition(currpos);
}


// HELPERS-------------------------

function hex2color(hex){
	let r = parseInt(hex.substr(1,2), 16)
	let g = parseInt(hex.substr(3,2), 16)
	let b = parseInt(hex.substr(5,2), 16)
	return [r/255.0, g/255.0, b/255.0];
}

//----------------------------------------------

$(document).ready(function(){
	init3dhop();
	setup3dhop();

	// widgets
	if(options.widgets.grid.atStartup)
		setTimeout(startupGrid, 100);	// grid shows up at startup
	if(options.widgets.trackSphere.atStartup)
		setTimeout(startupTrackSphere, 100);	// track sphere shows up at startup
		
	if(options.widgets.canonicalViews.atStartup)
		document.getElementById("canonicalViews").classList.remove("d-none");
	if(options.widgets.compass.atStartup)
		document.getElementById("compass").classList.remove("d-none");
	if(options.widgets.navCube.atStartup)
		document.getElementById("navCube").classList.remove("d-none");		

	// TOASTR configuration
		toastr.options = {
		"positionClass": "toast-bottom-left",
		"preventDuplicates": false,
		"showDuration": "300",
		"hideDuration": "300",
		"timeOut": "2000",
		"extendedTimeOut": "1000"
	}
});
</script>
</html>
