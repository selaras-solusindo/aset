<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "tb_assemblyinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$tb_assembly_add = NULL; // Initialize page object first

class ctb_assembly_add extends ctb_assembly {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{32C4CE20-1B57-4C82-8475-08C0302816A6}";

	// Table name
	var $TableName = 'tb_assembly';

	// Page object name
	var $PageObjName = 'tb_assembly_add';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}
	var $AuditTrailOnAdd = TRUE;
	var $AuditTrailOnEdit = FALSE;
	var $AuditTrailOnDelete = FALSE;
	var $AuditTrailOnView = FALSE;
	var $AuditTrailOnViewData = FALSE;
	var $AuditTrailOnSearch = FALSE;

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (tb_assembly)
		if (!isset($GLOBALS["tb_assembly"]) || get_class($GLOBALS["tb_assembly"]) == "ctb_assembly") {
			$GLOBALS["tb_assembly"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tb_assembly"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tb_assembly', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);
	}

	//
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("tb_assemblylist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->merk_id->SetVisibility();
		$this->device_id->SetVisibility();
		$this->unit_name->SetVisibility();
		$this->keterangan->SetVisibility();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $tb_assembly;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($tb_assembly);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();

			// Handle modal response
			if ($this->IsModal) {
				$row = array();
				$row["url"] = $url;
				echo ew_ArrayToJson(array($row));
			} else {
				header("Location: " . $url);
			}
		}
		exit();
	}
	var $FormClassName = "form-horizontal ewForm ewAddForm";
	var $IsModal = FALSE;
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;
		global $gbSkipHeaderFooter;

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["assembly_id"] != "") {
				$this->assembly_id->setQueryStringValue($_GET["assembly_id"]);
				$this->setKey("assembly_id", $this->assembly_id->CurrentValue); // Set up key
			} else {
				$this->setKey("assembly_id", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		} else {
			if ($this->CurrentAction == "I") // Load default values for blank record
				$this->LoadDefaultValues();
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("tb_assemblylist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "tb_assemblylist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "tb_assemblyview.php")
						$sReturnUrl = $this->GetViewUrl(); // View page, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD; // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->merk_id->CurrentValue = NULL;
		$this->merk_id->OldValue = $this->merk_id->CurrentValue;
		$this->device_id->CurrentValue = NULL;
		$this->device_id->OldValue = $this->device_id->CurrentValue;
		$this->unit_name->CurrentValue = NULL;
		$this->unit_name->OldValue = $this->unit_name->CurrentValue;
		$this->keterangan->CurrentValue = NULL;
		$this->keterangan->OldValue = $this->keterangan->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->merk_id->FldIsDetailKey) {
			$this->merk_id->setFormValue($objForm->GetValue("x_merk_id"));
		}
		if (!$this->device_id->FldIsDetailKey) {
			$this->device_id->setFormValue($objForm->GetValue("x_device_id"));
		}
		if (!$this->unit_name->FldIsDetailKey) {
			$this->unit_name->setFormValue($objForm->GetValue("x_unit_name"));
		}
		if (!$this->keterangan->FldIsDetailKey) {
			$this->keterangan->setFormValue($objForm->GetValue("x_keterangan"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->merk_id->CurrentValue = $this->merk_id->FormValue;
		$this->device_id->CurrentValue = $this->device_id->FormValue;
		$this->unit_name->CurrentValue = $this->unit_name->FormValue;
		$this->keterangan->CurrentValue = $this->keterangan->FormValue;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->assembly_id->setDbValue($rs->fields('assembly_id'));
		$this->merk_id->setDbValue($rs->fields('merk_id'));
		if (array_key_exists('EV__merk_id', $rs->fields)) {
			$this->merk_id->VirtualValue = $rs->fields('EV__merk_id'); // Set up virtual field value
		} else {
			$this->merk_id->VirtualValue = ""; // Clear value
		}
		$this->device_id->setDbValue($rs->fields('device_id'));
		if (array_key_exists('EV__device_id', $rs->fields)) {
			$this->device_id->VirtualValue = $rs->fields('EV__device_id'); // Set up virtual field value
		} else {
			$this->device_id->VirtualValue = ""; // Clear value
		}
		$this->unit_name->setDbValue($rs->fields('unit_name'));
		$this->keterangan->setDbValue($rs->fields('keterangan'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->assembly_id->DbValue = $row['assembly_id'];
		$this->merk_id->DbValue = $row['merk_id'];
		$this->device_id->DbValue = $row['device_id'];
		$this->unit_name->DbValue = $row['unit_name'];
		$this->keterangan->DbValue = $row['keterangan'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("assembly_id")) <> "")
			$this->assembly_id->CurrentValue = $this->getKey("assembly_id"); // assembly_id
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$this->OldRecordset = ew_LoadRecordset($sSql, $conn);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// assembly_id
		// merk_id
		// device_id
		// unit_name
		// keterangan

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// assembly_id
		$this->assembly_id->ViewValue = $this->assembly_id->CurrentValue;
		$this->assembly_id->ViewCustomAttributes = "";

		// merk_id
		if ($this->merk_id->VirtualValue <> "") {
			$this->merk_id->ViewValue = $this->merk_id->VirtualValue;
		} else {
			$this->merk_id->ViewValue = $this->merk_id->CurrentValue;
		if (strval($this->merk_id->CurrentValue) <> "") {
			$sFilterWrk = "`merk_id`" . ew_SearchString("=", $this->merk_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `merk_id`, `merk_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_merk`";
		$sWhereWrk = "";
		$this->merk_id->LookupFilters = array("dx1" => '`merk_nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->merk_id, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->merk_id->ViewValue = $this->merk_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->merk_id->ViewValue = $this->merk_id->CurrentValue;
			}
		} else {
			$this->merk_id->ViewValue = NULL;
		}
		}
		$this->merk_id->ViewCustomAttributes = "";

		// device_id
		if ($this->device_id->VirtualValue <> "") {
			$this->device_id->ViewValue = $this->device_id->VirtualValue;
		} else {
			$this->device_id->ViewValue = $this->device_id->CurrentValue;
		if (strval($this->device_id->CurrentValue) <> "") {
			$sFilterWrk = "`device_id`" . ew_SearchString("=", $this->device_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `device_id`, `device_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_device`";
		$sWhereWrk = "";
		$this->device_id->LookupFilters = array("dx1" => '`device_name`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->device_id, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->device_id->ViewValue = $this->device_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->device_id->ViewValue = $this->device_id->CurrentValue;
			}
		} else {
			$this->device_id->ViewValue = NULL;
		}
		}
		$this->device_id->ViewCustomAttributes = "";

		// unit_name
		$this->unit_name->ViewValue = $this->unit_name->CurrentValue;
		$this->unit_name->ViewCustomAttributes = "";

		// keterangan
		$this->keterangan->ViewValue = $this->keterangan->CurrentValue;
		$this->keterangan->ViewCustomAttributes = "";

			// merk_id
			$this->merk_id->LinkCustomAttributes = "";
			$this->merk_id->HrefValue = "";
			$this->merk_id->TooltipValue = "";

			// device_id
			$this->device_id->LinkCustomAttributes = "";
			$this->device_id->HrefValue = "";
			$this->device_id->TooltipValue = "";

			// unit_name
			$this->unit_name->LinkCustomAttributes = "";
			$this->unit_name->HrefValue = "";
			$this->unit_name->TooltipValue = "";

			// keterangan
			$this->keterangan->LinkCustomAttributes = "";
			$this->keterangan->HrefValue = "";
			$this->keterangan->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// merk_id
			$this->merk_id->EditAttrs["class"] = "form-control";
			$this->merk_id->EditCustomAttributes = "";
			$this->merk_id->EditValue = ew_HtmlEncode($this->merk_id->CurrentValue);
			if (strval($this->merk_id->CurrentValue) <> "") {
				$sFilterWrk = "`merk_id`" . ew_SearchString("=", $this->merk_id->CurrentValue, EW_DATATYPE_NUMBER, "");
			$sSqlWrk = "SELECT `merk_id`, `merk_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_merk`";
			$sWhereWrk = "";
			$this->merk_id->LookupFilters = array("dx1" => '`merk_nama`');
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->merk_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->merk_id->EditValue = $this->merk_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->merk_id->EditValue = ew_HtmlEncode($this->merk_id->CurrentValue);
				}
			} else {
				$this->merk_id->EditValue = NULL;
			}
			$this->merk_id->PlaceHolder = ew_RemoveHtml($this->merk_id->FldCaption());

			// device_id
			$this->device_id->EditAttrs["class"] = "form-control";
			$this->device_id->EditCustomAttributes = "";
			$this->device_id->EditValue = ew_HtmlEncode($this->device_id->CurrentValue);
			if (strval($this->device_id->CurrentValue) <> "") {
				$sFilterWrk = "`device_id`" . ew_SearchString("=", $this->device_id->CurrentValue, EW_DATATYPE_NUMBER, "");
			$sSqlWrk = "SELECT `device_id`, `device_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_device`";
			$sWhereWrk = "";
			$this->device_id->LookupFilters = array("dx1" => '`device_name`');
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->device_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->device_id->EditValue = $this->device_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->device_id->EditValue = ew_HtmlEncode($this->device_id->CurrentValue);
				}
			} else {
				$this->device_id->EditValue = NULL;
			}
			$this->device_id->PlaceHolder = ew_RemoveHtml($this->device_id->FldCaption());

			// unit_name
			$this->unit_name->EditAttrs["class"] = "form-control";
			$this->unit_name->EditCustomAttributes = "";
			$this->unit_name->EditValue = ew_HtmlEncode($this->unit_name->CurrentValue);
			$this->unit_name->PlaceHolder = ew_RemoveHtml($this->unit_name->FldCaption());

			// keterangan
			$this->keterangan->EditAttrs["class"] = "form-control";
			$this->keterangan->EditCustomAttributes = "";
			$this->keterangan->EditValue = ew_HtmlEncode($this->keterangan->CurrentValue);
			$this->keterangan->PlaceHolder = ew_RemoveHtml($this->keterangan->FldCaption());

			// Add refer script
			// merk_id

			$this->merk_id->LinkCustomAttributes = "";
			$this->merk_id->HrefValue = "";

			// device_id
			$this->device_id->LinkCustomAttributes = "";
			$this->device_id->HrefValue = "";

			// unit_name
			$this->unit_name->LinkCustomAttributes = "";
			$this->unit_name->HrefValue = "";

			// keterangan
			$this->keterangan->LinkCustomAttributes = "";
			$this->keterangan->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->merk_id->FldIsDetailKey && !is_null($this->merk_id->FormValue) && $this->merk_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->merk_id->FldCaption(), $this->merk_id->ReqErrMsg));
		}
		if (!$this->device_id->FldIsDetailKey && !is_null($this->device_id->FormValue) && $this->device_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->device_id->FldCaption(), $this->device_id->ReqErrMsg));
		}
		if (!$this->unit_name->FldIsDetailKey && !is_null($this->unit_name->FormValue) && $this->unit_name->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->unit_name->FldCaption(), $this->unit_name->ReqErrMsg));
		}
		if (!$this->keterangan->FldIsDetailKey && !is_null($this->keterangan->FormValue) && $this->keterangan->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->keterangan->FldCaption(), $this->keterangan->ReqErrMsg));
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $Language, $Security;
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// merk_id
		$this->merk_id->SetDbValueDef($rsnew, $this->merk_id->CurrentValue, 0, FALSE);

		// device_id
		$this->device_id->SetDbValueDef($rsnew, $this->device_id->CurrentValue, 0, FALSE);

		// unit_name
		$this->unit_name->SetDbValueDef($rsnew, $this->unit_name->CurrentValue, "", FALSE);

		// keterangan
		$this->keterangan->SetDbValueDef($rsnew, $this->keterangan->CurrentValue, "", FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {

				// Get insert id if necessary
				$this->assembly_id->setDbValue($conn->Insert_ID());
				$rsnew['assembly_id'] = $this->assembly_id->DbValue;
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
			$this->WriteAuditTrailOnAdd($rsnew);
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("tb_assemblylist.php"), "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_merk_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `merk_id` AS `LinkFld`, `merk_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_merk`";
			$sWhereWrk = "{filter}";
			$this->merk_id->LookupFilters = array("dx1" => '`merk_nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`merk_id` = {filter_value}', "t0" => "2", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->merk_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_device_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `device_id` AS `LinkFld`, `device_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_device`";
			$sWhereWrk = "{filter}";
			$this->device_id->LookupFilters = array("dx1" => '`device_name`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`device_id` = {filter_value}', "t0" => "2", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->device_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_merk_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `merk_id`, `merk_nama` AS `DispFld` FROM `tb_merk`";
			$sWhereWrk = "`merk_nama` LIKE '{query_value}%'";
			$this->merk_id->LookupFilters = array("dx1" => '`merk_nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->merk_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_device_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `device_id`, `device_name` AS `DispFld` FROM `tb_device`";
			$sWhereWrk = "`device_name` LIKE '{query_value}%'";
			$this->device_id->LookupFilters = array("dx1" => '`device_name`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->device_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		}
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tb_assembly';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		global $Language;
		if (!$this->AuditTrailOnAdd) return;
		$table = 'tb_assembly';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['assembly_id'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
		$usr = CurrentUserName();
		foreach (array_keys($rs) as $fldname) {
			if ($this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldHtmlTag == "PASSWORD") {
					$newvalue = $Language->Phrase("PasswordMask"); // Password Field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$newvalue = $rs[$fldname];
					else
						$newvalue = "[MEMO]"; // Memo Field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$newvalue = "[XML]"; // XML Field
				} else {
					$newvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $usr, "A", $table, $fldname, $key, "", $newvalue);
			}
		}
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($tb_assembly_add)) $tb_assembly_add = new ctb_assembly_add();

// Page init
$tb_assembly_add->Page_Init();

// Page main
$tb_assembly_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tb_assembly_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = ftb_assemblyadd = new ew_Form("ftb_assemblyadd", "add");

// Validate form
ftb_assemblyadd.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_merk_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $tb_assembly->merk_id->FldCaption(), $tb_assembly->merk_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_device_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $tb_assembly->device_id->FldCaption(), $tb_assembly->device_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_unit_name");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $tb_assembly->unit_name->FldCaption(), $tb_assembly->unit_name->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_keterangan");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $tb_assembly->keterangan->FldCaption(), $tb_assembly->keterangan->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
ftb_assemblyadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftb_assemblyadd.ValidateRequired = true;
<?php } else { ?>
ftb_assemblyadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftb_assemblyadd.Lists["x_merk_id"] = {"LinkField":"x_merk_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_merk_nama","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"tb_merk"};
ftb_assemblyadd.Lists["x_device_id"] = {"LinkField":"x_device_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_device_name","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"tb_device"};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$tb_assembly_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $tb_assembly_add->ShowPageHeader(); ?>
<?php
$tb_assembly_add->ShowMessage();
?>
<form name="ftb_assemblyadd" id="ftb_assemblyadd" class="<?php echo $tb_assembly_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($tb_assembly_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $tb_assembly_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="tb_assembly">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($tb_assembly_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($tb_assembly->merk_id->Visible) { // merk_id ?>
	<div id="r_merk_id" class="form-group">
		<label id="elh_tb_assembly_merk_id" class="col-sm-2 control-label ewLabel"><?php echo $tb_assembly->merk_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $tb_assembly->merk_id->CellAttributes() ?>>
<span id="el_tb_assembly_merk_id">
<?php
$wrkonchange = trim(" " . @$tb_assembly->merk_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$tb_assembly->merk_id->EditAttrs["onchange"] = "";
?>
<span id="as_x_merk_id" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_merk_id" id="sv_x_merk_id" value="<?php echo $tb_assembly->merk_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($tb_assembly->merk_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($tb_assembly->merk_id->getPlaceHolder()) ?>"<?php echo $tb_assembly->merk_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="tb_assembly" data-field="x_merk_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $tb_assembly->merk_id->DisplayValueSeparatorAttribute() ?>" name="x_merk_id" id="x_merk_id" value="<?php echo ew_HtmlEncode($tb_assembly->merk_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_merk_id" id="q_x_merk_id" value="<?php echo $tb_assembly->merk_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ftb_assemblyadd.CreateAutoSuggest({"id":"x_merk_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($tb_assembly->merk_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_merk_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x_merk_id" id="s_x_merk_id" value="<?php echo $tb_assembly->merk_id->LookupFilterQuery(false) ?>">
</span>
<?php echo $tb_assembly->merk_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($tb_assembly->device_id->Visible) { // device_id ?>
	<div id="r_device_id" class="form-group">
		<label id="elh_tb_assembly_device_id" class="col-sm-2 control-label ewLabel"><?php echo $tb_assembly->device_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $tb_assembly->device_id->CellAttributes() ?>>
<span id="el_tb_assembly_device_id">
<?php
$wrkonchange = trim(" " . @$tb_assembly->device_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$tb_assembly->device_id->EditAttrs["onchange"] = "";
?>
<span id="as_x_device_id" style="white-space: nowrap; z-index: 8970">
	<input type="text" name="sv_x_device_id" id="sv_x_device_id" value="<?php echo $tb_assembly->device_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($tb_assembly->device_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($tb_assembly->device_id->getPlaceHolder()) ?>"<?php echo $tb_assembly->device_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="tb_assembly" data-field="x_device_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $tb_assembly->device_id->DisplayValueSeparatorAttribute() ?>" name="x_device_id" id="x_device_id" value="<?php echo ew_HtmlEncode($tb_assembly->device_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_device_id" id="q_x_device_id" value="<?php echo $tb_assembly->device_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ftb_assemblyadd.CreateAutoSuggest({"id":"x_device_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($tb_assembly->device_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_device_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x_device_id" id="s_x_device_id" value="<?php echo $tb_assembly->device_id->LookupFilterQuery(false) ?>">
</span>
<?php echo $tb_assembly->device_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($tb_assembly->unit_name->Visible) { // unit_name ?>
	<div id="r_unit_name" class="form-group">
		<label id="elh_tb_assembly_unit_name" for="x_unit_name" class="col-sm-2 control-label ewLabel"><?php echo $tb_assembly->unit_name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $tb_assembly->unit_name->CellAttributes() ?>>
<span id="el_tb_assembly_unit_name">
<textarea data-table="tb_assembly" data-field="x_unit_name" name="x_unit_name" id="x_unit_name" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($tb_assembly->unit_name->getPlaceHolder()) ?>"<?php echo $tb_assembly->unit_name->EditAttributes() ?>><?php echo $tb_assembly->unit_name->EditValue ?></textarea>
</span>
<?php echo $tb_assembly->unit_name->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($tb_assembly->keterangan->Visible) { // keterangan ?>
	<div id="r_keterangan" class="form-group">
		<label id="elh_tb_assembly_keterangan" for="x_keterangan" class="col-sm-2 control-label ewLabel"><?php echo $tb_assembly->keterangan->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $tb_assembly->keterangan->CellAttributes() ?>>
<span id="el_tb_assembly_keterangan">
<textarea data-table="tb_assembly" data-field="x_keterangan" name="x_keterangan" id="x_keterangan" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($tb_assembly->keterangan->getPlaceHolder()) ?>"<?php echo $tb_assembly->keterangan->EditAttributes() ?>><?php echo $tb_assembly->keterangan->EditValue ?></textarea>
</span>
<?php echo $tb_assembly->keterangan->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<?php if (!$tb_assembly_add->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $tb_assembly_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
ftb_assemblyadd.Init();
</script>
<?php
$tb_assembly_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tb_assembly_add->Page_Terminate();
?>
