<div class="col-12">
	<h5>
		<img class="restore" title="Reset colors options to default" src="restore.svg" onclick="material_config.materialReset();"> Color
	</h5>

	<div class="row">
		<p class="col-6">Initial color: </p>
		<div class="col-6 mb-1">
			<select id="i_startcolor" class="form-control  form-control-sm" onchange="material_config.setStartColor(this.value);">
				<option value="color">Texture</option>
				<option value="solid">Solid color</option>
			</select>
		</div>

		<p class="col-6">Solid color:</p>
		<p class="col-6"><input type="color" id="i_solidcolor" onchange="material_config.setSolidColor(this.value);" value="#aaaaaa"></p>
		<p class="col-12">
			<img src="skins/dark/color.png" width="24px"> <input type="checkbox" id="i_toggleColor" onchange="material_config.setTool('color', this.checked);" checked> Texture/Solid toggle</input>
		</p>
		
		<p class="col-6">Glossiness:</p>
		<div class="col-6">
			<select id="i_glossy"  class="form-control  form-control-sm"  onchange="material_config.setGlossy(this.value);">
				<option value="0">Dull</option>
				<option value="2">Low</option>
				<option value="4">Medium</option>
				<option value="6">Shiny</option>
			</select>
		</div>
	</div>

	<hr/>

	<h5>
		<img class="restore" title="Reset lighting options to default" src="restore.svg" onclick="lighting_config.reset();"> Lighting:
	</h5>
	<div class="row">
		<p class="col-6">Initial lighting:</p>
		<div class="col-6">
			<select id="i_startlighting"  class="form-control  form-control-sm"  onchange="lighting_config.setStartLighting(this.value);">
				<option value="true">Enabled</option>
				<option value="false">Disabled</option>
			</select>
		</div>
	</div>

	<div class="row mt-3">
		<p class="col-12">
		<img src="skins/dark/lighting.png" width="24px"> <input type="checkbox" id="i_toggleLighting" onchange="lighting_config.setTool('lighting', this.checked);" checked>Lighting on/off toggle</input>
		</p>

		<p class="col-12">
		<img src="skins/dark/light.png" width="24px"> <input type="checkbox" id="i_toggleLight" onchange="lighting_config.setTool('light', this.checked);" checked>Light direction tool</input>
		</p>
	</div>
</div>
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
		// glossy
		document.querySelector('#i_glossy').value = this.scene().specular;		
	}
	
	setStartColor(value) {
		this.scene().startColor = value;
		this.save();
	}
	setSolidColor(value) {
		this.scene().solidColor = value;
		this.save();
	}

	setGlossy(value){
		this.scene().specular = parseInt(value);
		this.save();
	}

	materialReset() {
		this.scene().startColor = default_ariadne.scene[0].startColor;
		this.scene().solidColor = default_ariadne.scene[0].solidColor;
		this.resetTool('color');
		this.scene().specular = default_ariadne.scene[0].specular;
		this.save();
	}
}

let material_config = new MaterialConfig();


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
		Config.options.space.sceneLighting = default_ariadne.space.sceneLighting;
		this.resetTool('lighting');
		this.resetTool('light');
		this.save();	
	}
}

let lighting_config = new LightingConfig();

</script>
