<?php
  # booleans are below, change them to change functionality
  $index = false; # used to see if the current page is the index, shouldn't have to be set manually.
  $exists = false; # used to see if the current page exists, shouldn't have to be set manually.

  $base = "/var/www/shortcuts.natfan.io";
  $posts = "$base/docs";

  require_once '/var/www/cdn.natfan.io/public_html/php/parsedown/Parsedown.php';
  $parse = new Parsedown();
  
  $site = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'];
  $query = $_SERVER['QUERY_STRING'];
  $query = substr($query, 2, strlen($query));
  $path = "$posts/$query.md";
  if ($query == "" or $query == "index") {
    $index = true; # the query is blank (index)
  }

  if ($index) {
    $program = "Index";
  } else {
    $program = $query;
    $program = preg_replace('(_|-)', ' ', $program);
    $program = ucwords($program);
  }

  if (file_exists("$path")) {
    $exists = true;
    $file = fopen("$path", "r");
    $data = fread($file, filesize("$path"));
    $markdown = $parse->text($data);
    fclose($file);
  }

  function endsWith($haystack, $needle) {
    return substr($haystack, -strlen($needle)) === $needle;
  }

  $post_files = array_diff(scandir($posts), array('..', '.'));
  $filetypes = array('.md');
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="https://cdn.natfan.io/css/github.css">
    <title>AwesomeShortcut | <?php echo $program; ?></title>
  </head>

  <body>
  <?php
    echo "<h1>AwesomeShortcuts<h1>";
    echo "<h2>Menu | Bar | Goes | Here</h2>";
    if ($index) {
      echo "<h3>Index</h3>";
      foreach ($post_files as $post_key => $file) {
        foreach ($filetypes as $filetypes_key => $filetype) {
          $filetype = strtolower($filetype);
          if (endsWith($file, $filetype)) {
            $name = $file;
            $name = preg_replace('(_|-)', ' ', $name);
            $name = ucwords($name);
            $name = substr($name, 0, strpos($name, "$filetype"));
            $ffile = substr($file, 0, strpos($file, "$filetype"));
            echo "<a href='$ffile'>$name</a><br>";
          }
        }
      }
    } else {
      if ($exists) {
        echo "<h3>$program</h3>";
        echo $markdown;
      } else {
        echo "<h2>File does not exist.</h1>";
        echo "<h6>Sorry about that.</h2>";
        echo "$site/$query";
      }
    }
  ?>
  </body>
</html>
