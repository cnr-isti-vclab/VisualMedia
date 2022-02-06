
<div>		
	<h5>
	Object Manipulation
	</h5>
	<div class="m-1">
	<center>
	<span id="trackname"></span>
	<center>
	<center>
		<button class="btn btn-primary m-1" onclick="navigation_config.setTurntable();">TURNTABLE</button>
		<button class="btn btn-primary m-1" onclick="navigation_config.setSphere();">SPHERE</button>				
	</center>
	</div>	

<hr/>

	<h5>
	<img class="m-1" width="25px" src="skins/icons/restore.png" title="Reset Initial View" onclick="navigation_config.resetInitialView();"> Initial View
	</h5>
	<div class="m-1">
	<center>
		<button class="btn btn-primary btn-sm m-1" onclick="navigation_config.useCurrentView();">Use Current View</button>
	</center>
	</div>	
	
<hr/>

	<h5>
	<img class="m-1" width="25px" src="skins/icons/restore.png" title="Reset FOV" onclick="navigation_config.resetFOV();"> Field of View
	</h5>
	<div class="m-1">
	<center>
	<span class="h5 m-1" id="labelFOV">---</span>
	<input type="range" min="5" max="85" step="1.0" id="rangeFOV" onInput="updatingFOV(this.value);" onChange="navigation_config.setFOV(this.value);">
	</br>
	<button class="btn btn-primary btn-sm m-1" title="15°" onclick="navigation_config.setFOV(15);">Tele</button>
	<button class="btn btn-primary btn-sm m-1" title="35°" onclick="navigation_config.setFOV(35);">Human</button>
	<button class="btn btn-primary btn-sm m-1" title="80°" onclick="navigation_config.setFOV(80);">FishEye</button>
	</center>
	</div>
	
<hr/>

</div>

<script>

class NavigationConfig extends Config {
	constructor(frame, options) {
		super(frame, options);

		//SpiderGL.openNamespace();
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

	setTurntable(){
		let trackball = this.options.trackball;
		if(trackball.type === "TurntablePanTrackball") return;	//ignore if same type
		
		trackball.type = "TurntablePanTrackball"
		trackball.trackOptions = default_ariadne.trackball.trackOptions;
		
		// enabling/disabling visual components
		this.options.widgets.grid.atStartup = true;
		this.options.widgets.trackSphere.atStartup = false;
		
		this.save();
		this.update();
	}

	setSphere() {
		let trackball = this.options.trackball;

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
		this.options.widgets.grid.atStartup = false;
		this.options.widgets.trackSphere.atStartup = true;
		
		this.save();
		this.update();
	}

	setFOV(newVal){
		this.options.space.cameraFOV = newVal;
		this.save();
		this.update();
	}


	resetFOV(){
		this.options.space.cameraFOV = default_ariadne.space.cameraFOV;
		this.save();
		this.update();
	}

	useCurrentView() {
		var track = window.frames[0].presenter.getTrackballPosition();
		let trackball = this.options.trackball;
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
			let trackball = this.options.trackball;
			let  trackOptions = trackball.trackOptions;

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

	reset() {
		super.reset();
		this.update();
	}
}

let navigation_config = new NavigationConfig('#media', 'update.php'); 


function updatingFOV(newVal){
	document.querySelector('#labelFOV').innerHTML = newVal + "°";
	window.frames[0].presenter._scene.space.cameraFOV = newVal;
	window.frames[0].presenter.repaint();	
}


</script>
</html>
