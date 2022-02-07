

class Config {
	static options = null;
	static children = [];

	constructor(frame, url) {
		Config.children.push(this);
		if(!Config.options)
			Config.options = JSON.parse(JSON.stringify(default_ariadne));

		this.url = url;
		this.frame = document.querySelector(frame);
		if(url) {
			fetch(url, { method: "GET", headers: { "Content-Type": "application/json" } })
				.then(response => {
					response.json()
						.then(data => { 
							Config.options = data; 
							this.update(); 
						})
				});
		} else {
			this.update();
		}
	}
	
	scene() { return Config.options.scene[0]; }
	tools() { return Config.options.tools; }

	update() {}

	refresh() {
		this.frame.contentWindow.location.reload();
	}

	reset() {
		Config.options = JSON.parse(JSON.stringify(default_ariadne));
		this.save();
	}
	
	set(key, value) {
		Config.options[key] = value;
		this.save();
	}
	// special case, lighting OFF and lighting toggle OFF, I must deactivate light direction
	checkLightning() {
		let tools = Config.options.tools;
		if( (!this.scene().useLighting) && (!tools.includes("lighting"))) {
			Config.options.tools = tools.filter(t => t != "light");
			this.update();
		}
	}

	setTool(tool, value) {
		Config.options.tools = Config.options.tools.filter(t => t != tool);
		if(value)
			Config.options.tools.push(tool);
		this.checkLightning();
		this.save();
	}

	resetTool(tool) {
		Config.options.tools = Config.options.tools.filter(t => t != tool);
		if(default_ariadne.tools.includes(tool))
			this.tools().push(tool);
	}

	save() {
		for(let child of Config.children)
			child.update();
		let json = JSON.stringify(Config.options);
		let xhr = new XMLHttpRequest();
		xhr.open('POST', this.url, true);
		xhr.setRequestHeader('Content-Type', 'application/json');
		xhr.send(json);

		xhr.onreadystatechange = (event) => {
			if (xhr.readyState === 4) {
				if (xhr.status === 200) {
					this.refresh();
				} else {
					this.onError(xhr.statusText);
				}
			}
		};
	}

	onError(error) {
		alert("Could not save options: " + error);
	}
};


var default_ariadne = { 
    "background": {
        "type": "linear",
        "color0": "#aaaaaa",
        "color1": "#000000",
        "image": "light.jpg"
    },
	"skin": "light", 
	"tools": [ "home", "light", "color", "measure", "pick", "sections", "orthographic", "full", "help" ],
	"space": {
		"centerMode": "scene",
		"radiusMode": "scene",
		"cameraFOV": 60,
		"sceneLighting": true
	}, 
	"trackball": {
		"type": "TurntablePanTrackball",
		"trackOptions": {
			"startPhi": 30,
			"startTheta": 25,
			"startDistance": 1.5,
			"startPanX": 0,
			"startPanY": 0,
			"startPanZ": 0,
			"minMaxPhi": [-180, 180],
			"minMaxTheta": [-90, 90],
			"minMaxDist": [0.1, 3],
			"minMaxPanX": [-1.0, 1.0],
			"minMaxPanY": [-1.0, 1.0],
			"minMaxPanZ": [-1.0, 1.0]
		}
	}, 
	 "scene": [{
		"id": "mesh",
		"url": "",
		"matrix": null,
		"startColor": "color",
		"solidColor": "#aaaaaa"
		}
	],
	"widgets": {
		"grid" : {
			"step" : 0,
			"atStartup" : true
		},
		"trackSphere" : {
			"atStartup" : false
		},
		"cardinalViews" : {
			"atStartup" : true
		},
		"compass" : {
			"atStartup" : false
		}		
	},
	"bookmark": {
		"ID": {
			"view": {
				"position": [ 0.0, 0.0, 0.0],
				"target": [ 0.0, 0.0, 0.0],
				"up": [ 0.0, 0.0, 0.0]
			},
			"title": "",
			"text": "",
			"tags": [],
			"state": {
				"camera": "ortho",
				"solid": false,
				"transparency": false,
				"lighting": true,
				"lightDir": [ 0.0, 0.0, 0.0]
			}
		},
		"viewsNewIndex": 2
	}
};
