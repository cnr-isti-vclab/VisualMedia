<h5>
	<img class="m-1" width="25px" src="skins/icons/restore.png" title="Reset Color Options" onclick="material_config.reset();">
	Color:
	</h5>
	<div class="">
		<p class="m-1">
		Start color: 
		<select id="i_startcolor" onchange="material_config.setStartColor(this.value);">
				<option value="color">Texture</option>
				<option value="solid">Solid color</option>
		</select>
		</p>
		<p class="m-1">
		Solid color: <input type="color" id="i_solidcolor" onchange="material_config.setSolidColor(this.value);" value="#aaaaaa">
		</p>
		<p class="m-1">
		<img src="skins/dark/color.png" width="24px"> <input type="checkbox" id="i_toggleColor" onchange="material_config.setTool('color', this.checked);" checked>Texture/Solid toggle</input>
		</p>
	</div>
	
<hr/>

	<h5>
	<img class="m-1" width="25px" src="skins/icons/restore.png" title="Reset Lighting Options" onclick="lighting_config.reset();">
	Lighting:
	</h5>
	<div class="">
		<p class="m-1">
		Start lighting: 
		<select id="i_startlighting" onchange="lighting_config.setStartLighting(this.value);">
				<option value="true">enabled</option>
				<option value="false">disabled</option>
		</select>
		</p>
		<p class="m-1">
		<img src="skins/dark/lighting.png" width="24px"> <input type="checkbox" id="i_toggleLighting" onchange="lighting_config.setTool('lighting', this.checked);" checked>Lighting on/off toggle</input>
		</p class="m-1">
		<p class="m-1">
		<img src="skins/dark/light.png" width="24px"> <input type="checkbox" id="i_toggleLight" onchange="lighting_config.setTool('light', this.checked);" checked>Light direction tool</input>
		</p>					
	</div>
<hr/>

<script>

class MaterialConfig extends Config {
	constructor(frame, options) {
		super(frame, options);
	}
	update() {
		// color options
		document.querySelector('#i_startcolor').value = this.scene().startColor;
		document.querySelector('#i_solidcolor').value = this.scene().solidColor;
		document.querySelector('#i_toggleColor').checked = this.tools().includes("color");
	}
	setStartColor(value) {
		this.scene().startColor = value;
		this.save();
	}
	setSolidColor(value) {
		this.scene().solidColor = value;
		this.save();
	}

	reset() {
		this.scene().startColor = default_ariadne.scene[0].startColor;
		this.scene().solidColor = default_ariadne.scene[0].solidColor;
		this.resetTool('color');

		this.save();
	}
}

let material_config = new MaterialConfig('#media', 'update.php');



class LightingConfig extends Config {
	constructor(frame, options) {
		super(frame, options);
	}
	update() {
		//lighting
		document.querySelector('#i_startlighting').value = Config.options.space.sceneLighting;
		document.querySelector('#i_toggleLighting').checked = this.tools().includes("lighting");
		document.querySelector('#i_toggleLight').checked = this.tools().includes("light");
	}

	setStartLighting(value) {
		Config.options.space.sceneLighting = (value == "true");
		this.checkLightning();
		this.save();
	}

	reset() {
		this.scene().useLighting = default_ariadne.scene[0].useLighting;
		this.resetTool('lighting');
		this.resetTool('light');
		this.save();	
	}
}

let lighting_config = new LightingConfig('#media', 'update.php');

</script>
