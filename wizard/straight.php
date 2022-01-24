
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
		
		<div id="viewControls" class="d-none" style="position:absolute; right:400px; top:0;">
			<h5>View Scene From:</h5>			
			<center>
			<table>
			<tr><td></td><td><button id="vtop" class="btn btn-sm btn-secondary w-100 vbutton" onclick="viewFrom('top');">ABOVE</button></td><td></td><td></td></tr>
			<tr><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vleft" onclick="viewFrom('left');">LEFT</button></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vfront" onclick="viewFrom('front');">FRONT</button></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vright" onclick="viewFrom('right');">RIGHT</button></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vback" onclick="viewFrom('back');">BACK</button></td></tr>
			<tr><td></td><td><button class="btn btn-sm btn-secondary w-100 vbutton" id="vbottom" onclick="viewFrom('bottom');">BELOW</button></td><td></td><td></td></tr> 
			</table>
			</center>
		</div>
		
		<iframe id="media" allowfullscreen allow="fullscreen" style="border-width:0px" class="relight" src="3d.php"></iframe>
		
		<div class="panel">
			


			<p>Straightening:</p>
			
			<button class="btn btn-secondary btn-block" id="smStart" onclick="startStraightMode()">Straighten your model</button>
			<div class="border d-none" id="smControls">
				<div class="m-1 row">
				Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
				</div>
				<div class="m-1">
				
				<center>
					<div class="m-1">
					<table border="1">
					 <tr>
					 <td align="center"><button onclick="rotView('z', 5.0);"><span><<-</span></button></td>
					 <td align="center"><button onclick="rotView('x',-5.0);"><span>V+</span></button></td>
					 <td align="center"><button onclick="rotView('z',-5.0);"><span>->></span></button></td>
					 </tr>
					  <tr>
					 <td align="center"><button onclick="rotView('y',-5.0);"><span>H-</span></button></td>
					 <td align="center"></td>
					 <td align="center"><button onclick="rotView('y', 5.0);"><span>H+</span></button></td>
					 </tr>
					  <tr>
					 <td align="center"></td>
					 <td align="center"><button onclick="rotView('x', 5.0);"><span>V-</span></button></td>
					 <td align="center"></td>
					 </tr>
					</table>
					</div>
				</center>	
				
					<!--
					<div class="m-1">					
					<table border="1">
					 <tr>
					 <td align="center"><button style="cursor:hand;" onclick="rotAbs('z', 5.0);">Z-</button></td>
					 <td align="center"><button style="cursor:hand;" onclick="rotAbs('x',-5.0);">X+</button></td>
					 <td align="center"><button style="cursor:hand;" onclick="rotAbs('z',-5.0);">Z+</button></td>
					 </tr>
					  <tr>
					 <td align="center"><button style="cursor:hand;" onclick="rotAbs('y',-5.0);">Y-</button></td>
					 <td align="center"></td>
					 <td align="center"><button style="cursor:hand;" onclick="rotAbs('y', 5.0);">Y+</button></td>
					 </tr>
					  <tr>
					 <td align="center"></td>
					 <td align="center"><button style="cursor:hand;" onclick="rotAbs('x', 5.0);">X-</button></td>
					 <td align="center"></td>
					 </tr>
					</table>
					</div>
					-->
				</div>
				<div class="m-1">
					<button class="btn btn-sm btn-danger" onclick="rotReset()">Reset to initial pose</button>					
				</div>				
				<div class="m-1 text-right">
					<button class="btn btn-sm btn-danger" onclick="cancelStraightMode()">CANCEL</button>
					<button class="btn btn-sm btn-success" onclick="applyStraightMode()">APPLY</button>
				</div>
			</div>
		<hr/>

			<div class="row">
				<div class="col-6"><button class="btn btn-secondary btn-sm btn-block" name="reset"> Reset everything </button></div>
			</div>

		</div>

	</div>
</body>

<script src="config.js"></script>
<script>

class Straight extends Config {
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
	showReference();
	viewFrom("front");
	//drag
	window.frames[0].document.getElementById("draw-canvas").addEventListener('mousemove', onDrag3D);
	presenter.setTrackballLock(true);
}
function applyStraightMode(){
	document.getElementById("smStart").classList.remove("d-none");
	document.getElementById("smControls").classList.add("d-none");
	document.getElementById("viewControls").classList.add("d-none");	
	var newmatrix = window.frames[0].presenter._scene.modelInstances["model_1"].transform.matrix;
	presenter = null;
	straight.options.scene[0].matrix = newmatrix;
	straight.save();
	//drag
	window.frames[0].document.getElementById("draw-canvas").removeEventListener('mousemove', onDrag3D);
}
function cancelStraightMode(){
	document.getElementById("smStart").classList.remove("d-none");
	document.getElementById("smControls").classList.add("d-none");
	document.getElementById("viewControls").classList.add("d-none");
	presenter = null;
	straight.refresh();
	//drag
	window.frames[0].document.getElementById("draw-canvas").removeEventListener('mousemove', onDrag3D);

}

//-------------------------------------------------------------------------


function onDrag3D(e){
	if(e.buttons != 1)
		return;

	rotView("y", 0.06 * e.movementX);
	rotView("x", 0.06 * e.movementY);

	
	/*
	var EX = (e.clientX / e.target.width) - 0.5;
	var EY = (e.clientY / e.target.height) - 0.5;
	console.log(EX + " " + EY);
	
	var central = (0.16 - ((EX*EX)+(EY*EY)))/0.16;
		
	rotView("y", 0.06 * e.movementX * central);
	rotView("x", 0.06 * e.movementY * central);
	rotView("z", 0.06 * (e.movementX) * (1.0-central))
	*/
}

//-------------------------------------------------------------------------

function rotReset(){
	var newmatrix = SglMat4.identity();
	presenter._scene.modelInstances["model_1"].transform.matrix = newmatrix;
	presenter.repaint();
}

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
	rotAxis = SglMat4.mul4(SglMat4.rotationAngleAxis(sglDegToRad(track[1]), [-1.0, 0.0, 0.0]), rotAxis);
	rotAxis = SglMat4.mul4(SglMat4.rotationAngleAxis(sglDegToRad(track[0]), [0.0, 1.0, 0.0]), rotAxis);
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
	var distance = 1.6;
    switch(direction) {
        case "front":
			presenter.animateToTrackballPosition([0.0, 0.0, 0.0, 0.0, 0.0, distance]);
			document.querySelector('#vfront').classList.remove("btn-secondary");
			document.querySelector('#vfront').classList.add("btn-primary");
            break;
        case "back":
			presenter.animateToTrackballPosition([180.0, 0.0, 0.0, 0.0, 0.0, distance]);
			document.querySelector('#vback').classList.remove("btn-secondary");			
			document.querySelector('#vback').classList.add("btn-primary");			
            break;			
        case "top":
			presenter.animateToTrackballPosition([0.0, 90.0, 0.0, 0.0, 0.0, distance]);
			document.querySelector('#vtop').classList.remove("btn-secondary");
			document.querySelector('#vtop').classList.add("btn-primary");
            break;
        case "bottom":
			presenter.animateToTrackballPosition([0.0, -90.0, 0.0, 0.0, 0.0, distance]);
			document.querySelector('#vbottom').classList.remove("btn-secondary");
			document.querySelector('#vbottom').classList.add("btn-primary");
            break;
        case "left":
			presenter.animateToTrackballPosition([270.0, 0.0, 0.0, 0.0, 0.0, distance]);
			document.querySelector('#vleft').classList.remove("btn-secondary");
			document.querySelector('#vleft').classList.add("btn-primary");
            break;
        case "right":
			presenter.animateToTrackballPosition([90.0, 0.0, 0.0, 0.0, 0.0, distance]);
			document.querySelector('#vright').classList.remove("btn-secondary");
			document.querySelector('#vright').classList.add("btn-primary");
            break;			
    }
}

//-------------------------------------------------------------------------



let straight = new Straight('#media', 'update.php'); //'options.json'); 


let reset = document.querySelector('button[name=reset]');
reset.addEventListener('click', () => straight.reset());



</script>
</html>
