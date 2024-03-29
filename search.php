<?php
declare(strict_types=1);
?>

<head>
   <?php
   if (isset($_POST['query'])) {
      $title = 'Search for "' . $_POST['query'] . '"';
   } else {
      $title = 'Search';
   }
   require('head.php');
   ?>
</head>
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
<?php
   // @TODO: Somehow make it so the selected scope
   //        persists when 'Submit' is clicked.
?>
<form action="search.php" method="post">
	<input type="text" name="query">&nbsp;<input type="submit"><br>
  <input type="radio" name="scope" checked="true" value="system">Systems
  <input type="radio" name="scope" value="device">Devices
  <input type="radio" name="scope" value="files">Filename
</form>
<?php
if (!isset($_POST['scope'])) {
   require('footer.php');
   return;
}

/**
 * Cleans a string
 *
 * @param string $data The string to be cleaned
 *
 * @return string Clean string
 */

function cleanInput(string $data): string {
   $data = trim($data);
   $data = stripslashes($data);

   return htmlspecialchars($data);
}

function listName(string $list, array $row): string {
   if ($list === 'system' || $list === 'device') {
      if ($list === 'system') {
         $output = $row['manufacturer'] . ' ' . $row['model'];
      } else {
         $output = $row['manufacturer'] . ' ' . $row['device_name'];
      }

      return '<h2><a href="/' . $list . '.php?id=' . $row['id'] . '">'
         . $output
         . '</a></h2>';
   } else if ($list === 'files') {
      $date = new DateTime($row['date']);

      return '<p><b>Filename:</b> ' . $row['file_name'] . '<br><b>Version:</b> ' . $row['version'] . '<br><b>Date:</b> ' . $date->format('d M Y') .
         '<br><a href="/download.php?id=' . $row['id'] . '"><button type="button">Download</button></a></p>';
   }

   return '';
}

$queryScope = cleanInput($_POST['scope']);

$query      = null;
$cleanquery = '';
if (isset($_POST['query'])) {
   $query      = '%' . $_POST['query'] . '%';
   $cleanquery = str_replace('%', '', $query);
}

require('creds.php');

// Create connection
$conn = new mysqli(CONF["servername"], CONF["username"], CONF["password"], CONF["dbname"]);
// Check connection
if ($conn->connect_error) {
   die('Connection failed: ' . $conn->connect_error);
}

if ($query === null) {
   $conn->close();

   return;
}

$result = false;
$list   = '';
$querytime = microtime(true);
switch ($queryScope) {
   case 'system':
   {
      $stmt = $conn->prepare('SELECT id, manufacturer, model FROM systems WHERE model LIKE ?');
      $stmt->bind_param('s', $query);
      $stmt->execute();
      $result = $stmt->get_result();
      break;
   }
   case 'device':
   {
      $stmt = $conn->prepare('SELECT id, manufacturer, device_name FROM devices WHERE device_name LIKE ? OR manufacturer LIKE ?');
      $stmt->bind_param('ss', $query, $query);
      $stmt->execute();
      $result = $stmt->get_result();
      break;
   }
   case 'files':
   {
      $stmt = $conn->prepare("SELECT id, file_name, file_path, version, date FROM files WHERE file_name LIKE ?");
      $stmt->bind_param('s', $query);
      $stmt->execute();
      $result = $stmt->get_result();
   }
}
$querytime = microtime(true) - $querytime;
if ($result !== false) {

   echo $result->num_rows . ' results for "' . $cleanquery . '" in ' . $queryScope . ' (took ' . round($querytime, 5) . 'ms)';

   if ($result->num_rows > 0) {
      // output data of each row
      foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
         echo '<hr>';
         echo listName($queryScope, $row);
      }
   } else {
      echo 'No Results for ' . $cleanquery;
   }
}
$conn->close();
require('footer.php');
?>
