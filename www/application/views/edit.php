<div class="row">
	<div class="col-md-9 offset-md-3">
		<h1>Editing <a href="/media/<?= $media->label ?>"><?= $media->title ?></a></h1>
	</div>
</div>

<div class="row">
	<div class="col-md-3 d-flex flex-column gap-3">

		<!-- Section 1: Versions -->
		<div class="card">
			<div class="card-header">Model Versions</div>
			<div class="list-group list-group-flush" id="versionList">
<!--				<button type="button" class="list-group-item list-group-item-action active" data-version="0">
					Original<br><small class="text-muted">15,000 triangles</small>
				</button>
				<button type="button" class="list-group-item list-group-item-action active" data-version="1">
					Version 1<br><small class="text-muted">15,000 triangles</small>
				</button>
				<button type="button" class="list-group-item list-group-item-action" data-version="2">
					Version 2<br><small class="text-muted">8,200 triangles</small>
				</button> -->
			</div>
		</div>

		<!-- Section 2: Actions -->
		<div class="card">
			<div class="card-header">Actions</div>
			<div class="btn-group-vertical w-100" role="group" id="actionButtons">
				<button type="button" class="btn btn-outline-primary action" id="simplify">Simplify</button>
				<button type="button" class="btn btn-outline-primary action" id="remesh">Remesh</button>
				<button type="button" class="btn btn-outline-primary action" id="close">Close Holes</button>
				<button type="button" class="btn btn-outline-primary action" id="info">Info</button>
			</div>
		</div>

		<!-- Section 3: Parameters -->
		<div class="card">
			<div class="card-header">Parameters</div>
			<div class="card-body">

				<form id="processForm" method="POST" action="<?= site_url('media/process') ?>">
					<input type="hidden" name="parent" value="">
					<input type="hidden" name="label" value="<?= $media->label ?>">

					<div class="form-group" id="parameters">
					</div>
			</div>
		</div>

	</div>

	<div class="col-md-9" style="position:relative; height:80vh;">
		<iframe id="thumb" allowfullscreen allow="fullscreen"
			style="position:absolute; top:0px; left:0px; width:100%; height:80%; border-width:0px"
			src="<?= $media->link ?>?standalone"></iframe>
	</div>
	<div class="col-md-12">
		<p class="text-muted">Status: <span id="status"><?= $media->status ?></span></p>
	</div>

</div>


<script>
	let status = '<?= $media->status ?>';
//make sure to check ariadne.py in modify reads the correct parameters
	let parameters = {
		'simplify': {
			'triangles': {
				'label': 'Number of triangles',
				'type': 'integer'
			}
		},
		'remesh': {
			'size': {
				'label': 'Size of triangles',
				'type': 'float'
			}
		},
		'close': {
			'size': {
				'label': 'Size of holes to close',
				'type': 'float'
			}
		},
		'info':	{
			'type':	{
				'label': 'Type of information',
				'type': 'select',
				'options': {
					'vertices': 'Topology',
					'edges': 'Geometry',
					'faces': 'Thickness',
				}
			}
		}
	};

	document.querySelectorAll('button.action').forEach(btn => {
		btn.addEventListener('click', function () {
			buildForm(this.id);
		});
	});

	document.getElementById('processForm').addEventListener('submit', function (e) {
		e.preventDefault();

		const form = e.target;
		const formData = new FormData(form);

		fetch('/media/modify/<?= $media->label ?>', {
			method: 'POST',
			body: formData
		})
			.then(response => response.json())
			.then(data => {
				if (data.error) {
					console.error('Error:', data.error);
					alert('Error: ' + data.error);
				} else {
					//set status as processing.
					setStatus('on queue');
					// do something with result
				}
			})
			.catch(err => {
				console.error('Fetch failed:', err);
				alert('Request failed.');
			});
	});


	function buildForm(action) {
		let params = parameters[action];

		let html = "";

		for (const [key, value] of Object.entries(params)) {
			console.log(key, value);
			html += `
	<input type="hidden" name="action" value="${action}">
	<div class="form-group">
		<label for="${key}">${value.label}</label>`;
			if (['percentage', 'integer', 'float'].includes(value.type)) {
				html += `<input type="number" class="form-control form-control-sm" id="${key}" name="${key}" value="" required>`;
			} else if (value.type === 'select') {
				html += `<select class="form-control form-control-sm" id="${key}" name="${key}">`;
				for (const [optKey, optValue] of Object.entries(value.options)) {
					html += `<option value="${optKey}">${optValue}</option>`;
				}
				html += `</select>`;
			} else {
				html += `<input type="text" class="form-control form-control-sm" id="${key}" name="${key}">`;
			}
			html += `</div>`;
		}

		html += `<button type="submit" class="btn btn-primary">Apply</button>`

		document.querySelector('#parameters').innerHTML = html;
	}

	function setStatus(status) {
		let statusbar = document.querySelector('#status');
		statusbar.textContent = data.status;
		//TODO set class for statusbar depending on status
	}

	function setCurrentVersion(btn) {
		document.querySelectorAll('#versionList .list-group-item').forEach(b => b.classList.remove('active'));
		btn.classList.add('active');
		//set input with name="version" to the data-version of the button
		let version = btn.dataset.version;
		document.querySelector('input[name="parent"]').value = version;
	}
	
	function fillVariants(variants) {
		for(let v of variants) {
			let btn = document.createElement('button');
			btn.className = 'list-group-item list-group-item-action';
			btn.textContent = v.label;
			btn.dataset.version = v.version;
			btn.addEventListener('click', (e) => { setCurrentVersion(e.target); });
			setCurrentVersion(btn);	
			document.getElementById('versionList').appendChild(btn);
		}
	}
	let variants = <?=$media->variants ?>;
	fillVariants(variants);

	function checkStatus() {
		fetch('/media/status/<?= $media->label ?>')
			.then(response => response.json())
			.then(data => {
				if (data.status && data.status != '<?= $media->status ?>') {
					setStatus(data.status);
				}
			});
	}

	checkStatus();

</script>