<?php
declare(strict_types=1);
?>
<html>
<head>
   <?php
   $title = 'Home';

   require('head.php'); ?>
</head>

<body>
<a href="/">
	<table>
		<tr>
			<td><img src="/favicon.png" width="50"></td>
			<td><h1 style="margin: 0">NetDrivers</h1><i>Archiving Drivers Since February 2022</i></td>
		</tr>
	</table>
</a>
<hr>
<?php include('nav.html'); ?>
<hr>
<p>This page currently has no content. Use the above links to navigate the site!</p>
<?php
require('footer.php');
?>
</body>
</html>
