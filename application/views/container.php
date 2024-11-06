<!DOCTYPE html>
<html lang="en" style="height:100%">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?=$title?></title>

	<link rel="stylesheet" href="/css/bootstrap.min.css?1">
	<link rel="stylesheet" href="/css/fontawesome-all.min.css?1">
<!--	<link rel="stylesheet" href="https://use.fontawesome.c77om/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous"> -->
	<? if(in_array($page, array('manage', 'upload'))) { ?>
<link rel="stylesheet" href="/css/simplemde.min.css">

	<? } ?>


	<style>
a:link:not(.btn), a:visited:not(.btn), a:hover, a:active { color: #337ab7; text-decoration:none; }
a:hover, a:active { color: #539ad7; }
nav { border-bottom: 1px solid #eee; margin-bottom:1em; }
.navbar { padding-bottom: 0px; }
.nav-link, .navbar-brand { padding-bottom: 0px; }

.CodeMirror, .CodeMirror-scroll { min-height: 150px; }
.editor-toolbar:before { margin-bottom: 0px }
.editor-toolbar:after { margin-top: 0px }
.editor-toolbar { padding: 0px }

/* lazy load fade-in */
.lazy-hidden{opacity:0}.lazy-loaded{-webkit-transition:opacity .3s;-moz-transition:opacity .3s;
-ms-transition:opacity .3s;-o-transition:opacity .3s;transition:opacity .3s;opacity:1}
.lazy-hidden{background:url(/css/loading.gif) 50% 50% no-repeat #eee}

	</style>
	<script src="/js/jquery-3.3.1.min.js"></script>
	<script src="/js/bootstrap.min.js?1"></script>
	<script src="/js/bootbox.min.js"></script>
	<script src="/js/jquery.lazyloadxt.min.js"></script>
	<script> alert = bootbox.alert;  $(window).lazyLoadXT(); </script>

</head>

<body>

	<header> <!-- Fixed navbar -->
		<nav class="navbar navbar-expand-md navbar-light bg-light">
			<div class="container">
			<a class="navbar-brand" href="/">Visual Media Service</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarCollapse">
				<ul class="navbar-nav mr-auto">
				<li class="nav-item<?=$page=='browse'	?' active':''?>"><a class="nav-link" href="/browse">Browse</a>
				<li class="nav-item<?=$page=='upload'	?' active':''?>"><a id="upload" class="nav-link" href="/upload">Upload</a>
				<li class="nav-item<?=$page=='help'		?' active':''?>"><a class="nav-link" href="/help">Help</a>
				<li class="nav-item<?=$page=='contacts'?' active':''?>"><a class="nav-link" href="/about">About</a>
				</ul>

				<ul class="nav navbar-nav navbar-right">
<? if($user) { ?>
				<li	role="presentation" class="dropdown">
					<a class="nav-link dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" href="#">
					<?=$user->name? $user->name : ($user->username? $user->username : $user->email)?><span class="caret"></span></a>
					<ul class="dropdown-menu">
					<li class="dropdown-item"><a class="nav-link" href="/profile">Profile</a></li>
<?		if($user->role == 'admin') { ?>
					<li class="dropdown-item"><a class="nav-link" href="/admin">Admin</a></li>
					<li class="dropdown-item"><a class="nav-link" href="/admin/users">Users</a></li>
					<li class="dropdown-item"><a class="nav-link" href="/admin/jobs">Jobs</a></li>
					<li class="dropdown-item"><a class="nav-link" href="/admin/media">Media</a></li>
<? } ?>
					<li role="separator" class="divider"></li>
					<li class="dropdown-item"><a class="nav-link" href="/logout">Logout</a></li>
					</ul>
				</li>
<? } else {?>
				<li class=""><a class="btn btn-sm btn-info" data-toggle="modal" data-target="#login" href="/login">Login</a></li>
<? } ?>
				</ul>
			</div>
			</div>
		</nav>
	</header>

<div class="container">

<?=$content?>

</div>
<div class="container-fluid">
	<div class="footer text-center">
		<hr/>
		<p><a href="http://vcg.isti.cnr.it">Visual Computing Lab</a> - ISTI - CNR</p>
	</div>

</div> <!-- container -->


<? if(!$user) { 

$baseURL = "https://parthenos.d4science.org/group/parthenos-gateway/authorization?";

$host = 'https://'.$_SERVER['HTTP_HOST'];

$data = array(
	'client_id' => "a4dcada3-5b17-4b85-8fa4-6281f12507c4",
	'redirect_uri' => "$host/login",
	'state' => 'd4science'
);

$uri = $baseURL . http_build_query($data);
$data['scope'] = "/d4science.research-infrastructures.eu";
$uriscope = $baseURL . http_build_query($data);


$google_url = 'https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=257893829634-3l1e27frsg2vp8d014sumd93bgtrtg7g.apps.googleusercontent.com&redirect_uri='.urlencode($host).'%2Flogin&state=google&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile&approval_prompt=force';

$orcid_url = 'https://orcid.org/oauth/authorize?'.
	'client_id=APP-INSSY2VKBR9EV73P&response_type=code&'.
	'scope=/authenticate&'.
	'redirect_uri=https://visual.ariadne-infrastructure.isti.cnr.it/login&state=orcid';
?>

<style>
#login .row { background-color:white; }
#login h3 { line-height: 300%; }
#login .btn { color:white; opacity:1; }
#login .btn:hover { color:white; opacity:0.8; }
#login .separator { line-height:400%; }
#login hr { margin-top: 2rem; }
#login .social { width:80%; }
.btn-facebook {background: #3b5998;}
.btn-twitter {background: #00aced;}
.btn-google {background: #c32f10;}
.btn-d4science {background: #427585;}
</style>

<div class="modal" id="login">
	<div class="modal-dialog">
	<div class="modal-content">

		<div class="row text-center">
			<div class="col-12">
				<h3>Login</h3>
			</div>
			<div class="col-4"><a href="<?=$google_url?>" class="btn btn-google social">Google</a></div>
			<div class="col-4"><a href="<?=$orcid_url?>" class="btn btn-orcid social">Orcid</a></div>
			<div class="col-4"><a href="<?=$uri?>" class="btn btn-d4science social">D4Science</a></div> 

			<div class="col-12 separator d-flex"><hr style="width:45%"/><p>or</p><hr style="width:45%"/></div>

			<div class="col-12">
				<form method="POST" action="/passwordless" id="loginform">
				<div class="row">
					<div class="col">
						<p>We will send you an email with a link you can follow to log in.</p>
						<div class="form-group">
							<input type="email" name="email" required class="form-control" placeholder="Your email">
						</div>
						<div class="form-group">
							<button class="btn btn-info btn-block btn-lg" type="submit">Request login link</button>
						</div>
						
					</div>
				</div>
				</form>
			</div>

		</div>
	</div>
	</div>
</div>

<? } ?>

<? if(!$user) { ?>
<script>
$('#upload').click(function(e) {
	$('#login').modal('toggle');
	e.preventDefault();
});
</script>
<? } ?>

<!--
<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-92319210-1', 'auto');
	ga('send', 'pageview');

</script>
-->
</body>
</html>
