<?php
declare(strict_types=1);
?>
<html>
<head>
   <?php 
   $title = 'Statistics';
   require('head.php'); ?>
</head>
<body>
<a href="/">
	<table>
		<tr>
			<td><img src="favicon.png" width="50"></td>
			<td><h1 style="margin: 0">NetDrivers</h1><i>Archiving Drivers Since February 2022</i></td>
		</tr>
	</table>
</a>
<hr>
<?php
include('nav.html');
echo '<hr/>';
require('creds.php');

// Create connection
$conn = new mysqli(CONF["servername"], CONF["username"], CONF["password"], CONF["dbname"]);
// Check connection
if ($conn->connect_error) {
   $diemsg = '<pre><i>Unable to retrieve database statistics!</i></pre>';
   die($diemsg);
}

$result = $conn->query('SELECT name, count FROM stats');

if ($result !== false) {
	echo '<table border="1"><tr><th>Item</th><th>Count</th><tr>';
	while ($row = $result->fetch_assoc()) {
		echo '<tr><td>' . $row['name'] . '</td><td>' . $row['count'] . '</td></tr>';
	}
	echo '</table>';
}

$conn->close();
require('footer.php');
?>
</body>
</html>
