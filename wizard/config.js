

class Config {
	static options = null;
	static children = [];
	static url = null;
	static frame = null;

	constructor(frame, url) {
		Config.children.push(this);
		if(!Config.options)
			Config.options = JSON.parse(JSON.stringify(default_ariadne));

		if(url)
			Config.url = url;
		if(frame)
			Config.frame = frame;
		if(typeof(Config.frame) == 'string')
			Config.frame = document.querySelector(Config.frame);


		fetch(Config.url, { method: "GET", headers: { "Content-Type": "application/json" } })
		.then(response => {
				response.json()
			.then(data => { 
				Config.options = data; 
				this.update(); 
			})
		});
	}
	
	scene() { return Config.options.scene[0]; }
	tools() { return Config.options.tools; }

	update() {}

	static refresh() {
		Config.frame.contentWindow.location.reload();
	}

	static reset() {
		Config.options = JSON.parse(JSON.stringify(default_ariadne));
		Config.saveOptions();
	}
	
	set(key, value) {
		Config.options[key] = value;
		this.save();
	}
	// special case, lighting OFF and lighting toggle OFF, I must deactivate light direction
	checkLightning() {
		let tools = Config.options.tools;
		if( (!Config.options.space.sceneLighting) && (!tools.includes("lighting"))) {
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

	
	save(skiprefresh) {
		Config.saveOptions(skiprefresh);
	}

	static saveOptions(skiprefresh) {
		for(let child of Config.children)
			child.update();
		let json = JSON.stringify(Config.options);
		let xhr = new XMLHttpRequest();
		xhr.open('POST', Config.url, true);
		xhr.setRequestHeader('Content-Type', 'application/json');
		xhr.send(json);

		xhr.onreadystatechange = (event) => {
			if (xhr.readyState === 4) {
				if (xhr.status === 200) {
					if(!skiprefresh)
						Config.refresh();
				} else {
					if(this.onError)
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
        "type": "radial",
        "color0": "#aaaaaa",
        "color1": "#000000",
        "image": "light.jpg"
    },
	"skin": "light", 
	"tools": [ "home", "light", "measure", "sections", "full", "help" ],
	"space": {
		"centerMode": "scene",
		"radiusMode": "scene",
		"cameraFOV": 60,
		"sceneLighting": true,
		"cameraType": "perspective"
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
		"solidColor": "#aaaaaa",
		"specular": 2
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
		"canonicalViews" : {
			"atStartup" : true
		},
		"compass" : {
			"atStartup" : false
		},
		"navCube" : {
			"atStartup" : false
		}		
	},
	"spots": {},
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
