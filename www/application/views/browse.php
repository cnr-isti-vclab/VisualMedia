<style>
div[data-type] button {
	padding-left:6px;
	padding-right:6px;
}
.btn-group {
	width:100%;
}
</style>

<form id='searchform'>
	<input type="hidden" name="3d" value="1"/>
	<input type="hidden" name="rti" value="1"/>
	<input type="hidden" name="album" value="1"/>
	<input type="hidden" name="img" value="1"/>

<div class="row mt-4">
	<div class="col-lg-4 col-md-12 mb-2">
		<div class="input-group">
			 <input type="text" class="form-control" name='search' placeholder="Free text search...">
			<div class="input-group-append">
				<button class="btn btn-primary" id="searchbutton"><i class="fas fa-search"></i></button>
			</div>
		</div>
	</div>

	<div class="col-lg-2 col-md-3 col-sm-6 mb-2">
		<div class="btn-group" data-type="3d">
			<button class="btn btn-primary"><i class="fas fa-check"></i></button>
			<button class="btn btn-outline-secondary btn-block"> 3D models</button>
		</div>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-6">
		<div class="btn-group" data-type="rti">
			<button class="btn btn-primary"><i class="fas fa-check"></i></button>
			<button class="btn btn-outline-secondary btn-block"> Relightables</button>
		</div>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-6">
		<div class="btn-group" data-type="album">
			<button class="btn btn-primary"><i class="fas fa-check"></i></button>
			<button class="btn btn-outline-secondary btn-block"> Image sets</button>
		</div>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-6">
		<div class="btn-group" data-type="img">
			<button class="btn btn-primary"><i class="fas fa-check"></i></button>
			<button class="btn btn-outline-secondary btn-block"> Images</button>
		</div>
	</div>
</div>
</form>

<div id="browsertable">
<?=$browsertable?>
</div>

<script>
$('#searchbutton').click(function() {

});

$('div[data-type]').click(function(e) {
	var type = $(this).attr('data-type');
	var input = $('input[name=' + type +']');
	var on = input.val();
	$(this).find('i').css('visibility', on == '1' ?'hidden':'visible');
	input.val(on == '1' ?'0':'1');

//	e.preventDefault();
//	return false;
});

$('#searchform').submit(function(e) {
	var data = $(this).serializeArray();
	$.post('/search', data, function(doc) {
		$('#browsertable').html(doc);
		$('#browsertable img').lazyLoadXT();
	})
	.fail(function() {
		alert("Oooops! We have some problem here...");
	});
	e.preventDefault();
	return false;
});
</script>

