
<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!--BOOTSTRAP STYLE-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
<!--STYLE-->
<link rel="stylesheet" href="stylesheet/style.css">

<!--SPIDERGL-->
<script type="text/javascript" src="js/spidergl.js"></script>

</head>

<body>
	<div class="vms_container">
		<iframe id="media" allowfullscreen allow="fullscreen" style="border-width:0px" class="vms" src="3d.php"></iframe>

		<div class="panel">

		<hr/>
		
			<h5>
			Object Manipulation
			</h5>
			<div class="m-1">
			<center>
			<span id="trackname"></span>
			<center>
			<center>
				<button class="btn btn-primary m-1" onclick="setTurntable();">TURNTABLE</button>
				<button class="btn btn-primary m-1" onclick="setSphere();">SPHERE</button>				
			</center>
			</div>	

		<hr/>
		
			<h5>
			<img class="m-1" width="25px" src="skins/icons/restore.png" title="Reset Initial View" onclick="resetInitialView();"> Initial View
			</h5>
			<div class="m-1">
			<center>
				<button class="btn btn-primary btn-sm m-1" onclick="useCurrentView();">Use Current View</button>
			</center>
			</div>	
			
		<hr/>

			<h5>
			<img class="m-1" width="25px" src="skins/icons/restore.png" title="Reset FOV" onclick="resetFOV();"> Field of View
			</h5>
			<div class="m-1">
			<center>
			<span class="h5 m-1" id="labelFOV">---</span>
			<input type="range" min="5" max="85" step="1.0" id="rangeFOV" onInput="updatingFOV(this.value);" onChange="changedFOV(this.value);">
			</br>
			<button class="btn btn-primary btn-sm m-1" title="15°" onclick="changedFOV(15);">Tele</button>
			<button class="btn btn-primary btn-sm m-1" title="35°" onclick="changedFOV(35);">Human</button>
			<button class="btn btn-primary btn-sm m-1" title="80°" onclick="changedFOV(80);">FishEye</button>
			</center>
			</div>
			
		<hr/>
		
		</div>

	</div>
</body>

<script src="config.js"></script>
<script>

class NavigationConfig extends Config {
	constructor(frame, options) {
		super(frame, options);

		SpiderGL.openNamespace();
	}

	update() {
		let options = this.options;
		
		// trackball
		let tname = "error";
		switch(options.trackball.type) {
        case "TurntablePanTrackball": tname = "Turntable with Panning";
            break;
        case "SphereTrackball": tname = "Spherical Trackball";
            break;
		}
		document.querySelector('#trackname').innerHTML = tname;
		
		//fov
		document.querySelector('#rangeFOV').value = options.space.cameraFOV;
		document.querySelector('#labelFOV').innerHTML = options.space.cameraFOV + "°";
	}
	
	reset() {
		super.reset();
		this.update();
	}
}

//------------------------------------------
// config object
//------------------------------------------
let navigation_config = new NavigationConfig('#media', 'update.php'); //'options.json'); 
//------------------------------------------


function setTurntable(){
	if(navigation_config.options.trackball.type === "TurntablePanTrackball") return;	//ignore if same type
	
	navigation_config.options.trackball.type = default_ariadne.trackball.type;
	navigation_config.options.trackball.trackOptions = default_ariadne.trackball.trackOptions;
	
	// enabling/disabling visual components
	navigation_config.options.widgets.grid.atStartup = true;
	navigation_config.options.widgets.trackSphere.atStartup = false;
	
	navigation_config.save();
	navigation_config.update();
}
function setSphere(){
	if(navigation_config.options.trackball.type === "SphereTrackball") return;	//ignore if same type
	
	navigation_config.options.trackball.type = "SphereTrackball";
	navigation_config.options.trackball.trackOptions = {};
	navigation_config.options.trackball.trackOptions.startMatrix = SglMat4.identity();
	//navigation_config.options.trackball.trackOptions.startMatrix = SglMat4.mul(SglMat4.rotationAngleAxis(sglDegToRad(-25.0), [0.0, 1.0, 0.0]), SglMat4.rotationAngleAxis(sglDegToRad(25.0), [1.0, 0.0, 0.0])); 
	navigation_config.options.trackball.trackOptions.startPanX = 0.0;
	navigation_config.options.trackball.trackOptions.startPanY = 0.0;
	navigation_config.options.trackball.trackOptions.startPanZ = 0.0;
	navigation_config.options.trackball.trackOptions.startDistance = 1.5;

	// enabling/disabling visual components
	navigation_config.options.widgets.grid.atStartup = false;
	navigation_config.options.widgets.trackSphere.atStartup = true;
	
	navigation_config.save();
	navigation_config.update();
}


//----------------------------------------------------------------------------------
function updatingFOV(newVal){
	document.querySelector('#labelFOV').innerHTML = newVal + "°";
	window.frames[0].presenter._scene.space.cameraFOV = newVal;
	window.frames[0].presenter.repaint();	
}
function changedFOV(newVal){
	navigation_config.options.space.cameraFOV = newVal;
	navigation_config.save();
	navigation_config.update();	
}
function resetFOV(){
	navigation_config.options.space.cameraFOV = default_ariadne.space.cameraFOV;
	navigation_config.save();
	navigation_config.update();
}
//----------------------------------------------------------------------------------
function useCurrentView(){
	var track = window.frames[0].presenter.getTrackballPosition();
	
	if(navigation_config.options.trackball.type === "TurntablePanTrackball") {
		navigation_config.options.trackball.trackOptions.startPhi      = track[0];
		navigation_config.options.trackball.trackOptions.startTheta    = track[1];
		navigation_config.options.trackball.trackOptions.startPanX     = track[2];
		navigation_config.options.trackball.trackOptions.startPanY     = track[3];
		navigation_config.options.trackball.trackOptions.startPanZ     = track[4];
		navigation_config.options.trackball.trackOptions.startDistance = track[5];
	}
	else if(navigation_config.options.trackball.type === "SphereTrackball") {
		navigation_config.options.trackball.trackOptions.startMatrix = track[0];
		navigation_config.options.trackball.trackOptions.startPanX = track[1];
		navigation_config.options.trackball.trackOptions.startPanY = track[2];
		navigation_config.options.trackball.trackOptions.startPanZ = track[3];		
		navigation_config.options.trackball.trackOptions.startDistance = track[4];
	}
	navigation_config.save();
	navigation_config.update();	
}
function resetInitialView(){
	if(navigation_config.options.trackball.type === "TurntablePanTrackball") {	
		navigation_config.options.trackball.trackOptions.startPhi      = default_ariadne.trackball.trackOptions.startPhi;
		navigation_config.options.trackball.trackOptions.startTheta    = default_ariadne.trackball.trackOptions.startTheta;
		navigation_config.options.trackball.trackOptions.startPanX     = default_ariadne.trackball.trackOptions.startPanX;
		navigation_config.options.trackball.trackOptions.startPanY     = default_ariadne.trackball.trackOptions.startPanY;
		navigation_config.options.trackball.trackOptions.startPanZ     = default_ariadne.trackball.trackOptions.startPanZ;
		navigation_config.options.trackball.trackOptions.startDistance = default_ariadne.trackball.trackOptions.startDistance;
	}
	else if(navigation_config.options.trackball.type === "SphereTrackball") {
		navigation_config.options.trackball.trackOptions.startMatrix = SglMat4.identity();
		navigation_config.options.trackball.trackOptions.startPanX = 0.0;
		navigation_config.options.trackball.trackOptions.startPanY = 0.0;
		navigation_config.options.trackball.trackOptions.startPanZ = 0.0;
		navigation_config.options.trackball.trackOptions.startDistance = 1.5;
	}	
	navigation_config.save();
	navigation_config.update();	
}
//----------------------------------------------------------------------------------


</script>
</html>
