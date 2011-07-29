<?

	class ImageDimensions_Settings extends Backend_Controller {
		public $implement = 'Db_FormBehavior';
		
		public $form_edit_title = 'Image Dimensions Settings';
		public $form_model_class = 'ImageDimensions_Configuration';
		public $form_redirect = null;

		protected $required_permissions = array('imagedimensions:manage_settings');

		public function __construct() {
			parent::__construct();
			$this->app_tab = 'imagedimensions';
			$this->app_page = 'settings';
			$this->app_module_name = 'Image Dimensions';
		}

		public function index() {
			try {
				$this->app_page_title = 'Settings';
				
				$config = new ImageDimensions_Configuration();
				$this->viewData['form_model'] = $config->load();
			}
			catch(exception $ex) {
				$this->_controller->handlePageError($ex);
			}
		}
		
		protected function index_onSave() {
			try {
				$config = new ImageDimensions_Configuration();
				$config = $config->load();
			
				$config->save(post($this->form_model_class, array()), $this->formGetEditSessionKey());
			
				echo Backend_Html::flash_message('Configuration have been successfully saved.');
			}
			catch(Exception $ex) {
				Phpr::$response->ajaxReportException($ex, true, true);
			}
		}
	}