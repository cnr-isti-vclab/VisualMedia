

class Config {
	constructor(frame, url) {
		this.url = url;
		this.frame = document.querySelector(frame);

		this.options = default_ariadne;

		if(url) {
			fetch(url, { method: "GET", headers: { "Content-Type": "application/json" } })
				.then(response => {
					response.json()
						.then(data => { 
							this.options = data; 
							this.update(); })
				});
		} else
			this.update();
	}

	update() {}

	refresh() {
		this.frame.contentWindow.location.reload();
	}

	reset() {
		this.options = JSON.parse(JSON.stringify(default_ariadne));
		this.save();
	}
	
	set(key, value) {
		this.options[key] = value;
		this.save();
	}

	save() {
		var json = JSON.stringify(this.options);
		var xhr = new XMLHttpRequest();
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
	"tools": [ "home", "lighting", "light", "color", "measure", "pick", "sections", "orthographic", "full", "help" ],
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
		"matrix": null
		}
	],
	"widgets": {
		"grid" : {
			"step" : 0,
			"atStartup" : true
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



