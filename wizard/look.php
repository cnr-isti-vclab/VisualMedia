<div>
		<p>Skin:</p>
		<?php foreach(array('dark', 'light', 'minimal_dark', 'minimal_light', 'transparent_dark', 'transparent_light') as $s) {?>
			<img width="32" class="skins" data-skin="<?=$s?>" src="skins/<?=$s?>/home.png">
		<?php } ?>

		<hr/>

			<h5>Background</h5>
			Colors: <input type="color" name="background-0" value="#aaaaaa"> 
				    <input type="color" name="background-1" value="#000000"> <br/>
			<input type="radio" name="background" value="flat"> Flat color <br/>
			<input type="radio" name="background" value="linear" > Linear gradient <br/>
			<input type="radio" name="background" value="radial"> Radial gradient <br/>

			<div class="-group form-inline justify-content-between">
				<div><input type="radio" name="background" value="image"> Image</div>
				<select class="form-control  form-control-sm" name="image">
					<option value="light.jpg">Light</option>
					<option value="dark.jpg">Dark</option>
				</select>
			</div>

		<hr/>

			<h5>Buttons:</h5>
			<input type="checkbox" name="tools[]" value="home" style="display:none">
			<input type="checkbox" name="tools[]" value="zoomin" style="display:none">
			<input type="checkbox" name="tools[]" value="zoomout" style="display:none">
			<input type="checkbox" name="tools[]" value="lighting"> <img src="skins/dark/lighting.png" width="24px"> Lighting</br>
			<input type="checkbox" name="tools[]" value="light"> <img src="skins/dark/lightcontrol.png" width="24px"> Light Direction</br>
			<input type="checkbox" name="tools[]" value="measure"> <img src="skins/dark/measure.png" width="24px"> Measure</br>
			<input type="checkbox" name="tools[]" value="pick"> <img src="skins/dark/pick.png" width="24px"> Picking</br>
			<input type="checkbox" name="tools[]" value="sections"> <img src="skins/dark/sections.png" width="24px"> Sections</br>
			<input type="checkbox" name="tools[]" value="color"> <img src="skins/dark/color.png" width="24px"> Solid Color</br>
			<input type="checkbox" name="tools[]" value="orthographic"> <img src="skins/dark/orthographic.png" width="24px"> Orthographic</br>
			<input type="checkbox" name="tools[]" value="hotspot" style="display:none">
			<input type="checkbox" name="tools[]" value="full" style="display:none">
			<input type="checkbox" name="tools[]" value="help" style="display:none">

		<hr/>

			<h5>
			<img class="m-1" width="25px" src="skins/icons/restore.png" title="Reset FOV" onclick="look.resetWidgets();">Widgets:
			</h5>
				<p id="cbl_basegrid"><input type="checkbox" onchange="look.changedBaseGrid(this.checked);" id="cb_basegrid"> Base Grid</p>
				<p id="cbl_tracksphere"><input type="checkbox" onchange="look.changedTrackSphere(this.checked);" id="cb_tracksphere"> Track Sphere</p>
				</br>
				<input type="checkbox" onchange="look.changedCardinalviews(this.checked);" id="cb_cardinalviews"> Cardinal Views</br>
				<p id="cbl_compass"><input type="checkbox" onchange="look.changedCompass(this.checked);" id="cb_compass"> Compass</p></br>
				
		<hr/>

			<div class="row no-gutters">
				<div class="col-6"><button class="btn btn-secondary btn-sm btn-block" name="reset"> Reset everything </button></div>
			</div>
</div>


<script>
class Look extends Config {
	constructor(frame, options) {
		super(frame, options);

		this.types = document.querySelectorAll('input[name=background]');
		this.image = document.querySelector('select[name=image]');
		this.color0 = document.querySelector('input[type=color][name="background-0"]');
		this.color1 = document.querySelector('input[type=color][name="background-1"]');
		this.skins = document.querySelectorAll('.skins');
		this.tools = [...document.querySelectorAll('input[name="tools[]"')];

		for(let type of this.types) 
			type.addEventListener('change', () => {
				if(type.checked) {
					this.options.background.type = type.value;
					this.save();
				}
			});

		for(let property of ['image', 'color0', 'color1']) {
			this[property].addEventListener('change', () => {
				this.options.background[property] = this[property].value;
				this.save();
			});
		}

		for(let skin of this.skins) {
			let value = skin.getAttribute('data-skin');
			skin.addEventListener('click', (e) => this.set('skin', value))
		}

		for(let tool of this.tools) {
			let value = tool.value;
			tool.addEventListener('change', () => { 
				this.set('tools', this.tools.filter(t => t.checked).map(i => i.value));
			});
		}
	}
	
	update() {
		let options = this.options;

		let background = options.background || 'flat';
		let type = background.type;
		for(let input of this.types)
			input.checked = input.value == type;

		this.image.value = background.image;
		this.color0.value = background.color0;
		this.color1.value = background.color1;

		for(let tool of this.tools) {
			if(options.tools.includes(tool.value))
				tool.checked = true;
		}

		document.getElementById("cb_basegrid").checked = options.widgets.grid.atStartup;
		document.getElementById("cb_tracksphere").checked = options.widgets.trackSphere.atStartup;
		document.getElementById("cb_compass").checked = options.widgets.compass.atStartup;
		
		// depending on which trackball, show the appropriate widget
		if(options.trackball.type === "TurntablePanTrackball"){
			document.getElementById("cbl_basegrid").classList.remove("d-none");
			document.getElementById("cbl_tracksphere").classList.add("d-none");
			document.getElementById("cbl_compass").classList.remove("d-none");
		}
		if(options.trackball.type === "SphereTrackball"){
			document.getElementById("cbl_basegrid").classList.add("d-none");			
			document.getElementById("cbl_tracksphere").classList.remove("d-none");
			document.getElementById("cbl_compass").classList.add("d-none");
		}
		
		document.getElementById("cb_cardinalviews").checked = options.widgets.cardinalViews.atStartup;	
	}
	
	reset() {
		super.reset();
		this.update();
	}
	
	changedBaseGrid(value){	
		this.options.widgets.grid.atStartup = value;
		this.save();
		this.update();
	}
	changedTrackSphere(value){
		this.options.widgets.trackSphere.atStartup = value;
		this.save();
		this.update();
	}
	changedCardinalviews(value){	
		this.options.widgets.cardinalViews.atStartup = value;
		this.save();
		this.update();
	}
	changedCompass(value){	
		this.options.widgets.compass.atStartup = value;
		this.save();
		this.update();
	}	
	
	resetWidgets(){
		if(this.options.trackball.type === "TurntablePanTrackball"){
			this.options.widgets.grid.atStartup = default_ariadne.widgets.grid.atStartup;
			this.options.widgets.trackSphere.atStartup = false;		
		}
		if(this.options.trackball.type === "SphereTrackball"){
			this.options.widgets.grid.atStartup = false;
			this.options.widgets.trackSphere.atStartup = default_ariadne.widgets.trackSphere.atStartup;	
		}	
		this.options.widgets.compass.atStartup = default_ariadne.widgets.compass.atStartup;
		this.options.widgets.cardinalViews.atStartup = default_ariadne.widgets.cardinalViews.atStartup;
		this.options.widgets.compass.atStartup = default_ariadne.widgets.compass.atStartup;		
		this.save();
		this.update();	
	}	
	
}

//-------------------------------------------------------


let look = new Look('#media', 'update.php'); //'options.json'); 

let reset = document.querySelector('button[name=reset]');
reset.addEventListener('click', () => look.reset());

</script>
