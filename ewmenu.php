<!-- Begin Main Menu -->
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(6, "mi_home_php", $Language->MenuPhrase("6", "MenuText"), "home.php", -1, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}home.php'), FALSE, TRUE);
$RootMenu->AddMenuItem(4, "mci_Master", $Language->MenuPhrase("4", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(1, "mi_tb_lantai", $Language->MenuPhrase("1", "MenuText"), "tb_lantailist.php", 4, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_lantai'), FALSE, FALSE);
$RootMenu->AddMenuItem(2, "mi_tb_lokasi", $Language->MenuPhrase("2", "MenuText"), "tb_lokasilist.php", 4, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_lokasi'), FALSE, FALSE);
$RootMenu->AddMenuItem(3, "mi_tb_device", $Language->MenuPhrase("3", "MenuText"), "tb_devicelist.php", 4, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_device'), FALSE, FALSE);
$RootMenu->AddMenuItem(5, "mi_tb_merk", $Language->MenuPhrase("5", "MenuText"), "tb_merklist.php", 4, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_merk'), FALSE, FALSE);
$RootMenu->AddMenuItem(7, "mi_tb_assembly", $Language->MenuPhrase("7", "MenuText"), "tb_assemblylist.php", -1, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_assembly'), FALSE, FALSE);
$RootMenu->AddMenuItem(-1, "mi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
