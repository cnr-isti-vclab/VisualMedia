<div class="row">
	<div class="col-12">
		<h2>Users</h2>

		<table class="table" id="usertable">
			<thead>
				<th>id</th><th>username</th><th>email</th><th>name</th><th>institution</th><th>created</th>
			</thead>
			<tbody>
<? foreach($users as $u) { ?>
				<tr>
					<td><a href="/profile/<?=$u->id?>"><?=$u->id?></a></td>
					<td><?=$u->username?></td>
					<td><?=$u->email?></td>
					<td><?=$u->name?></td>
					<td><?=$u->institution?></td>
					<td><?=$u->created?></td>
				</tr>
	
<? } ?>
			</tbody>
		</table>
	</div>
</div>

<script src="/js/pbTable.min.js"></script>
<script>
$(document).ready(function(e) {
	$('#usertable').pbTable({
		selectable: true,
		sortable:true,
		toolbar:{
			enabled:true,
			filterBox:true,
			tags:[],
			buttons:[]
		}
	});
});
</script>
