
		
<!--		<div id="viewControls" class="d-none" style="position:absolute; right:400px; top:0;">
			<h5>View Scene From:</h5>			
			<center>
			<table>
			<tr><td></td><td><button id="vtop" class="btn btn-sm btn-secondary w-100 vbutton" onclick="viewFrom('top');">ABOVE</button></td><td></td><td></td></tr>
			<tr><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vleft" onclick="viewFrom('left');">LEFT</button></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vfront" onclick="viewFrom('front');">FRONT</button></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vright" onclick="viewFrom('right');">RIGHT</button></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vback" onclick="viewFrom('back');">BACK</button></td></tr>
			<tr><td></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vbottom" onclick="viewFrom('bottom');">BELOW</button></td><td></td><td></td></tr> 
			</table>
			</center>
		</div> -->
		
			

<div id="straightening_panel">
	<h5>
	<img class="m-1" width="25px" src="skins/icons/restore.png" onclick="resetOrientation();"> Model Orientation
	</h5>			
	
	<button class="btn btn-secondary btn-block" id="smStart" onclick="startStraightMode()">Straighten your model</button>
	<div class="border d-none" id="smControls">
		<div class="m-1">
		Select a view using the buttons.</br>
		Orient the model to match the chosen view by clicking and dragging in the 3D panel or by using the buttons below.</br>
		Apply when satisfied; Cancel to revert changes. Reset to inital pose removes all transformations, and returns to the original pose in the 3D file.
		</div>
		<div class="m-1">
		
		<center>
		<table border="0">
			<tr>
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('y',-90.0);">90</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('y',-15.0);">15</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('y', -5.0);">5<img width="20px" src="skins/icons/lf.png"/></button></td>
			</div>
			<img width="40px" src="skins/icons/roty.png" title="HORIZONTAL"/>					
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('y',  5.0);"><img width="20px" src="skins/icons/rt.png"/>5</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('y', 15.0);">15</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('y', 90.0);">90</button></td>
			</div>					
			</tr>
			<tr>
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('x',-90.0);">90</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('x',-15.0);">15</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('x', -5.0);">5<img width="20px" src="skins/icons/up.png"/></button></td>
			</div>
			<img width="40px" src="skins/icons/rotx.png" title="VERTICAL"/>
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('x',  5.0);"><img width="20px" src="skins/icons/dn.png"/>5</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('x', 15.0);">15</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('x', 90.0);">90</button></td>
			</div>
			</tr>
			<tr>
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('z', 90.0);">90</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('z', 15.0);">15</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('z',  5.0);">5<img width="20px" src="skins/icons/rl.png"/></button></td>
			</div>
			<img width="40px" src="skins/icons/rotz.png" title="ROLL"/>
			<div class="btn-group" role="group">
				<button class="btn btn-secondary btn-sm" onclick="rotView('z', -5.0);"><img width="20px" src="skins/icons/rr.png"/>5</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('z',-15.0);">15</button></td>
				<button class="btn btn-secondary btn-sm" onclick="rotView('z',-90.0);">90</button></td>
			</div>
			</tr>					
		</table>			
		</center>
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
		
	
			
		</div>			
		<div class="m-1 text-right">
			<button class="btn btn-sm btn-danger" onclick="cancelStraightMode()">CANCEL</button>
			<button class="btn btn-sm btn-success" onclick="applyStraightMode()">APPLY</button>
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
	
	reset() {
		cancelStraightMode();
		super.reset();
		this.update();
	}
}

//------------------------------------------
// config object
//------------------------------------------
let model_config = new ModelConfig('#media', 'update.php'); //'options.json'); 
//------------------------------------------

//------------------------------------------
var presenter = null;	// current presenter instance from iframe
//------------------------------------------


//-------------------------------------------------------------------------
function startStraightMode(){
	presenter = window.frames[0].presenter; // get current presenter instance
	window.frames[0].document.getElementById("toolbar").classList.add("d-none");	
	document.getElementById("smStart").classList.add("d-none");
	document.getElementById("smControls").classList.remove("d-none");
	document.getElementById("viewControls").classList.remove("d-none");
	window.frames[0].presenter.setCameraOrthographic();
	window.frames[0].onTrackballUpdate = updateReference;
	window.frames[0].closeAllTools();
	showReference();
	viewFrom("front");
	window.frames[0].removeGrid(); // remove base grid, if any
	window.frames[0].removeTrackSphere(); // remove track sphere, if any
	presenter.setTrackballLock(true);
	//setup mini sphere-trackball manipulator
	window.frames[0].document.getElementById("draw-canvas").addEventListener('mousedown', onDown);
	window.frames[0].document.getElementById("draw-canvas").addEventListener('mousemove', onMove);
}
function endStraightInterface(){
	document.getElementById("smStart").classList.remove("d-none");
	document.getElementById("smControls").classList.add("d-none");
	document.getElementById("viewControls").classList.add("d-none");
	presenter = null;
	//remove mini sphere-trackball manipulator
	window.frames[0].document.getElementById("draw-canvas").removeEventListener('mousedown', onDown);
	window.frames[0].document.getElementById("draw-canvas").removeEventListener('mousemove', onMove);	
}
function applyStraightMode(){
	endStraightInterface();	
	var newmatrix = window.frames[0].presenter._scene.modelInstances["model_1"].transform.matrix;
	model_config.options.scene[0].matrix = newmatrix;
	model_config.save();	
}
function cancelStraightMode(){
	endStraightInterface();
	model_config.refresh();	
}
function resetOrientation(){
	endStraightInterface();	
	var newmatrix = SglMat4.identity();
	model_config.options.scene[0].matrix = newmatrix;
	model_config.save();
}

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

	var axis   = SglVec3.cross(trackV1, trackV2); //axis of rotation
	var angle  = SglVec3.length(axis); //angle of rotation
	var rotMat = SglMat4.rotationAngleAxis(angle*2.0, axis); // *2.0 makes it spin faster :)
	var newmatrix = SglMat4.mul(rotMat, presenter._scene.modelInstances["model_1"].transform.matrix);
	presenter._scene.modelInstances["model_1"].transform.matrix = newmatrix;
	presenter.repaint();

	trackV1 = trackV2;
}
function projectPoint(x,y){
		var r = 0.5;
		var z = 0.0;
		var d = Math.sqrt(x*x + y*y);

		if (d < (r * 0.70710678118654752440)) { /* Inside sphere */
			z = Math.sqrt(r*r - d*d);
		}
		else { /* On hyperbola */
			let t = r / 1.41421356237309504880;
			z = t*t / d;
		}
	var v = [x, y, z, 1.0];
	var track = presenter.getTrackballPosition();
	// transform to consider current 3dhop trackball view			
	if(model_config.options.trackball.type === "TurntablePanTrackball"){
		v = SglMat4.mul4(SglMat4.rotationAngleAxis(sglDegToRad(track[1]), [-1.0, 0.0, 0.0]), v);
		v = SglMat4.mul4(SglMat4.rotationAngleAxis(sglDegToRad(track[0]), [0.0, 1.0, 0.0]), v);		
	}		
	else if (model_config.options.trackball.type === "SphereTrackball"){
		v = SglMat4.mul4(SglMat4.inverse(track[0]), v);		
	}
	return [v[0],v[1],v[2]];
}
//-------------------------------------------------------------------------

function rotView(axis,delta){
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
	if(model_config.options.trackball.type === "TurntablePanTrackball"){
		rotAxis = SglMat4.mul4(SglMat4.rotationAngleAxis(sglDegToRad(track[1]), [-1.0, 0.0, 0.0]), rotAxis);
		rotAxis = SglMat4.mul4(SglMat4.rotationAngleAxis(sglDegToRad(track[0]), [0.0, 1.0, 0.0]), rotAxis);
	}		
	else if (model_config.options.trackball.type === "SphereTrackball"){
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


//-------------------------------------------------------------------------
function showReference(){
	var rad = 0.6 / presenter.sceneRadiusInv;
	var linesBuffer;
	var refgridX,refgridY,refgridZ;

	var numDivMaj = 10;
	var majStep = rad / numDivMaj;
	
	var XC = 0.0
	var YC = 0.0
	var ZC = 0.0

	linesBuffer = [];
	for (gg = -numDivMaj; gg <= numDivMaj; gg+=1)
	{
		linesBuffer.push([XC, YC + (gg*majStep), ZC + (-majStep*numDivMaj)]);
		linesBuffer.push([XC, YC + (gg*majStep), ZC + ( majStep*numDivMaj)]);
		linesBuffer.push([XC, YC + (-majStep*numDivMaj), ZC + (gg*majStep)]);
		linesBuffer.push([XC, YC + ( majStep*numDivMaj), ZC + (gg*majStep)]);		
	}
	{	
		//arrow
		linesBuffer.push([XC, YC, ZC]);
		linesBuffer.push([XC + (rad*1.3), YC, ZC]);
		linesBuffer.push([XC + (rad*1.3), YC, ZC]);
		linesBuffer.push([XC + (rad*1.1), YC, ZC + (rad*0.15)]);
		linesBuffer.push([XC + (rad*1.3), YC, ZC]);
		linesBuffer.push([XC + (rad*1.1), YC, ZC - (rad*0.15)]);
		// X
		linesBuffer.push([XC + (rad*1.4), YC, ZC - (rad*0.1)]);
		linesBuffer.push([XC + (rad*1.6), YC, ZC + (rad*0.1)]);
		linesBuffer.push([XC + (rad*1.4), YC, ZC + (rad*0.1)]);
		linesBuffer.push([XC + (rad*1.6), YC, ZC - (rad*0.1)]);		
	}
	refgridX = presenter.createEntity("refgridX", "lines", linesBuffer);
	refgridX.color = [0.9, 0.5, 0.5, 1.0];
	refgridX.zOff = 0.0;
	linesBuffer = [];
	for (gg = -numDivMaj; gg <= numDivMaj; gg+=1)
	{
			linesBuffer.push([XC + (gg*majStep), YC, ZC + (-majStep*numDivMaj)]);
			linesBuffer.push([XC + (gg*majStep), YC, ZC + ( majStep*numDivMaj)]);
			linesBuffer.push([XC + (-majStep*numDivMaj), YC, ZC + (gg*majStep)]);
			linesBuffer.push([XC + ( majStep*numDivMaj), YC, ZC + (gg*majStep)]);		
	}
	{	
		//arrow
		linesBuffer.push([XC, YC, ZC]);
		linesBuffer.push([XC, YC + (rad*1.3), ZC]);
		linesBuffer.push([XC, YC + (rad*1.3), ZC]);
		linesBuffer.push([XC + (rad*0.15), YC + (rad*1.1), ZC]);
		linesBuffer.push([XC, YC + (rad*1.3), ZC]);
		linesBuffer.push([XC - (rad*0.15), YC + (rad*1.1), ZC]);
		// Y
		linesBuffer.push([XC, YC + (rad*1.4), ZC]);
		linesBuffer.push([XC, YC + (rad*1.5), ZC]);
		linesBuffer.push([XC, YC + (rad*1.5), ZC]);
		linesBuffer.push([XC + (rad*0.1), YC + (rad*1.6), ZC]);
		linesBuffer.push([XC, YC + (rad*1.5), ZC]);
		linesBuffer.push([XC - (rad*0.1), YC + (rad*1.6), ZC]);		
	}	
	refgridY = presenter.createEntity("refgridY", "lines", linesBuffer);
	refgridY.color = [0.5, 0.9, 0.5, 1.0];
	refgridY.zOff = 0.0;
	linesBuffer = [];
	for (gg = -numDivMaj; gg <= numDivMaj; gg+=1)
	{
			linesBuffer.push([XC + (gg*majStep), YC + (-majStep*numDivMaj), ZC]);
			linesBuffer.push([XC + (gg*majStep), YC + ( majStep*numDivMaj), ZC]);
			linesBuffer.push([XC + (-majStep*numDivMaj), YC + (gg*majStep), ZC]);
			linesBuffer.push([XC + ( majStep*numDivMaj), YC + (gg*majStep), ZC]);		
	}
	{	
		//arrow
		linesBuffer.push([XC, YC, ZC]);
		linesBuffer.push([XC, YC, ZC + (rad*1.3)]);
		linesBuffer.push([XC, YC, ZC + (rad*1.3)]);
		linesBuffer.push([XC, YC + (rad*0.15), ZC + (rad*1.1)]);
		linesBuffer.push([XC, YC, ZC + (rad*1.3)]);
		linesBuffer.push([XC, YC - (rad*0.15), ZC + (rad*1.1)]);
		// Z
		linesBuffer.push([XC, YC + (rad*0.1), ZC + (rad*1.4)]);
		linesBuffer.push([XC, YC - (rad*0.1), ZC + (rad*1.6)]);
		linesBuffer.push([XC, YC + (rad*0.1), ZC + (rad*1.4)]);
		linesBuffer.push([XC, YC + (rad*0.1), ZC + (rad*1.6)]);
		linesBuffer.push([XC, YC - (rad*0.1), ZC + (rad*1.6)]);
		linesBuffer.push([XC, YC - (rad*0.1), ZC + (rad*1.4)]);
	}
	refgridZ = presenter.createEntity("refgridZ", "lines", linesBuffer);
	refgridZ.color = [0.5, 0.5, 0.9, 1.0];
	refgridZ.zOff = 0.0;	
	
	presenter.repaint();
}
function deleteReference(){
	presenter.deleteEntity("refgridX");
	presenter.deleteEntity("refgridY");
	presenter.deleteEntity("refgridZ");
}
function updateReference(trackState){	
	if (typeof presenter._scene.entities === 'undefined') return;
	if (typeof presenter._scene.entities["refgridX"] === 'undefined') return;
	
	var tt=[0.0,0.0,0.0];
	tt[0] = presenter.sceneCenter[0];
	tt[1] = presenter.sceneCenter[1];
	tt[2] = presenter.sceneCenter[2];
	
	//var mrX = SglMat4.rotationAngleAxis(sglDegToRad(-trackState[1]), [1.0, 0.0, 0.0]);
	//var mrY = SglMat4.rotationAngleAxis(sglDegToRad(trackState[0]), [0.0, 1.0, 0.0]);
	var mrT = SglMat4.translation(tt);
	var matrix = mrT;//SglMat4.mul(SglMat4.mul(mrT, mrY), mrX);
	presenter._scene.entities["refgridX"].transform.matrix = matrix;
	presenter._scene.entities["refgridY"].transform.matrix = matrix;
	presenter._scene.entities["refgridZ"].transform.matrix = matrix;	
}

//-------------------------------------------------------------------------
function viewFrom(direction){
	document.querySelectorAll('.vbutton').forEach(el => {el.classList.remove('btn-primary'); el.classList.add('btn-secondary');});
	
	let presenter = window.frames[0].presenter; // get current presenter instance
	var distance = 1.4;
	
	let trackType = model_config.options.trackball.type;
		
    switch(direction) {
        case "front":
			if(trackType === "TurntablePanTrackball")
				presenter.animateToTrackballPosition([0.0, 0.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === "SphereTrackball")
				presenter.animateToTrackballPosition([[ 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);
			document.querySelector('#vfront').classList.remove("btn-secondary");
			document.querySelector('#vfront').classList.add("btn-primary");
            break;
        case "back":
			if(trackType === "TurntablePanTrackball")		
				presenter.animateToTrackballPosition([180.0, 0.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === "SphereTrackball")
				presenter.animateToTrackballPosition([[-1, 0, 0, 0, 0, 1, 0, 0, 0, 0,-1, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);
			document.querySelector('#vback').classList.remove("btn-secondary");			
			document.querySelector('#vback').classList.add("btn-primary");			
            break;			
        case "top":
			if(trackType === "TurntablePanTrackball")		
				presenter.animateToTrackballPosition([0.0, 90.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === "SphereTrackball")
				presenter.animateToTrackballPosition([[ 1, 0, 0, 0, 0, 0, 1, 0, 0,-1, 0, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);
			document.querySelector('#vtop').classList.remove("btn-secondary");
			document.querySelector('#vtop').classList.add("btn-primary");
            break;
        case "bottom":
			if(trackType === "TurntablePanTrackball")
				presenter.animateToTrackballPosition([0.0, -90.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === "SphereTrackball")
				presenter.animateToTrackballPosition([[ 1, 0, 0, 0, 0, 0,-1, 0, 0, 1, 0, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);				
			document.querySelector('#vbottom').classList.remove("btn-secondary");
			document.querySelector('#vbottom').classList.add("btn-primary");
            break;
        case "left":
			if(trackType === "TurntablePanTrackball")		
				presenter.animateToTrackballPosition([270.0, 0.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === "SphereTrackball")
				presenter.animateToTrackballPosition([[ 0, 0,-1, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);				
			document.querySelector('#vleft').classList.remove("btn-secondary");
			document.querySelector('#vleft').classList.add("btn-primary");
            break;
        case "right":
			if(trackType === "TurntablePanTrackball")		
				presenter.animateToTrackballPosition([90.0, 0.0, 0.0, 0.0, 0.0, distance]);
			else if (trackType === "SphereTrackball")				
				presenter.animateToTrackballPosition([[ 0, 0, 1, 0, 0, 1, 0, 0,-1, 0, 0, 0, 0, 0, 0, 1 ], 0.0, 0.0, 0.0, distance]);
			document.querySelector('#vright').classList.remove("btn-secondary");
			document.querySelector('#vright').classList.add("btn-primary");
            break;			
    }
}

//-------------------------------------------------------------------------





</script>
</html>
