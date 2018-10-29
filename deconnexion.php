<?php

    require_once("inc/init.php");

    unset($_SESSION['user']);

    header("location:index.php");