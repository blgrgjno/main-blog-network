<?php
/**
 * Template Name: Uten rammeverk
 *
 * Side uten noe rammeverk
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php the_title(); ?></title>
	<style type="text/css">
		body, p {margin:0;padding:0;background:#000;text-align:center;}
	</style>
</head>
<body>
<?php
	the_post();
	the_content();
?>
</body>
</html>