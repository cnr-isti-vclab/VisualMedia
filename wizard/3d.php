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

<style>
.panel {
	visibility				:hidden;
	position				:absolute;
	z-index					:1;
	width					:600px; 
	font-size				:12px; 
	font-family				:verdana; 
	color					:#f8f8f8; 
	padding					:10px;
	background-color		:rgba(50, 50, 50, 0.9); 
	border					:2px solid #f8f8f8; 
	border-radius			:10px;
	box-shadow				:1px 1px 10px black;
	-webkit-box-shadow		:1px 1px 10px black;
	-moz-box-shadow			:1px 1px 10px black;
}
.panel a:link, .panel a:visited {
  color: #f8f8f8; decoration:none;
}

.a:hover, .panel a:active {
  color: #ffffff;  decoration:none;
}


.close {
	position				:relative;
	margin-left				:98%; 
	margin-top				:-30px;
	width					:25px; 
	height					:25px;
	background-color		:rgba(50, 50, 50, 1.0); 
	border					:2px solid #f8f8f8; 
	border-radius			:50%; 
	box-shadow				:1px 1px 10px black;
	-webkit-box-shadow		:1px 1px 10px black;
	-moz-box-shadow			:1px 1px 10px black;
}

.close:hover {
	cursor:pointer; 
}

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

html { overflow:hidden; }

</style>
<?php
	$skin = $options->skin;
	$tools = $options->tools;
	$background = $options->background;
?>

</head>
<body>
<div id="3dhop" class="tdhop" onmousedown="if (event.preventDefault) event.preventDefault()"><div id="tdhlg"></div>
<div id="toolbar">
	<img id="home"        title="Home"                   src="skins/<?=$skin?>/home.png"/><br/>
<!--LIGHTING-->
<?php if(in_array('lighting', $tools)) { ?>
	<img id="lighting_off" title="Enable Lighting"       src="skins/<?=$skin?>/lighting_off.png" style="position:absolute; visibility:hidden;"/>
	<img id="lighting"     title="Disable Lighting"      src="skins/<?=$skin?>/lighting.png"/><br/>
<?php } ?>
<!--LIGHTING-->
<!--LIGHT-->
<?php if(in_array('light', $tools)) { ?>
	<img id="light_on"    title="Disable Light Control"  src="skins/<?=$skin?>/lightcontrol_on.png" style="position:absolute; visibility:hidden;"/>
	<img id="light"       title="Enable Light Control"   src="skins/<?=$skin?>/lightcontrol.png"/><br/>
<?php } ?>
<!--LIGHT-->
<!--MEASURE-->
<?php if(in_array('measure', $tools)) { ?>
	<img id="measure_on"  title="Disable Measure Tool"   src="skins/<?=$skin?>/measure_on.png" style="position:absolute; visibility:hidden;"/>
	<img id="measure"     title="Enable Measure Tool"    src="skins/<?=$skin?>/measure.png"/><br/>
<?php } ?>
<!--MEASURE-->
<!--POINT PICKING-->
<?php if(in_array('pick', $tools)) { ?>
	<img id="pick_on"     title="Disable PickPoint Mode" src="skins/<?=$skin?>/pick_on.png" style="position:absolute; visibility:hidden;"/>
	<img id="pick"        title="Enable PickPoint Mode"  src="skins/<?=$skin?>/pick.png"/><br/>
<?php } ?>
<!--POINT PICKING-->
<!--SECTIONS-->
<?php if(in_array('sections', $tools)) { ?>
	<img id="sections_on" title="Disable Plane Sections" src="skins/<?=$skin?>/sections_on.png" style="position:absolute; visibility:hidden;"/>
	<img id="sections"    title="Enable Plane Sections"  src="skins/<?=$skin?>/sections.png"/><br/>
<?php } ?>
<!--SECTIONS-->
<!--COLOR-->
<?php if(in_array('color', $tools)) { ?>
	<img id="color_on"    title="Disable Solid Color"    src="skins/<?=$skin?>/color_on.png" style="position:absolute; visibility:hidden;"/>
	<img id="color"       title="Enable Solid Color"     src="skins/<?=$skin?>/color.png"/><br/>
<?php } ?>
<!--COLOR-->
<!--CAMERA-->
<?php if(in_array('orthographic', $tools)) { ?>
	<img id="perspective"  title="Perspective Camera"    src="skins/<?=$skin?>/perspective.png" style="position:absolute; visibility:hidden;"/>
	<img id="orthographic" title="Orthographic Camera"   src="skins/<?=$skin?>/orthographic.png"/><br/>
<?php } ?>
<!--CAMERA-->
<!--FULLSCREEN-->
	<img id="full_on"     title="Exit Full Screen"       src="skins/<?=$skin?>/full_on.png" style="position:absolute; visibility:hidden;"/>
	<img id="full"        title="Full Screen"            src="skins/<?=$skin?>/full.png"/><br/>
<!--FULLSCREEN-->
	<img id="help_on"     title="Info about the media"  src="skins/<?=$skin?>/help_on.png" style="position:absolute; visibility:hidden;"/>
	<img id="help"     title="Info about the media"  src="skins/<?=$skin?>/help.png"/><br/>
</div>

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
<div class="panel" id="help_pane" cellspacing="5">
	<h3 style="text-align:center"><img class="close" id="close_on" src="skins/minimal_light/close_on.png" onclick="helpSwitch();$('#toolbar img').css('opacity','0.5');" style="display:none;"/>
        <img class="close" id="close" src="skins/minimal_light/close.png"/>Info</h3>
<hr/>
	<p></p>
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

let tools = { 
	home:     { title: "Home",                  icon: "home.png"},
	zoomin:   { title: "Zoom In",               icon: "zoomin.png"},
	zoomout:  { title: "Zoom Out",              icon: "zoomout.png"},
	lighting: { title: "Enable Lighting",       icon: "lighting.png", 
	            title_on: "Disable Lighting",   icon_on: "lighting_off.png", id_on: "lighting_off"},
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
	full:     { title: "Full Screen", icon: "full_on.png", 
				title_on: "it Full Screen",icon_on: "full.png"},
	help:     { title: "Info about the media", icon: "help.png", 
				title_on: "Info about the media",icon_on: "help_on.png"},
};

/*
let toolbar = document.querySelector('#toolbar');
for(let id in options.tools) {
	let tool = tools[options.tools[id]];
	let img=null;
	
	if(tool.title_on) {
		img = document.createElement('img');
		img.id = tool.id_on? tool.id_on : options.tools[id] + '_on';
		img.setAttribute('title', tool.title_on);
		img.setAttribute('style', 'position:absolute; visibility:hidden;');		
		img.src = `skins/${options.skin}/${tool.icon_on}`;
		toolbar.appendChild(img);
	}	
	
	img = document.createElement('img');
	img.id = options.tools[id];
	img.setAttribute('title', tool.title);
	img.src = `skins/${options.skin}/${tool.icon}`;
	toolbar.appendChild(img);

	toolbar.appendChild(document.createElement("br"));
}
*/

function setup3dhop() {
	presenter = new Presenter("draw-canvas");

	presenter.toggleDebugMode();

	presenter.setScene({
		meshes: {
			"mesh_1" : { url: "models/gargoyle.nxs" }
		},
		modelInstances : {
			"model_1" : { 
				mesh  : "mesh_1",
				color : [0.8, 0.7, 0.75],
				transform: options.scene[0].matrix? {matrix : options.scene[0].matrix} : null
			}
		},
		trackball: trackball,
		space: options.space
	});

//--MEASURE--
	presenter._onEndMeasurement = onEndMeasure;
//--MEASURE--

//--POINT PICKING--
	presenter._onEndPickingPoint = onEndPick;
//--POINT PICKING--

//--SECTIONS--
	sectiontoolInit();
//--SECTIONS--

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
	else if(action=='sections') { sectiontoolReset(); sectiontoolSwitch(); }
	else if(action=='sections_on') { sectiontoolReset(); sectiontoolSwitch(); }	
//--SECTIONS--
	else if(action=='help') { helpSwitch(); } 
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

//-- HELP PANEL
function setHelpPanel() {
	$('#help_pane')
		.css('margin-left', ($('#draw-canvas').width()/2 - 190))
		.css('margin-top', ($('#draw-canvas').height()/2 - 175));

	$('#title').css('width',$('#title').width());

	$('.close').hover(
	  function() {
		$('#close').css("display", "none");
		$('#close_on').css("display", "inline");
	  }, function() {
		$('#close_on').css("display", "none");
		$('#close').css("display", "inline");
	  }
	);
} 

function helpSwitch() {
  if($('#help_on').css("visibility")=='hidden') {
    $('#help').css("visibility", "hidden");
    $('#help_on').css("visibility", "visible");
    $('#help_on').css("opacity","1.0");
    $('#help_pane').css("visibility", "visible");
  }
  else{
    $('#help_on').css("visibility", "hidden");
    $('#help').css("visibility", "visible");
    $('#help').css("opacity","1.0");
    $('#help_pane').css("visibility", "hidden");
  }
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

	if (typeof vv === 'undefined') {
		setTimeout(startupGrid, 50);
	}
	else {
		addGrid("model_1",options.widgets.grid.step);	
	}
}


$(document).ready(function(){
	init3dhop();
	setup3dhop();
	setHelpPanel();
	
	if(options.widgets.grid.atStartup)
		setTimeout(startupGrid, 100);	// grid shows up at startup

});
</script>
</html>
