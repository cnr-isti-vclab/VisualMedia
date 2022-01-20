

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
		xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
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
        "image": "light.png"
    },
	"skin": "light", 
	"tools": [ "home", "zoomin", "zoomout", "lighting", "light", "color", "measure", 
		"pick", "sections", "orthographic", "full", "help" ],
	"space": {
		"cameraFOV": 60,
		"sceneLighting": true
	}, 
	"trackball": {
		"type": "TurntablePanTrackball",
		"trackOptions": {
			"startPhi": 0,
			"startTheta": 0,
			"startDistance": 2.5,
			"startPanX": 0,
			"startPanY": 0,
			"startPanZ": 0,
			"minMaxPhi": [-180, 180],
			"minMaxTheta": [-70, 70],
			"minMaxDist": [0.2, 5],
			"minMaxPanX": [-1, 1],
			"minMaxPanY": [-1, 1],
			"minMaxPanZ": [-1, 1]
		}
	}, 
	 "scene": [{
		"id": "mesh",
		"url": "",
		"transform": null
		}
	],
	"spots": {
		"ID": {
			"pos": [0.0, 0.0, 0.0],
			"title": "",
			"text": "",
			"visible": true,
			"radius": 3,
			"color": [0.9, 0.2, 0.2],
			"alpha": 0.2,
			"alphaHigh" : 0.5,
			"tags": []
		},
		"spotNewIndex": 2
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



