<?php
include 'config.php';
include 'SimpleWebCrawler.php';

/*
 *  Connect to database
 */
mysql_connect($config_DBserver, $config_DBuser, $config_DBpassword) or die ("No connection to database.");
mysql_select_db($config_DBuser) or die ("The database does not exists.");

/*
 * Crawler
 */
$simpleWebCrawler = new SimpleWebCrawler();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Simple PHP Crawler</title>
	<style type="text/css">
		p {
			font-size: small;
		}
		.style_green {
			color: #006600;
		}
		.style_red {
			color: #FF0000;
		}
		.style_blue {
			color: #0000FF;
		}
		.style_normal {
			font-weight: bold;
		}
	</style>
</head>

<body>

	<form action="" method="post" enctype="application/x-www-form-urlencoded" name="form">
		<input type="url" name="url" value="<?php echo $simpleWebCrawler->get_next_url(); ?>">
		<button type="submit">Crawl</button> <a href=""><button>Stop</button></a>
	</form>
	<hr>
	<p>
	<?php
		if($_POST['url'] != ''){
			$simpleWebCrawler->crawl_start($_POST['url']);
		}
	?>
	</p>
</body>
</html>