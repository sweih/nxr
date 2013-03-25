<?php
  $url = $_GET["u"];
  if ($url != "") {
    $url = base64_decode($url);
    header("Location: ".$url);
  }
  exit();
?>