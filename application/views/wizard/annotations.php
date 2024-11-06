<style>
#spots {
  list-style-type: none;
  padding: 0;
  margin: 0;
}
#spots li div {
	display:flex;
	justify-content: space-around;
}
#spots a {
	cursor:pointer;
}

.modal {
	color:black;
}
</style>
<div id="hotspot_panel" class="col-12">
	<h5>
	<img class="restore" title="Reset Hotspots" src="/wizard/skins/icons/restore.svg" onclick="if(confirm('All hotspots will be deleted. Are you sure?')) annotations.resetAnnotations()"> Hotspots
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
		<ul class="p-1" id="spots">
		</ul>
		<div class="m-1 text-right">
<!--			<button class="btn btn-sm btn-secondary" title="Exit Discarding Changes" onclick="annotations.cancelSpots()">Cancel</button> -->
			<button class="btn btn-sm btn-success" title="Exit Saving Changes" onclick="annotations.exitSpecialMode()">Done</button>
		</div>
	</div>
</div>

<div id="spot-config" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<div class="form-group">
			<label>Title:</label>
			<input name="title" type="text" data-spot="-1" class="form-control form-control-sm" oninput="annotations.updateSpotTitle(this);"
			title='Title' value=""></input>
		</div>

		<div class="form-group">
			<label>Body:</label>
			<textarea name="body" data-spot="-1" class="form-control form-control-sm" oninput='annotations.updateSpotBody(this);'>
			</textarea>
		</div>

		<div class="form-group row">
			<label class="col-3">Size:</label>
			<div class="col-3">
				<input name="radius" type='number' data-spot="-1" class="form-control form-control-sm" min='1' max='9' step='0.5' 
				value='' onchange='annotations.updateSpotRadius(this);' style='display:inline-block; width:4em;' title='Radius'/>
			</div>

			<label class="col-3">Color:</label>
			<div class="col-3">
				<input name="color" type='color' data-spot="-1" value='' style='cursor:hand;margin-right:40px' 
					onchange='annotations.updateSpotColor(this);' title='Color'>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>
$('#spot-config').modal({show:false});
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
		this.save(true);
	}

	displaySpots() {
		this.presenter._spotsProgressiveID = 1;
		this.presenter._scene.spots = this.presenter._parseSpots(window.frames[0].createSceneSpots(Config.options.spots));
		this.presenter._scenePrepare();
		this.presenter.repaint();
	}

	showSpotConfig(id) {
		const spot = Config.options.spots[id];
		for(let field of ['title', 'body', 'color', 'radius']) {
			let input = $(`#spot-config [name=${field}]`);
			if(field == 'color')
				input.val(color2Hex(spot[field]));
			else
				input.val(spot[field]);
			input.attr('data-spot', id);
		}
		$('#spot-config').modal('show');
	}

	fillSpotsPanel() {
		let target = document.getElementById("spots");
		let content = "";
		if(!Config.options.spots)
			Config.options.spots = [];
		for (const [id, spot] of Object.entries(Config.options.spots)) {
			if(!spot) continue;
		
			content += `
			<li class="mt-2">
				<div>
					<input type="text" class='form-control form-control-sm' oninput='annotations.updateSpotTitle("${id}",this.value);' title='Title' value="${spot.title}"></input>
					<a class='ml-1 btn-sm btn-secondary' onclick='annotations.showSpotConfig("${id}");' title='Edit Spot'>
						<img src='/wizard/skins/icons/edit.svg' width='24px'/></a>

					<a class='ml-1 btn-sm btn-secondary' onclick='annotations.deleteSpot("${id}");' title='Delete Spot'>
						<img src='/wizard/skins/icons/trash.svg' width='24px'/></a>
				</div>
			</li>
			`;
		}

		target.innerHTML = content;
	}

	updateSpotTitle(input) {
		const id = input.getAttribute('data-spot');
		Config.options.spots[id].title = input.value;
		this.save(true);
	}

	updateSpotBody(input) {
		const id = input.getAttribute('data-spot');
		Config.options.spots[id].body = input.value;
		this.save(true);
	}

	updateSpotRadius(input) {
		const id = input.getAttribute('data-spot');
		Config.options.spots[id].radius = input.value;
		this.displaySpots();
		this.save(true);
	}

	updateSpotColor(input){
		const id = input.getAttribute('data-spot');
		const value = input.value;
		const r = parseInt(value.substr(1,2), 16)
		const g = parseInt(value.substr(3,2), 16)
		const b = parseInt(value.substr(5,2), 16)
		Config.options.spots[id].color = [r/255.0, g/255.0, b/255.0];
		this.displaySpots();
		this.save(true);
	}

	deleteSpot(spotID){
		delete Config.options.spots[spotID];
		if(!Object.keys(Config.options.spots).length) Config.options.spots = null;
		this.fillSpotsPanel();
		this.displaySpots();exitSpecialMode
		this.save(true);
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

	exitSpecialMode() {
		this.endSpotInterface();
		Config.refresh();
	}

	endSpotInterface() {
		document.getElementById("spot_instructions").classList.remove("d-none");
		document.getElementById("annotationsControls").classList.add("d-none");
	}

	resetAnnotations(){
		this.endSpotInterface();
		Config.options.spots = null;
		this.save(true);
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

function color2Hex(color) {
	let r = parseInt(color[0]*255);
	let g = parseInt(color[1]*255);
	let b = parseInt(color[2]*255);
	return "#" + r.toString(16).padStart(2, '0') + g.toString(16).padStart(2, '0') + b.toString(16).padStart(2, '0'); 
}

</script>
