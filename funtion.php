<?php
date_default_timezone_set('UTC');
session_start();
require_once 'lib/Connection.php';

define('base_url', 'http://localhost/my-wash/');

function setMessage($message) {
  setcookie('message', $message, time() + 1, "/");
}

function generateRandomString($length = 2) {
  if(isset($_COOKIE['kwitansi'])) {
    $kwitansi = $_COOKIE['kwitansi'];
  } else {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    $kwitansi = "MYW-" . $randomString . date("YmdHis");
    setcookie('kwitansi', $kwitansi, time() + 600000, "/");
  }
  return $kwitansi;
}

function getTimeNow() {
  $date = date("Y/m/d");;
  return $date;
}

function rupiah($angka){
	$hasil_rupiah = "Rp" . number_format($angka,0,',','.');
	return $hasil_rupiah;
}