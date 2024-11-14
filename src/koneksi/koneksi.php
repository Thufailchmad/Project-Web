<?php
function dbConnection() {
  $db = new PDO("mysql:host=localhost;dbname=toko_listrik", "root", "");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $db;
}