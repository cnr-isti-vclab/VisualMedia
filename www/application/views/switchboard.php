
<div id="status">
<h1>Your model is being processed.</h1>
</div>


<script>

let model = '<?=htmlspecialchars($media->url)?>';
function setFailed(error) {
	let html = `
<h1>The 3D model could not be processed.</h1>
<p>${error}</p>`
	$('#status').html(html);
}

function setStatus(status) {
	let html;
	switch(status) {
	case 'download':
		html = `<h1>The 3D  model <a href="${model}">${model}</a> is being downloaded.</h1>`;
		break;
	case 'on queue':
		html = `<h1>The 3D model <a href="${model}">${model}</a> is waiting to be converted.</h1>
			<p>This step might take a few minutes depending on other models in queue.</p>`;
		break;
	case 'processing':
		html = `<h1>The 3D model <a href="${model}">${model}</a> is being converted.</h1>
			<p>This step might take a few minutes depending on the size of the model.</p>`;
		break;
	default:
		html = status;
	}

	$('#status').html(html);
}

function checkStatus() {
	$.get('/media/status/<?=$media->secret?>', 
		function(e) {
			
			if(e.status) {
				if(e.status == 'ready')
					window.location.reload();
				if(e.status == 'failed') {
					setFailed(e.error);
					return;
				}
			}
			setStatus(e.status);
			setTimeout(checkStatus, 1000);
		}
	);
}

checkStatus();
</script>
