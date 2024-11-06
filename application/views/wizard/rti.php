<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="stylesheet" href="css/relight.css">

<? if(count($media->files) > 1) { ?>
<link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
<? } ?>

<style>
html, body, .relight { width:100%; height:100%; padding:0px; margin:0px; }

.relight-home     { background-image: url(skins/<?=$options->skin?>/home.png); }
.relight-zoomin   { background-image: url(skins/<?=$options->skin?>/zoomin.png); }
.relight-zoomout  { background-image: url(skins/<?=$options->skin?>/zoomout.png); }
.relight-rotate   { background-image: url(skins/<?=$options->skin?>/rotate.png); }
.relight-light    { background-image: url(skins/<?=$options->skin?>/light.png); }
.relight-light_on { background-image: url(skins/<?=$options->skin?>/light_on.png); }
.relight-full     { background-image: url(skins/<?=$options->skin?>/full.png); }
.relight-full_on  { background-image: url(skins/<?=$options->skin?>/full_on.png); }
.relight-info     { background-image: url(skins/<?=$options->skin?>/help.png); }

.relight {
<? 
$type = $options->background->type;
$background = $options->background;

switch($type) {
	case 'flat':   echo("background-color: {$background->color0}\n"); break;
	case 'linear': echo("background: linear-gradient(".$background->color0.", ".$background->color1.")\n"); break;
	case 'radial': echo("background: radial-gradient(".$background->color0.", ".$background->color1.")\n"); break;
	case 'image':  echo("background: url(\"/skins/backgrounds/{$background->image}\") center / cover\n"); break;
}
?>
}

<? if($media->media_type == 'album') { ?>
.relight-pagemap { right: 240px; } 

.relight-thumb {  
	cursor:pointer; 
	position:relative; margin-bottom:5px; margin-right:5px;
	float:left;

} 
.relight-thumb p { position:absolute; top: 0px; right: 0px; color:white; 
	margin:0px; padding:8px 8px 48px 4px; background: linear-gradient(45deg, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.0), rgba(0, 0, 0, 0.4));
}

.relight-thumbs img { display:block; }
.relight-thumbs {
/*	max-width: 256px; */
	height:100%;
	padding:5px 0px 5px 5px;
	box-sizing:border-box;
	position:relative;
}

.mCSB_inside > .mCSB_container {
	margin-right: 10px;
}


<? } ?>

.relight {
	display:flex;
}

.relight canvas {
	width: 0px;
	flex-grow: 2;
}

@media screen and (max-width: 576px) { .relight-thumbs { display:block; max-width: 105px; } .relight-thumbs img { width:80px; } }
@media screen and (min-width: 576px) { .relight-thumbs { display:block; max-width: 105px; } .relight-thumbs img { width:80px; } }
@media screen and (min-width: 768px) { .relight-thumbs { display:block; max-width: 149px; } .relight-thumbs img { width:124px; } }
@media screen and (min-width: 992px) { .relight-thumbs { display:block; max-width: 231px; } .relight-thumbs img { width:206px; }}

</style>

</head>



<body>

	<div class="relight">
	</div>

	<div class="relight-info-content">
		<h3><?=$media->title?></h3>
		<hr/>
		<?=$media->description?>
	</div>

<? if($media->media_type == 'album') { ?>
	<div class="relight-thumbs">
<?	$count = 0;
	foreach($media->files as $file) { ?>
		<div class="relight-thumb"><img data-ordering="<?=$count++?>" loading="lazy" src="<?=$options->model?><?=$file->label?>.jpg">
		<p><?=$count?></p></div>
	<? } ?>
	</div>
<? } ?>

</body>

<? if(count($media->files) > 1) { ?>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/jquery.mCustomScrollbar.concat.min.js"></script>


<script>
$(window).on('load',function() {
	$(".relight-thumbs").mCustomScrollbar({
		axis: 'y',
		scrollInertia: 200,
		mouseWheel:{ deltaFactor: 100, scrollAmount: 100 },
		scrollbarPosition: 'inside',
		scrollButtons: { enable: true },
		advanced: { updateOnImageLoad: true },
		theme: 'dark-3',
		callbacks:{ whileScrolling: function(){ $(window).lazyLoadXT(); } }
	});
});
</script>

<? } ?>

<script src="js/hammer.min.js"></script>
<script src="js/relight-viewer.min.js"></script>
<script>

<? 
$filenames = array();
foreach($media->files as $file) {
	if($file->format == $media->media_type || $media->media_type == 'album')
		$filenames[] = "'$file->label'";
} ?>

var files = [ <?=implode(",\n", $filenames)?> ];
var current = 0;
var relight = new RelightViewer('.relight', { 


<? if($media->media_type == 'img' || $media->media_type == 'album') { ?>
	url:"<?=$options->model?>",
	notool: ["light", "normals"],
	img: files[current],
	lighting: false,
<? } else { ?>
	url:"<?=$options->model?>" + files[0],
<? } ?>
	layout:"deepzoom",
<? if($media->media_type != 'rti') { ?>
	pagemap: { thumb: files[current] + ".jpg", autohide:1000 },
<? } ?>
<? if($options->mm) { ?>
	scale: <?=$options->mm?>,
<? } ?>
//	background: [0.7, 0.7, 0.7, 1],
	preserveDrawingBuffer: true
});




var div = document.querySelector(".relight");
var thumbstrip = document.querySelector(".relight-thumbs");
if(files.length > 1 && thumbstrip) {
	thumbstrip.remove();
	div.appendChild(thumbstrip);
	//this changed size of the canvas!
	var canvas = document.querySelector('.relight canvas');
	relight.resize(canvas.offsetWidth, canvas.offsetHeight);

	var thumbs = document.querySelectorAll(".relight-thumbs img");
	for(var i = 0; i < thumbs.length; i++) {
		var t = thumbs[i];
		t.onclick = function(e) {
			var c = e.target.getAttribute('data-ordering');
			if(c != current) {
				current = c; 
				update();
			}
		}
	}
}

<? if($media->media_type == 'album') { ?>

function next() {
	if(current == files.length-1)
		return;
	current++;
	update();
}

function previous() {
	if(current == 0)
		return;
	current--;
	update();
}

function update() {
	relight.layers[0].img = files[current];
	relight.layers[0].loadInfo({type: 'img', colorspace: null, width: 0, height: 0, nplanes: 1 });
	$('.relight-pagemap').css('background-image', 'url("<?=$options->model?>' + files[current] + '.jpg")');

}

<? } else { ?>
<? } ?>

</script>

</html>
