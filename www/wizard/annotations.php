<div id="hotspot_panel" class="col-12">
	<h5>
	<img class="restore" title="Reset Hotspots" src="skins/icons/restore.svg" onclick="if(confirm('All hotspots will be deleted. Are you sure?')) annotations.resetAnnotations()"> Hotspots
	</h5>

	<div id="spot_instructions">
		<p>Hotspots are clickable geometries that you can link to additional information. You can add hotspots to the model by using this button:</p>
		<button class="btn btn-secondary btn-block" id="spotStart" onclick="annotations.startSpotInterface()">Configure Hotspots</button>
	</div>
	<div class="d-none" id="annotationsControls">
		<div class="m-1">
		To add an hotspot to your model, click on the object.
		You can configure each hotspot using the panel below.
		</div>
		<div class="p-1">
		<table border="1" style="width:100%">
			<thead id="spots-head"></thead>
			<tbody id="spots-panel"></tbody>
		</table>
		</div>
		<!--button class="btn btn-sm btn-secondary mb-2" title="Exit Discarding Changes" onclick="annotations.resetAnnotations()">Remove all hotspots</button-->
		<div class="m-1 text-right">
			<button class="btn btn-sm btn-danger" title="Exit Discarding Changes" onclick="annotations.cancelSpots()">CANCEL</button>
			<button class="btn btn-sm btn-success" title="Exit Saving Changes" onclick="annotations.applySpots()">APPLY</button>
		</div>
	</div>
</div>

<script>
class Annotations extends Config {
	constructor(frame, options) {
		super(frame, options);

		this.presenter = null;
	}

	pickSpot(pos) {
		this.presenter._pickValid = false;

		if(!Config.options.spots)
			Config.options.spots = {};

		let newID = 1;
		if(Object.keys(Config.options.spots).length) newID = parseInt(Object.keys(Config.options.spots).pop())+1;

//		let sceneMatrix = SglMat4.identity();
//		if(Config.options.scene[0].matrix) sceneMatrix = Config.options.scene[0].matrix;
		let sceneMatrix = this.presenter._scene.modelInstances["model_1"].transform.matrix;
		pos = SglMat4.mul3(SglMat4.inverse(sceneMatrix), pos);

		let newSpot = {};
		newSpot.pos = pos;
		newSpot.visible = true;
		newSpot.radius = 1;
		newSpot.color = [0.9, 0.2, 0.2];
		newSpot.title = "Spot " + newID;
		newSpot.text = "";
		newSpot.tags = [];

		Config.options.spots[newID] = newSpot;
		Config.options.space.sceneRadius = 1/this.presenter.sceneRadiusInv;

		this.fillSpotsPanel();
		this.displaySpots();
	}

	displaySpots() {
		this.presenter._spotsProgressiveID = 1;
		this.presenter._scene.spots = this.presenter._parseSpots(window.frames[0].createSceneSpots(Config.options.spots));
		this.presenter._scenePrepare();

		this.presenter.repaint();
	}

	fillSpotsPanel() {
		var target = document.getElementById("spots-head");
		var content = "";

		var head = "";
		Config.options.spots ? head = "Hotspots List" : head = "Hotspots List Empty";

		content += `
		<tr>
			<th colspan="4" style="text-align:center">`+head+`<\/th>
		<\/tr>
		`;

		target.innerHTML = content;

		target = document.getElementById("spots-panel");
		content = "";

		for (let spot in Config.options.spots) {
			let r = parseInt(Config.options.spots[spot].color[0]*255);
			let g = parseInt(Config.options.spots[spot].color[1]*255);
			let b = parseInt(Config.options.spots[spot].color[2]*255);
			var hxcol = "#" + r.toString(16).padStart(2, '0') + g.toString(16).padStart(2, '0') + b.toString(16).padStart(2, '0'); 
		
			content += `
			<tr>
			<td>
				<textarea class='form-control' rows='1' style='resize:none;' oninput='annotations.updateSpotTitle("${spot}",this.value);' title='Title'>${Config.options.spots[spot].title}<\/textarea>
			<\/td>
			<td style="text-align:center">
				<input type='number' min='1' max='9' value='${Config.options.spots[spot].radius}' onchange='annotations.updateSpotRadius("${spot}",this.value);' style='cursor:hand;width:3em;' title='Radius'>
				<\/br>
				<input type='color' value='${hxcol}' style='cursor:hand;' onchange='annotations.updateSpotColor("${spot}",this.value);' title='Color'>
			<\/td>
			<td style="text-align:center">
				<button class='m-1 btn-sm btn-danger' onclick='annotations.deleteSpot("${spot}");' title='Delete Spot'><img src='./skins/icons/delete.png' width='15px'/><\/button>
			<\/td>
			<\/tr>
			`;
		}

		target.innerHTML = content;
	}

	updateSpotTitle(spotID, title){
		Config.options.spots[spotID].title = title;
	}

	updateSpotRadius(spotID, value){
		Config.options.spots[spotID].radius = value;
		this.displaySpots();
	}

	updateSpotColor(spotID, value){
		const r = parseInt(value.substr(1,2), 16)
		const g = parseInt(value.substr(3,2), 16)
		const b = parseInt(value.substr(5,2), 16)
		Config.options.spots[spotID].color = [r/255.0, g/255.0, b/255.0];
		this.displaySpots();
	}

	deleteSpot(spotID){
		delete Config.options.spots[spotID];
		if(!Object.keys(Config.options.spots).length) Config.options.spots = null;
		this.fillSpotsPanel();
		this.displaySpots();
	}

	startSpotInterface() {
		this.fillSpotsPanel();
		document.getElementById("spot_instructions").classList.add("d-none");
		document.getElementById("annotationsControls").classList.remove("d-none");
		window.frames[0].document.getElementById("toolbar").classList.add("d-none");
		window.frames[0].closeAllTools();
		window.frames[0].document.getElementById("draw-canvas").style.cursor = 'crosshair';
		this.presenter = window.frames[0].presenter;
		this.presenter._onEnterSpot = (id) => this.onEnterSpot(id);
		this.presenter._onEndPickingPoint = (pos) => this.pickSpot(pos);
		this.presenter.enablePickpointMode(true);
		this.presenter.setSpotVisibility(256, true, true);
		this.presenter.enableOnHover(true);
		viewFrom("front");
	}

	endSpotInterface() {
		document.getElementById("spot_instructions").classList.remove("d-none");
		document.getElementById("annotationsControls").classList.add("d-none");
	}

	cancelSpots(){
		this.endSpotInterface();
		Config.refresh();
		annotations = new Annotations();
	}

	applySpots() {
		this.endSpotInterface();
		this.save();
	}

	resetAnnotations(){
		this.endSpotInterface();
		Config.options.spots = null;
		this.save();
	}

	onEnterSpot(id){
		window.frames[0].toastr.options.timeOut = 0;
		window.frames[0].toastr.info(Config.options.spots[id].title);
	}

	update() {
//		let options = this.options;
	}

}

let annotations = new Annotations();

</script>
