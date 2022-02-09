<div class="col-12">
	<h5>
	<img class="restore" title="Reset skin to default" src="restore.svg" onclick="look.resetSkin();"> Skin
	</h5>

	<div style="display:flex; justify-content:space-around; width:100%">
	<?php foreach(array('dark', 'light', 'minimal_dark', 'minimal_light', 'transparent_dark', 'transparent_light') as $s) {?>
		<img width="32" class="skins" data-skin="<?=$s?>" src="skins/<?=$s?>/home.png">
	<?php } ?>
	</div>

	<hr/>

	<h5>
	<img class="restore" title="Reset background to default" src="restore.svg" onclick="look.resetBackground();"> Background
	</h5>
	<div class="row">
		<p class="col-6">Primary color:</p>
		<p class="col-6"> <input type="color" name="background-0" value="#aaaaaa"></p>
		<p class="col-6">Secondary color:</p>
		<p class="col-6"><input type="color" name="background-1" value="#000000"> <p>
	</div>
	<div class="row">
		<p class="col-12"><input type="radio" name="background" value="flat"> Flat color</p>
		<p class="col-12"><input type="radio" name="background" value="linear" > Linear gradient </p>
		<p class="col-12"><input type="radio" name="background" value="radial"> Radial gradient </p>

		<p class="col-6"><input type="radio" name="background" value="image"> Image</p>
		<div class="col-6">
			<select class="form-control  form-control-sm" style="" name="image">
				<option value="light.jpg">Light</option>
				<option value="dark.jpg">Dark</option>
			</select>
		</div>
	</div>

	<hr/>

	<h5>
		<img class="restore" title="Reset tools to default" src="restore.svg" onclick="look.resetTools();"> Tools
	</h5>
	<div class="row">
		<div class="col-12">
			<input type="checkbox" name="tools[]" value="home"    style="display:none">
			<input type="checkbox" name="tools[]" value="zoomin"  style="display:none">
			<input type="checkbox" name="tools[]" value="zoomout" style="display:none">
			<input type="checkbox" name="tools[]" value="lighting">     <img src="skins/dark/lighting.png" width="24px"> Lighting</br>
			<input type="checkbox" name="tools[]" value="light">        <img src="skins/dark/lightcontrol.png" width="24px"> Light Direction</br>
			<input type="checkbox" name="tools[]" value="measure">      <img src="skins/dark/measure.png" width="24px"> Measure</br>
			<input type="checkbox" name="tools[]" value="pick">         <img src="skins/dark/pick.png" width="24px"> Picking</br>
			<input type="checkbox" name="tools[]" value="sections">     <img src="skins/dark/sections.png" width="24px"> Sections</br>
			<input type="checkbox" name="tools[]" value="color">        <img src="skins/dark/color.png" width="24px"> Solid Color</br>
			<input type="checkbox" name="tools[]" value="orthographic"> <img src="skins/dark/orthographic.png" width="24px"> Orthographic</br>
			<input type="checkbox" name="tools[]" value="hotspot" style="display:none">
			<input type="checkbox" name="tools[]" value="full"> <img src="skins/dark/full.png" width="24px"> Full Screen</br>
			<input type="checkbox" name="tools[]" value="help"    style="display:none">
		</div>
	</div>

	<hr/>

		<h5>
		<img class="restore" title="Reset widgets to default" src="restore.svg" onclick="look.resetWidgets();"> Widgets
		</h5>
		<p id="cbl_basegrid"><input type="checkbox" onchange="look.setBaseGrid(this.checked);" id="cb_basegrid"> Base Grid</p>
		<p id="cbl_tracksphere"><input type="checkbox" onchange="look.setTrackSphere(this.checked);" id="cb_tracksphere"> Track Sphere</p>
		<p><input type="checkbox" onchange="look.setCanonicalViews(this.checked);" id="cb_canonicalviews"> Canonical Views</p>
		<p id="cbl_compass"><input type="checkbox" onchange="look.setCompass(this.checked);" id="cb_compass"> Compass</p>
		<p><input type="checkbox" onchange="look.setNavCube(this.checked);" id="cb_navcube"> Navigation Cube</p>
</div>

<hr/>

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
					Config.options.background.type = type.value;
					this.save();
				}
			});

		for(let property of ['image', 'color0', 'color1']) {
			this[property].addEventListener('change', () => {
				Config.options.background[property] = this[property].value;
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
		let options = Config.options;

		let background = options.background || 'flat';
		let type = background.type;
		for(let input of this.types)
			input.checked = input.value == type;

		this.image.value = background.image;
		this.color0.value = background.color0;
		this.color1.value = background.color1;

		for(let tool of this.tools) 
			tool.checked = options.tools.includes(tool.value);

		document.getElementById("cb_basegrid").checked = options.widgets.grid.atStartup;
		document.getElementById("cb_tracksphere").checked = options.widgets.trackSphere.atStartup;
		document.getElementById("cb_compass").checked = options.widgets.compass.atStartup;
		document.getElementById("cb_navcube").checked = options.widgets.navCube.atStartup
		
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
		
		document.getElementById("cb_canonicalviews").checked = options.widgets.canonicalViews.atStartup;
	}

	resetSkin() {
		Config.skin = default_ariadne.skin;
		this.save;
	}

	resetBackground() {
		this.save();
	}

	resetTools() {
		this.save;
	}

	setBaseGrid(value){	
		Config.options.widgets.grid.atStartup = value;
		this.save();
	}
	setTrackSphere(value){
		Config.options.widgets.trackSphere.atStartup = value;
		this.save();
	}
	setCanonicalViews(value){	
		Config.options.widgets.canonicalViews.atStartup = value;
		this.save();
	}
	setCompass(value){	
		Config.options.widgets.compass.atStartup = value;
		this.save();
	}
	setNavCube(value){	
		Config.options.widgets.navCube.atStartup = value;
		this.save();
	}
	
	resetWidgets(){
		let trackball = Config.options.trackball;
		let widgets = Config.options.widgets;
		if(trackball.type === "TurntablePanTrackball"){
			widgets.grid.atStartup = default_ariadne.widgets.grid.atStartup;
			widgets.trackSphere.atStartup = false;		
		}
		if(trackball.type === "SphereTrackball"){
			widgets.grid.atStartup = false;
			widgets.trackSphere.atStartup = default_ariadne.widgets.trackSphere.atStartup;	
		}	
		widgets.canonicalViews.atStartup = default_ariadne.widgets.canonicalViews.atStartup;
		widgets.compass.atStartup = default_ariadne.widgets.compass.atStartup;		
		widgets.navCube.atStartup = default_ariadne.widgets.navCube.atStartup;		
		this.save();
	}
}

//-------------------------------------------------------


let look = new Look(); //'options.json'); 

</script>
