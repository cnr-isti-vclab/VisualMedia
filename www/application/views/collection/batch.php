<style>
#media tr td {
	text-overflow: ellipsis;
}
#media tr td {
	white-space: nowrap;
	overflow: hidden;
	max-width: 200px;
}
#media tr td:nth-child(3) {
	max-width:300px;
}

</style>
<div class="row">
	<div class="col-12">

		<h2>Collection: <?=$collection->title?> </h2>

		<h3>Batch upload:</h3>
		<p>1. Upload a csv file with the following columns:</p>
		<ul>
			<li>filename
			<li>title
			<li>description
			<li>copyright
			<li>type
		</ul>
		<p>If multiplef iles are required for a media (such as .ply + textures), separate them using a comma (,) in the filename column.</p>
		<p>Filenames must be unique!</p>
		<input class="btn btn-primary" type="file" id="csv-upload" accept="text/csv"></input>



		<table class="table table-striped">
			<thead>
				<tr><th>Filename</th><th>Title</th><th>Text</th><th>Url</th><th>Copyright</th><th>Status</th></tr>
			</thead>
			<tbody id="media">
			</tbody>
		</table>
		<p>3. Upload the files.</p>
		<input class="btn btn-primary" type="file" id="media-upload" multiple disabled accept=""></input>


		<!-- we upload a csv file. format is
			//filename title short desc, url, copyright -->

		<!-- once uploaded, we upload the files and one by one create the media, assign to the collection	-->
</div>


<script>

/* working:
	1) upload csv.
	2) check status againgst collection (get also files and check)
	2) upload files
	3) for each file upload: find corresponding line, upload (will create media if missing), or just replace and reprocess.
	4) if processing return status and warn.
*/
	

const files = document.getElementById('media-upload');
files.addEventListener('change', loadFile);

function loadFile(event) {
	const fileList = event.target.files;
	
	for(let file of fileList) {
		let target = null;
		for(let m of media) {
			if(m.file == file.name)
				target = m;
			}
		if(!target) {
			alert("File not found in the csv: " + file.name);
			return;
		}


		let data = new FormData();
		data.append("file", file);
		data.append('collection', <?=$collection->id?>);
		for(let field in target)
			data.append(field, target[field]);

		$.ajax({
			type: 'POST',
			url: '/collection/uploadFile',
			enctype: 'multipart/form-data',
			data: data,
			processData: false,
			contentType: false,
			success: function (data) {
				if(data.error) {
					alert(data.error);
				}
				//update status.
				updateStatus();
			}
		}); 


	}
}



let media = [];
const fileSelector = document.getElementById('csv-upload');
fileSelector.addEventListener('change', loadCsv);

function loadCsv(event) {
	const fileList = event.target.files;
	
	const reader = new FileReader();
	reader.addEventListener('load', (event) => {
		initCsv(event.target.result);
		updateStatus();
	});
	reader.readAsText(fileList[0]);
}

function initCsv(txt) {
	let data = CSVToArray(txt);
	let src = '';
	for(let r of data) {
		let m = {
			'file': r[0],
			'title': r[1],
			'description': r[2],
			'url': r[3],
			'copyright': r[4],
			'media_type': r[5]
		};
		media.push(m);
		src += `<tr><td>${r[0]}</td><td>${r[1]}</td><td>${r[2]}</td><td>${r[3]}</td><td>${r[4]}</td><td>${r[5]}</td></tr>`;
	}

	let tbody = document.getElementById('media');
	tbody.innerHTML = src;
	let create = document.querySelector('#media-upload');
	create.removeAttribute('disabled');
}

function updateStatus() {
	$.getJSON('/collection/status/<?=$collection->label?>', (d) => {
		if(d.error) {
			alert(d.error);
			return;
		}
		let files = d.files;
		let filenames = new Set();
		for(let f of files)
			filenames.add(f.filename);

		$('#media tr td:nth-child(1)').each(
			(id, elem) => {
			if(filenames.has(elem.textContent))
				elem.parentElement.style.background="green";
		});
	}).fail(function () { alert("Network error"); });
}


function CSVToArray(strData, strDelimiter) {
	strDelimiter = (strDelimiter || ",");

	// Create a regular expression to parse the CSV values.
	var objPattern = new RegExp(
		(
			// Delimiters.
			"(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +

			// Quoted fields.
			"(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +

			// Standard fields.
			"([^\"\\" + strDelimiter + "\\r\\n]*))"
		),
		"gi"
	);


	var arrData = [[]];
	var arrMatches = null;

	while (arrMatches = objPattern.exec(strData)) {

		var strMatchedDelimiter = arrMatches[1];

		if (strMatchedDelimiter.length && strMatchedDelimiter !== strDelimiter) {
			arrData.push([]);
		}

		var strMatchedValue;

		if (arrMatches[2]) {
			strMatchedValue = arrMatches[2].replace(new RegExp("\"\"", "g"), "\"");
		} else {
			strMatchedValue = arrMatches[3];
		}

		arrData[arrData.length - 1].push(strMatchedValue);
	}

	if(arrData.length > 0) {
		let last = arrData[arrData.length-1];
		if(last.length == 1 && last[0] == '')
		arrData.pop();
	}
	return (arrData);
}

</script>



