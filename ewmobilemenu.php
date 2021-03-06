<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(6, "mmi_home_php", $Language->MenuPhrase("6", "MenuText"), "home.php", -1, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}home.php'), FALSE, TRUE);
$RootMenu->AddMenuItem(4, "mmci_Master", $Language->MenuPhrase("4", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(1, "mmi_tb_lantai", $Language->MenuPhrase("1", "MenuText"), "tb_lantailist.php", 4, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_lantai'), FALSE, FALSE);
$RootMenu->AddMenuItem(2, "mmi_tb_lokasi", $Language->MenuPhrase("2", "MenuText"), "tb_lokasilist.php", 4, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_lokasi'), FALSE, FALSE);
$RootMenu->AddMenuItem(8, "mmi_tb_departemen", $Language->MenuPhrase("8", "MenuText"), "tb_departemenlist.php", 4, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_departemen'), FALSE, FALSE);
$RootMenu->AddMenuItem(3, "mmi_tb_device", $Language->MenuPhrase("3", "MenuText"), "tb_devicelist.php", 4, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_device'), FALSE, FALSE);
$RootMenu->AddMenuItem(5, "mmi_tb_merk", $Language->MenuPhrase("5", "MenuText"), "tb_merklist.php", 4, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_merk'), FALSE, FALSE);
$RootMenu->AddMenuItem(7, "mmi_tb_assembly", $Language->MenuPhrase("7", "MenuText"), "tb_assemblylist.php", -1, "", IsLoggedIn() || AllowListMenu('{32C4CE20-1B57-4C82-8475-08C0302816A6}tb_assembly'), FALSE, FALSE);
$RootMenu->AddMenuItem(-1, "mmi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mmi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
