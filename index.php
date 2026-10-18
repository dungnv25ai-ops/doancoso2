<?php
require 'db.php';
go(isset($_SESSION['id'])?'trangchu.php':'dangnhap.php');
