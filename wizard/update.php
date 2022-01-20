<?

#$config = $_POST['config'];

$config = file_get_contents('php://input');

if(!$config) {
	header('Content-type: application/json');
	echo file_get_contents('options.json');
	return;
}

$config = json_decode($config, true);
$config = json_encode($config, JSON_PRETTY_PRINT );


file_put_contents('options.json', $config);
?>
