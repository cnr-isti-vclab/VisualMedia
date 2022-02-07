		<div id="viewControls" class="border d-none p-2" style="position:absolute; right:400px; top:10;">
			<center>
			<h5>View Scene From:</h5>			
			<table>
			<tr><td></td><td><button id="vtop" class="btn btn-sm btn-secondary w-100 vbutton" onclick="viewFrom('top');">ABOVE</button></td><td></td><td></td></tr>
			<tr><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vleft" onclick="viewFrom('left');">LEFT</button></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vfront" onclick="viewFrom('front');">FRONT</button></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vright" onclick="viewFrom('right');">RIGHT</button></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vback" onclick="viewFrom('back');">BACK</button></td></tr>
			<tr><td></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vbottom" onclick="viewFrom('bottom');">BELOW</button></td><td></td><td></td></tr> 
			</table>
			</center>
		</div>  

<div id="straightening_panel">
	<h5>
	<img class="m-1" width="25px" src="skins/icons/restore.png" onclick="model_config.resetOrientation();"> Model Orientation
	</h5>			
	<div id="sm_instructions">
		<p>This is how the viewer will look and navigate. If the model is not in the correct orientation, you can re-orient it using this button:</p>	
		<button class="btn btn-secondary btn-block" id="smStart" onclick="model_config.startStraightMode()">Straighten your model</button>
	</div>
	<div class="border d-none" id="smControls">
		<div class="m-1">
		<p>Select a view using the buttons: you'll see how the scene will look from that direction.</br>
		Orient the model to match the chosen view by clicking and dragging in the 3D panel or by using the buttons below.</p>
		</div>
		<div class="m-1">
		
		<center>
		<table border="0">
			<tr>
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('y',-90.0);">90</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('y',-15.0);">15</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('y', -5.0);">5<img width="20px" src="skins/icons/lf.png"/></button>
			</div>
			<img width="50px" src="skins/icons/roty.png" title="HORIZONTAL"/>					
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('y',  5.0);"><img width="20px" src="skins/icons/rt.png"/>5</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('y', 15.0);">15</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('y', 90.0);">90</button>
			</div>					
			</tr>
			<tr>
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('x',-90.0);">90</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('x',-15.0);">15</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('x', -5.0);">5<img width="20px" src="skins/icons/up.png"/></button>
			</div>
			<img width="50px" src="skins/icons/rotx.png" title="VERTICAL"/>
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('x',  5.0);"><img width="20px" src="skins/icons/dn.png"/>5</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('x', 15.0);">15</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('x', 90.0);">90</button>
			</div>
			</tr>
			<tr>
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('z', 90.0);">90</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('z', 15.0);">15</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('z',  5.0);">5<img width="20px" src="skins/icons/rl.png"/></button>
			</div>
			<img width="50px" src="skins/icons/rotz.png" title="ROLL"/>
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('z', -5.0);"><img width="20px" src="skins/icons/rr.png"/>5</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('z',-15.0);">15</button>
				<button class="btn btn-secondary btn-sm" onclick="rotView('z',-90.0);">90</button>
			</div>
			</tr>					
		</table>			
		</center>
		
		<!--
		</hr>
		<center>
		<table border="0">
			<tr>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('y',-90.0);">-90</button></td>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('y',-15.0);">-15</button></td>				 
			<td align="center"><img width="50px" src="skins/icons/roty.png" title="HORIZONTAL"/></td>			 
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('y', 15.0);">+15</button></td>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('y', 90.0);">+90</button></td>	 
			</tr>
			<tr>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('x',-90.0);">-90</button></td>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('x',-15.0);">-15</button></td>
			<td align="center"><img width="50px" src="skins/icons/rotx.png" title="VERTICAL"/></td>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('x', 15.0);">+15</button></td>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('x', 90.0);">+90</button></td>	 
			</tr>
			<tr>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('z', 90.0);">-90</button></td>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('z', 15.0);">-15</button></td>
			<td align="center"><img width="50px" src="skins/icons/rotz.png" title="ROLL"/></td>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('z',-15.0);">+15</button></td>
			<td align="center"><button class="btn btn-secondary btn-sm" onclick="rotView('z',-90.0);">+90</button></td>	 
			</tr>
		</table>			
		</center>
		-->
		
		</hr>		
		</div>			
		<div class="m-1 text-right">
			<button class="btn btn-sm btn-danger" onclick="model_config.cancelStraightMode();">CANCEL</button>
			<button class="btn btn-sm btn-success" onclick="model_config.applyStraightMode()">APPLY</button>
		</div>
	</div>
</div>

<hr/>

	

<script>
class ModelConfig extends Config {
	constructor(frame, options) {
		super(frame, options);
		SpiderGL.openNamespace();
	}

	update() {
	}
	
	startStraightMode(){
		let frame = window.frames[0];
		presenter = frame.presenter; // get current presenter instance
		frame.document.getElementById("toolbar").classList.add("d-none");
		frame.document.getElementById("panel_widgets").classList.add("d-none");
		
		document.getElementById("sm_instructions").classList.add("d-none");
		document.getElementById("smControls").classList.remove("d-none");
		document.getElementById("viewControls").classList.remove("d-none");
		presenter.setCameraOrthographic();

		this.reference = new Reference(presenter);

		frame.onTrackballUpdate = () => this.reference.update();
		this.reference.show();

		frame.closeAllTools();

		viewFrom("front");
		frame.removeGrid(); // remove base grid, if any
		frame.removeTrackSphere(); // remove track sphere, if any
		presenter.setTrackballLock(true);
		//setup mini sphere-trackball manipulator
		frame.document.getElementById("draw-canvas").addEventListener('mousedown', onDown);
		frame.document.getElementById("draw-canvas").addEventListener('mousemove', onMove);
	}

	reset() {
		cancelStraightMode();
		super.reset();
	}

	applyStraightMode(){
		this.endStraightInterface();	
		let newmatrix = window.frames[0].presenter._scene.modelInstances["model_1"].transform.matrix;
		Config.options.scene[0].matrix = newmatrix;
		model_config.save();	
	}

	cancelStraightMode(){
		this.endStraightInterface();
		model_config.refresh();	
	}

	endStraightInterface(){
		document.getElementById("sm_instructions").classList.remove("d-none");
		document.getElementById("smControls").classList.add("d-none");
		document.getElementById("viewControls").classList.add("d-none");
		
		//remove mini sphere-trackball manipulator
		window.frames[0].document.getElementById("draw-canvas").removeEventListener('mousedown', onDown);
		window.frames[0].document.getElementById("draw-canvas").removeEventListener('mousemove', onMove);	
	}

	resetOrientation(){
		this.endStraightInterface();	
		let newmatrix = SglMat4.identity();
		Config.options.scene[0].matrix = newmatrix;
		this.save();
	}



}

let model_config = new ModelConfig('#media', 'update.php'); 

var presenter = null;	// current presenter instance from iframe



//-------------------------------------------------------------------------
// mini sphere-trackball 
var trackV1, trackV2;

function onDown(e){
	let minSize = Math.min(e.target.width,e.target.height)
	let mx = (e.clientX - (e.target.width/2.0)) / minSize;
	let my = ((e.target.height-e.clientY) - (e.target.height/2.0)) / minSize;
	trackV1 = projectPoint(mx,my);
}
function onMove(e){
	if(e.buttons != 1) return;

	let minSize = Math.min(e.target.width,e.target.height);		
	let mx = (e.clientX - (e.target.width/2.0)) / minSize;
	let my = ((e.target.height-e.clientY) - (e.target.height/2.0)) / minSize;
	trackV2 = projectPoint(mx,my);

	let axis   = SglVec3.cross(trackV1, trackV2); //axis of rotation
	let angle  = SglVec3.length(axis);            //angle of rotation
	if(Math.abs(angle) < 0.0001)
		return;
	let rotMat = SglMat4.rotationAngleAxis(angle*2.0, axis); // *2.0 makes it spin faster :)
	let newmatrix = SglMat4.mul(rotMat, presenter._scene.modelInstances["model_1"].transform.matrix);
	presenter._scene.modelInstances["model_1"].transform.matrix = newmatrix;
	presenter.repaint();

	trackV1 = trackV2;
}
function projectPoint(x, y){
	let r = 0.5;
	let z = 0.0;
	let d = Math.sqrt(x*x + y*y);

	if (d < (r * 0.70710678118654752440)) { /* Inside sphere */
		z = Math.sqrt(r*r - d*d);
	} else { /* On hyperbola */
		let t = r / 1.41421356237309504880;
		z = t*t / d;
	}
	let v = [x, y, z, 1.0];
	let track = presenter.getTrackballPosition();

	// transform to consider current 3dhop trackball view			
	if(Config.options.trackball.type === "TurntablePanTrackball"){
		v = SglMat4.mul4(SglMat4.rotationAngleAxis(sglDegToRad(track[1]), [-1.0, 0.0, 0.0]), v);
		v = SglMat4.mul4(SglMat4.rotationAngleAxis(sglDegToRad(track[0]), [0.0, 1.0, 0.0]), v);		

	} else if (Config.options.trackball.type === "SphereTrackball"){
		v = SglMat4.mul4(SglMat4.inverse(track[0]), v);		
	}
	return [v[0],v[1],v[2]];
}
//-------------------------------------------------------------------------

function rotView(axis, delta){
	var track = presenter.getTrackballPosition();
	var rotAxis;
	switch(axis) {
        case "x": rotAxis = [1.0, 0.0, 0.0, 1.0];
            break;
        case "y": rotAxis = [0.0, 1.0, 0.0, 1.0];
            break;
        case "z": rotAxis = [0.0, 0.0, 1.0, 1.0];
            break;
	}
	
	// transform to consider current 3dhop trackball view			
	if(Config.options.trackball.type === "TurntablePanTrackball"){
		rotAxis = SglMat4.mul4(SglMat4.rotationAngleAxis(sglDegToRad(track[1]), [-1.0, 0.0, 0.0]), rotAxis);
		rotAxis = SglMat4.mul4(SglMat4.rotationAngleAxis(sglDegToRad(track[0]), [0.0, 1.0, 0.0]), rotAxis);
	}		
	else if (Config.options.trackball.type === "SphereTrackball"){
		rotAxis = SglMat4.mul4(SglMat4.inverse(track[0]), rotAxis);		
	}	
	var rotMat = SglMat4.rotationAngleAxis(sglDegToRad(delta), rotAxis);
	var newmatrix = SglMat4.mul(rotMat, presenter._scene.modelInstances["model_1"].transform.matrix);
	presenter._scene.modelInstances["model_1"].transform.matrix = newmatrix;
	presenter.repaint();
}
function rotAbs(axis,delta){
	var rotAxis;
	switch(axis) {
        case "x": rotAxis = [1.0, 0.0, 0.0, 1.0];
            break;
        case "y": rotAxis = [0.0, 1.0, 0.0, 1.0];
            break;
        case "z": rotAxis = [0.0, 0.0, 1.0, 1.0];
            break;
	}
	var rotMat = SglMat4.rotationAngleAxis(sglDegToRad(delta), rotAxis);
	var newmatrix = SglMat4.mul(rotMat, presenter._scene.modelInstances["model_1"].transform.matrix);
	presenter._scene.modelInstances["model_1"].transform.matrix = newmatrix;	
	presenter.repaint();
}


class Reference {
	constructor(presenter) {
		this.presenter = presenter;
	}

	show() {
		let rad = 0.6 / this.presenter.sceneRadiusInv;
		let numDiv = 10;

		let grid = [];
		for (let i = -numDiv; i <= numDiv; i++) {
			let f = i/numDiv;
			grid.push([0, f, -1], [0, f, 1], [0, -1, f], [0, 1, f]);
		}

		let arrow = [ [0, 0, 0], [1.3, 0, 0], [1.3, 0, 0], [1.1, 0, 0.15], [1.3, 0, 0], [1.1, 0, -0.15] ];
		let x = [ [1.4, 0, -0.1], [1.6, 0, 0.1], [1.4, 0, 0.1], [1.6, 0, -0.1] ];
		let y = [ [0, 1.4, 0], [0, 1.5, 0],  [0, 1.5, 0], [0.1, 1.6, 0],  [0, 1.5, 0], [-0.1, 1.6, 0] ];
		let z = [ [0, 0.1, 1.4], [0, -0.1, 1.6], [0, 0.1, 1.4], [0, 0.1, 1.6], [0, -0.1, 1.6], [0, -0.1, 1.4]];


		for(let point of [...grid, ...arrow, ...x, ...y, ...z])
			for(let c of point)
				c *= rad;

		this.refgridX = this.presenter.createEntity("refgridX", "lines", [...grid, ...arrow, ...x]);
		this.refgridX.color = [0.9, 0.5, 0.5, 1.0];
		this.refgridX.zOff = 0.0;

		rotate([...grid, ...arrow]);

		this.refgridY = this.presenter.createEntity("refgridZ", "lines", [...grid, ...arrow, ...y]);
		this.refgridY.color = [0.5, 0.9, 0.5, 1.0];
		this.refgridY.zOff = 0.0;

		rotate([...grid, ...arrow]);

		this.refgridZ = this.presenter.createEntity("refgridY", "lines", [...grid, ...arrow, ...z]);
		this.refgridZ.color = [0.5, 0.5, 0.9, 1.0];
		this.refgridZ.zOff = 0.0;

		function rotate(lines) {
			for(let point of lines) {
				point.unshift(point.pop()); //rotate
			}
		}

		this.presenter.repaint();
	}

	delete() {
		for(let e of ['refgridX', 'refgridY', 'refgridZ'])
			this.presenter.deleteEntity(e);
	}

	update(trackState){
		if(!this.refgridX) return;
	
		let matrix = SglMat4.translation(this.presenter.sceneCenter);
		this.refgridX.transform.matrix = matrix;
		this.refgridY.transform.matrix = matrix;
		this.refgridZ.transform.matrix = matrix;
	}
}





//-------------------------------------------------------------------------
function viewFrom(direction){
	document.querySelectorAll('.vbutton').forEach(el => {el.classList.remove('btn-primary'); el.classList.add('btn-secondary');});
	
	let presenter = window.frames[0].presenter; // get current presenter instance
	let distance = 1.4;
	
	let trackType = Config.options.trackball.type;

	let angles = {
		'front' : [0, 0],
		'back'  : [180, 0],
		'top'   : [0, 90],
		'bottom': [0, -90],
		'left'  : [-90, 0],
		'right' : [90, 0]
	};
	let axes = {
		'front' : [ 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1 ],
		'back'  : [-1, 0, 0, 0, 0, 1, 0, 0, 0, 0,-1, 0, 0, 0, 0, 1 ],
		'top'   : [ 1, 0, 0, 0, 0, 0, 1, 0, 0,-1, 0, 0, 0, 0, 0, 1 ],
		'bottom': [ 1, 0, 0, 0, 0, 0,-1, 0, 0, 1, 0, 0, 0, 0, 0, 1 ],
		'left'  : [ 0, 0,-1, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1 ],
		'right' : [ 0, 0, 1, 0, 0, 1, 0, 0,-1, 0, 0, 0, 0, 0, 0, 1 ]
	}

	if(trackType === "TurntablePanTrackball") {
		presenter.animateToTrackballPosition([...angles[direction], 0.0, 0.0, 0.0, distance]);
	} else {
		presenter.animateToTrackballPosition([...axes[direction], 0.0, 0.0, 0.0, distance]);
	}

	let div = document.querySelector(`#v${direction}`);
	div.classList.remove("btn-secondary");
	div.classList.add("btn-primary");
}


</script>
</html>
