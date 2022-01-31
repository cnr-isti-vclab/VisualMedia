
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
	<div class="container1">
		<iframe id="media" allowfullscreen allow="fullscreen" style="border-width:0px" class="relight" src="3d.php"></iframe>

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

class Navigation extends Config {
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
let navigation = new Navigation('#media', 'update.php'); //'options.json'); 
//------------------------------------------


function setTurntable(){
	if(navigation.options.trackball.type === "TurntablePanTrackball") return;	//ignore if same type
	
	navigation.options.trackball.type = default_ariadne.trackball.type;
	navigation.options.trackball.trackOptions = default_ariadne.trackball.trackOptions;
	
	// turntable trackball must re-enable some components disabled by sphere, just in case
	navigation.options.widgets.grid.atStartup = true;
	
	navigation.save();
	navigation.update();
}
function setSphere(){
	if(navigation.options.trackball.type === "SphereTrackball") return;	//ignore if same type
	
	navigation.options.trackball.type = "SphereTrackball";
	navigation.options.trackball.trackOptions = {};
	navigation.options.trackball.trackOptions.startMatrix = SglMat4.identity();
	navigation.options.trackball.trackOptions.startDistance = 1.5;
	
	// sphere trackball must disable some components
	navigation.options.widgets.grid.atStartup = false;
/*	

	"widgets": {
		"grid" : {
			"step" : 0.0,	
			"atStartup" : true
		}
	},	
*/

	navigation.save();
	navigation.update();
}


//----------------------------------------------------------------------------------
function updatingFOV(newVal){
	document.querySelector('#labelFOV').innerHTML = newVal + "°";
	window.frames[0].presenter._scene.space.cameraFOV = newVal;
	window.frames[0].presenter.repaint();	
}
function changedFOV(newVal){
	navigation.options.space.cameraFOV = newVal;
	navigation.save();
	navigation.update();	
}
function resetFOV(){
	navigation.options.space.cameraFOV = default_ariadne.space.cameraFOV;
	navigation.save();
	navigation.update();
}
//----------------------------------------------------------------------------------
function useCurrentView(){
	var track = window.frames[0].presenter.getTrackballPosition();
	
	if(navigation.options.trackball.type === "TurntablePanTrackball") {
		navigation.options.trackball.trackOptions.startPhi      = track[0];
		navigation.options.trackball.trackOptions.startTheta    = track[1];
		navigation.options.trackball.trackOptions.startPanX     = track[2];
		navigation.options.trackball.trackOptions.startPanY     = track[3];
		navigation.options.trackball.trackOptions.startPanZ     = track[4];
		navigation.options.trackball.trackOptions.startDistance = track[5];
	}
	else if(navigation.options.trackball.type === "SphereTrackball") {
		navigation.options.trackball.trackOptions.startMatrix = track;
		navigation.options.trackball.trackOptions.startDistance = window.frames[0].presenter.trackball._distance;		
	}
	navigation.save();
	navigation.update();	
}
function resetInitialView(){
	
	if(navigation.options.trackball.type === "TurntablePanTrackball") {	
		navigation.options.trackball.trackOptions.startPhi      = default_ariadne.trackball.trackOptions.startPhi;
		navigation.options.trackball.trackOptions.startTheta    = default_ariadne.trackball.trackOptions.startTheta;
		navigation.options.trackball.trackOptions.startPanX     = default_ariadne.trackball.trackOptions.startPanX;
		navigation.options.trackball.trackOptions.startPanY     = default_ariadne.trackball.trackOptions.startPanY;
		navigation.options.trackball.trackOptions.startPanZ     = default_ariadne.trackball.trackOptions.startPanZ;
		navigation.options.trackball.trackOptions.startDistance = default_ariadne.trackball.trackOptions.startDistance;
	}
	else if(navigation.options.trackball.type === "SphereTrackball") {
		
	}	
	navigation.save();
	navigation.update();	
}
//----------------------------------------------------------------------------------


</script>
</html>
