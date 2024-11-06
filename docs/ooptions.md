look

	{
		tools = [ 'home', 'zoomin', 'zoomout', 'light', 'color',  'color', 'measure', 'picking', sections', 'fullscreen','info'],

		background: url() or color #... or gradient,
		skin: dark,
	}

		nav: {

		trackball: {
			type : TurnTableTrackball,
			trackOptions : {
				startPhi: 0.0,
				startTheta: 0.0,
				startDistance: 2.5,
				minMaxPhi: [-180, 180],
				minMaxTheta: [-70.0, 70.0],
				minMaxDist: [0.5, 3.0]
			}
		}
		transform: {
		}

		}

scene

		spots: {
		"Sphere": {
			mesh: "Sphere",
			transform: {
				matrix: SglMat4.mul(SglMat4.translation([-2.0, 12.0, 25.0]), SglMat4.scaling([30.0, 15.0, 15.0]))
			},
			color: [0.0, 0.25, 1.0],
			alpha: 0.5
		}

		space {
			cameraType    : "perspective",	  
			cameraFOV     : 60.0,
			cameraNearFar : [0.01, 10.0],
			sceneLighting : true


