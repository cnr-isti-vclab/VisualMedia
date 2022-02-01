/*
3DHOP - 3D Heritage Online Presenter
Copyright (c) 2014-2020, Visual Computing Lab, ISTI - CNR
All rights reserved.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Constructs a SphereTrackball object.
 * @class Interactor which implements a full spherical trackball controller.
 */
function SphereTrackball() {
}

SphereTrackball.prototype = {

	setup : function (options,myPresenter) {
		options = options || {};
		var opt = sglGetDefaultObject({
			startCenter   : [ 0.0, 0.0, 0.0 ],			
			startMatrix   : [ 1.0, 0.0, 0.0, 0.0, 0.0, 1.0, 0.0, 0.0, 0.0, 0.0, 1.0, 0.0, 0.0, 0.0, 0.0, 1.0 ],
			startPanX     : 0.0,
			startPanY     : 0.0,
			startPanZ     : 0.0,
			startDistance : 2.0,
			minMaxDist    : [0.2, 4.0],
			minMaxPanX    : [-1.0, 1.0],
			minMaxPanY    : [-1.0, 1.0],
			minMaxPanZ    : [-1.0, 1.0]
		}, options);

		this._action = SGL_TRACKBALL_NO_ACTION;
		this._new_action = true;
		this._matrix = SglMat4.identity();

		this.myPresenter = myPresenter;// parent presenter

		// trackball center
		this._center = opt.startCenter;
		
		// starting/default parameters
		this._startMatrix = opt.startMatrix; //matrix
		this._startPanX = opt.startPanX; //panX
		this._startPanY = opt.startPanY; //panY
		this._startPanZ = opt.startPanZ; //panZ
		this._startDistance = opt.startDistance; //distance
		
		// current parameters
		this._rotMatrix = this._startMatrix;
		this._panX = this._startPanX;
		this._panY = this._startPanY;
		this._panZ = this._startPanZ;
		this._distance = this._startDistance;

		//limits
		this._minMaxDist  = opt.minMaxDist;
		this._minMaxPanX  = opt.minMaxPanX;
		this._minMaxPanY  = opt.minMaxPanY;
		this._minMaxPanZ  = opt.minMaxPanZ;
		
		this._pts    = [ [0.0, 0.0], [0.0, 0.0] ];
		this._past = [0.0, 0.0];
		this.reset();
	},

	_clamp : function(value, low, high) {
		if(value < low) return low;
		if(value > high) return high;
		return value;
	},

	_computeMatrix: function() {
		var m = SglMat4.identity();

		// centering
		m = SglMat4.mul(m, SglMat4.translation([-this._center[0], -this._center[1], -this._center[2]]));
		// zoom
		m = SglMat4.mul(m, SglMat4.translation([0.0, 0.0, -this._distance]));
		// 3-axis rotation
		m = SglMat4.mul(m, this._rotMatrix);
		// panning
		m = SglMat4.mul(m, SglMat4.translation([-this._panX, -this._panY, -this._panZ]));

		this._matrix = m;
	  
		if(typeof onTrackballUpdate != "undefined")
			onTrackballUpdate(this.getState());
	},

	_projectOnSphere : function(x, y) {
		var r = 1.0;
		var z = 0.0;
		var d = sglSqrt(x*x + y*y);

		if (d < (r * 0.70710678118654752440)) {
			/* Inside sphere */
			z = sglSqrt(r*r - d*d);
		}
		else {
			/* On hyperbola */
			t = r / 1.41421356237309504880;
			z = t*t / d;
		}
		return z;
	},

	_transform : function(m, x, y, z) {
		return SglMat4.mul4(m, [x, y, z, 0.0]);
	},

	_transformOnSphere : function(m, x, y) {
		var z = this._projectOnSphere(x, y); //get z value
		return this._transform(m, x, y, z);
	},

	_translate : function(offset, f) {
		var invMat = SglMat4.inverse(this._rotMatrix);
		var t = SglVec3.to4(offset, 0.0);
		t = SglMat4.mul4(invMat, t);
		t = SglVec4.muls(t, f);
		var trMat = SglMat4.translation(t);
		this._rotMatrix = SglMat4.mul(this._rotMatrix, trMat);
	},

	getState : function () {
		return [this._rotMatrix, this._panX, this._panY, this._panZ, this._distance];
	},

	setState : function (newstate) {
		this._rotMatrix = newstate[0];	
		this._panX = this._clamp(newstate[1], this._minMaxPanX[0], this._minMaxPanX[1]);
		this._panY = this._clamp(newstate[2], this._minMaxPanY[0], this._minMaxPanY[1]);
		this._panZ = this._clamp(newstate[3], this._minMaxPanZ[0], this._minMaxPanZ[1]);
		this._distance = this._clamp(newstate[4], this._minMaxDist[0], this._minMaxDist[1]);
		this._computeMatrix();
	},

	animateToState : function (newstate) {
		this.setState(newstate);	//no animation for sphere, just set
	},

	recenter : function (newpoint) {
		this._panX = (newpoint[0]-this.myPresenter.sceneCenter[0]) * this.myPresenter.sceneRadiusInv;
		this._panY = (newpoint[1]-this.myPresenter.sceneCenter[1]) * this.myPresenter.sceneRadiusInv;
		this._panZ = (newpoint[2]-this.myPresenter.sceneCenter[2]) * this.myPresenter.sceneRadiusInv;
		this._panX = this._clamp(this._panX, this._minMaxPanX[0], this._minMaxPanX[1]);
		this._panY = this._clamp(this._panY, this._minMaxPanY[0], this._minMaxPanY[1]);
		this._panZ = this._clamp(this._panZ, this._minMaxPanZ[0], this._minMaxPanZ[1]);

		this._distance *= 0.6;
		this._distance = this._clamp(this._distance, this._minMaxDist[0], this._minMaxDist[1]);
		this._computeMatrix();
	},

	tick : function (dt) {
		return false;
	},

	set action(a) { if(this._action != a) this._new_action = true; this._action = a;},

	get action()  { return this._action; },

	get matrix() { this._computeMatrix(); return this._matrix; },

	get distance() { return this._distance; },

	reset : function () {
		this._matrix = SglMat4.identity();
		this._action = SGL_TRACKBALL_NO_ACTION;
		this._new_action = true;
		this._pts    = [ [0.0, 0.0], [0.0, 0.0] ];
		
		this._rotMatrix = this._startMatrix;
		this._panX = this._startPanX;
		this._panY = this._startPanY;
		this._panZ = this._startPanZ;
		this._distance = this._startDistance;

		this._computeMatrix();
	},

	track : function(m, x, y, z) {
		
		if(this._new_action) {
			this._past[0] = this.myPresenter.x;
			this._past[1] = this.myPresenter.y;
			this._new_action = false;
		}
		
		this._pts[0][0] = this._past[0];
		this._pts[0][1] = this._past[1];
		this._pts[1][0] = this.myPresenter.x;
		this._pts[1][1] = this.myPresenter.y;		

		this._past[0] = this.myPresenter.x;
		this._past[1] = this.myPresenter.y;	

		switch (this._action) {
			case SGL_TRACKBALL_ROTATE:
				this.rotate(m);
			break;

			case SGL_TRACKBALL_PAN:
				this.pan(m);
			break;

			case SGL_TRACKBALL_SCALE:
				this.scale(m, z);
			break;

			default:
			break;
		}
	},

	rotate : function(m) {
		if ((this._pts[0][0] == this._pts[1][0]) && (this._pts[0][1] == this._pts[1][1])) return; //if Xold == Xnew && Yold ==Ynew return

		var mInv = SglMat4.inverse(m);

		var v0 = this._transformOnSphere(mInv, this._pts[0][0], this._pts[0][1]); //project on sphere (Xold, Yold)
		var v1 = this._transformOnSphere(mInv, this._pts[1][0], this._pts[1][1]); //project on sphere (Xnew, Ynew)

		var axis   = SglVec3.cross(v0, v1); //axis of rotation
		var angle  = SglVec3.length(axis); //angle of rotation
		var rotMat = SglMat4.rotationAngleAxis(angle, axis);

		this._rotMatrix = SglMat4.mul(rotMat, this._rotMatrix);
		this._computeMatrix();
	},

	pan : function(m) {
		var dx = this._pts[0][0] - this._pts[1][0];
		var dy = this._pts[0][1] - this._pts[1][1];		
		
		//determining current X, Y and Z axis
		var Xvec = SglMat4.mul4(this._rotMatrix, [1.0, 0.0, 0.0, 1.0]);
		var Yvec = SglMat4.mul4(this._rotMatrix, [0.0, 1.0, 0.0, 1.0]);
		var Zvec = SglMat4.mul4(this._rotMatrix, [0.0, 0.0, 1.0, 1.0]);

		var panSpeed = Math.max(Math.min(1.5, this._distance),0.05);
		this._panX += ((dx * Xvec[0]) + (dy * Xvec[1])) * panSpeed;
		this._panY += ((dx * Yvec[0]) + (dy * Yvec[1])) * panSpeed;
		this._panZ += ((dx * Zvec[0]) + (dy * Zvec[1])) * panSpeed;

		//clamping
		this._panX = this._clamp(this._panX, this._minMaxPanX[0], this._minMaxPanX[1]);
		this._panY = this._clamp(this._panY, this._minMaxPanY[0], this._minMaxPanY[1]);
		this._panZ = this._clamp(this._panZ, this._minMaxPanZ[0], this._minMaxPanZ[1]);

		this._computeMatrix();	
	},

	scale : function(m, s) {
		this._distance *= s;
		this._distance = this._clamp(this._distance, this._minMaxDist[0], this._minMaxDist[1]);
		this._computeMatrix();
	}
};
/***********************************************************************/
