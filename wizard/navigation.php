<div class="col-12">
	<h5>Object Manipulation</h5>
	<p><span id="trackname"></span></p>
	<div class="d-flex" style="gap:10px">
		<button style="flex-grow:1; flex-basis:0;" class="btn btn-secondary" id="bt_setTurntable" onclick="navigation_config.setTurntable();">TURNTABLE</button>
		<button style="flex-grow:1; flex-basis:0;" class="btn btn-secondary" id="bt_setSphere" onclick="navigation_config.setSphere();">SPHERE</button>				
	</div>

	<hr/>

	<h5>
		<img class="restore" title="Reset initial view" src="restore.svg" onclick="navigation_config.resetInitialView();"> Initial View
	</h5>
	<button style="width:100%" class="btn btn-secondary btn-sm" onclick="navigation_config.useCurrentView();">Use Current View</button>
		
	<hr/>

	<h5>
		<img class="restore" title="Reset FOV" src="restore.svg" onclick="navigation_config.resetFOV();"> Field of View
	</h5>
	<div style="display:flex">
		<span class="h5 m-1" id="labelFOV">---</span>
		<input style="flex-grow:1" type="range" min="5" max="85" step="1.0" id="rangeFOV" onInput="updatingFOV(this.value);" onChange="navigation_config.setFOV(this.value);">
	</div>
	<div style="display:flex; gap:7px;">
		<button style="flex-grow:1; flex-basis:0;" class="btn btn-secondary btn-sm" title="15°" onclick="navigation_config.setFOV(15);">Tele</button>
		<button style="flex-grow:1; flex-basis:0;" class="btn btn-secondary btn-sm" title="35°" onclick="navigation_config.setFOV(35);">Human</button>
		<button style="flex-grow:1; flex-basis:0;" class="btn btn-secondary btn-sm" title="80°" onclick="navigation_config.setFOV(80);">FishEye</button>
	</div>

	<div class="mt-4">
		<div class="row">
			<p class="col-6">Start with view:</p>
			<div class="col-6">
				<select id="i_startCamera"  class="form-control  form-control-sm"  onchange="navigation_config.setStartProjection(this.value);">
					<option value="perspective">Perspective</option>
					<option value="orthographic">Orthographic</option>
				</select>
			</div>
		</div>
		<p><img src="skins/dark/orthographic.png" width="24px"> 
			<input type="checkbox" id="i_toggleOrtho" onchange="navigation_config.setTool('orthographic', this.checked);" checked>
			Perspective/Ortho toggle</input>
		</p>
	</div>
</div>



<script>

class NavigationConfig extends Config {
	constructor(frame, options) {
		super(frame, options);

		//SpiderGL.openNamespace();
	}

	update() {
		let options = Config.options;
		
		// trackball
		let tname = "error";
		switch(Config.options.trackball.type) {
		case "TurntablePanTrackball": 
			tname = "Turntable with Panning";
			document.getElementById("bt_setTurntable").classList.add("btn-info");
			document.getElementById("bt_setSphere").classList.remove("btn-info");
			break;
		case "SphereTrackball": 
			tname = "Spherical Trackball";
			document.getElementById("bt_setTurntable").classList.remove("btn-info");
			document.getElementById("bt_setSphere").classList.add("btn-info");
			break;
		}
		document.querySelector('#trackname').innerHTML = tname;
		
		//fov
		document.querySelector('#rangeFOV').value = Config.options.space.cameraFOV;
		document.querySelector('#labelFOV').innerHTML = Config.options.space.cameraFOV + "°";
		
		document.getElementById("i_toggleOrtho").checked = this.tools().includes("orthographic");

		document.getElementById("i_startCamera").value = Config.options.space.cameraType;		
	}

	reset() {
		super.reset();
		this.update();
	}

	setTurntable(){
		let trackball = Config.options.trackball;
		if(trackball.type === "TurntablePanTrackball") return;	//ignore if same type
		
		trackball.type = "TurntablePanTrackball"
		trackball.trackOptions = default_ariadne.trackball.trackOptions;
		
		// enabling/disabling visual components
		Config.options.widgets.grid.atStartup = true;
		Config.options.widgets.trackSphere.atStartup = false;
		
		this.save();
		this.update();
	}

	setSphere() {
		let trackball = Config.options.trackball;
		if(trackball.type === "SphereTrackball") return;	//ignore if same type
		
		trackball.type = "SphereTrackball";
		trackball.trackOptions = {
			startMatrix: SglMat4.identity(),
			startPanX: 0.0,
			startPanY: 0.0,
			startPanZ: 0.0,
			startDistance: 1.5
		};

		// enabling/disabling visual components
		Config.options.widgets.grid.atStartup = false;
		Config.options.widgets.trackSphere.atStartup = true;
		Config.options.widgets.compass.atStartup = false;
		this.save();
	}

	useCurrentView() {
		var track = window.frames[0].presenter.getTrackballPosition();
		let trackball = Config.options.trackball;
		let trackOptions = trackball.trackOptions;
	
		if(trackball.type === "TurntablePanTrackball") {
			trackball.trackOptions = {...trackOptions, 
				startPhi: track[0],
				startTheta: track[1],
				startPanX: track[2],
				startPanY: track[3],
				startPanZ: track[4],
				startDistance: track[5]
			};
		}
		else if(trackball.type === "SphereTrackball") {
			trackball.trackOptions = {...trackOptions, 
				startMatrix: track[0],
				startPanX: track[1],
				startPanY: track[2],
				startPanZ: track[3],
				startDistance: track[4]
			};
		}
		this.save();
		this.update();	
	}

	resetInitialView() {
			let trackball = Config.options.trackball;
			let trackOptions = trackball.trackOptions;

		if(trackball.type === "TurntablePanTrackball") {
			const { startPhi, startTheta, startPanX, startPanY, startPanZ, startDistance } = default_ariadne.trackball.trackOptions;
			trackball.trackOptions = { ...trackOptions, startPhi, startTheta, startPanX, startPanY, startPanZ, startDistance };

		} else if(trackball.type === "SphereTrackball") {
			trackball.trackOptions = {
				startMatrix: SglMat4.identity(),
				startPanX: 0.0,
				startPanY: 0.0,
				startPanZ: 0.0,
				startDistance: 1.5
			};
		}
		this.save();
			this.update();	
	}

	setFOV(newVal){
		Config.options.space.cameraFOV = newVal;
		this.save();
	}

	resetFOV(){
		Config.options.space.cameraFOV = default_ariadne.space.cameraFOV;
		this.save();
	}

	setStartProjection(value){
		Config.options.space.cameraType = value;
		this.save();
	}

	reset() {
		super.reset();
	}
}

let navigation_config = new NavigationConfig(); 


function updatingFOV(newVal){
	document.querySelector('#labelFOV').innerHTML = newVal + "°";
	window.frames[0].presenter._scene.space.cameraFOV = newVal;
	window.frames[0].presenter.repaint();	
}


</script>
</html>
