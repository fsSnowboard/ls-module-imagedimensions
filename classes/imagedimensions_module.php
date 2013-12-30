<?

	define('PATH_MOD_IMAGEDIMENSIONS', PATH_APP . '/modules/imagedimensions');
	
	class ImageDimensions_Module extends Core_ModuleBase {
		const PATH = PATH_MOD_IMAGEDIMENSIONS;
		
		protected function get_info() {
			return new Core_ModuleInfo(
				"Image Dimensions",
				"Provides automatic resizing of products images after upload based upon configured dimensions.",
				"Limewheel Creative Inc."
			);
		}
		
		public function subscribe_events() {
			Backend::$events->addEvent('core:onAfterFormRecordUpdate', $this, 'after_form_record_modified');
			Backend::$events->addEvent('core:onAfterFormRecordCreate', $this, 'after_form_record_modified');
		}
		
		public function build_ui_permissions($host) {
			$host->add_field($this, 'manage_settings', 'Manage settings', 'left')->renderAs(frm_checkbox)->comment('View and manage the settings.', 'above');
		}
		
		public function list_tabs($tab_collection) {
			$user = Phpr::$security->getUser();
			
			$tabs = array(
				'settings' => array('settings', 'Settings', 'settings')
			);

			$first_tab = null;
			
			foreach($tabs as $tab_id => $tab_info) {
				if(($tabs[$tab_id][3] = $user->get_permission('imagedimensions', $tab_info[2])) && !$first_tab)
					$first_tab = $tab_info[0];
			}

			if($first_tab) {
				$tab = $tab_collection->tab('imagedimensions', 'Image Dimensions', $first_tab, 30);
				
				foreach($tabs as $tab_id => $tab_info) {
					if($tab_info[3])
						$tab->addSecondLevel($tab_id, $tab_info[1], $tab_info[0]);
				}
			}
		}

		public function after_form_record_modified($controller, $object) {
			if(get_class($object) !== 'Shop_Product')
				return;
		
			$files = $object->list_related_records_deferred('images', $controller->formGetEditSessionKey());
			$config = ImageDimensions_Configuration::create();

			if($config->max_width === 'auto' && $config->max_height === 'auto')
				return; // no changes, no need to process the image

			foreach($files as $file) {
				if(!$file->is_image())
					continue;
			
				$source_path = PATH_APP . $file->getPath();

				//Lets get image dimensions
				$imagedim = getimagesize($source_path);

				//skip resizing if image is smaller than config setting
				if($imagedim[0] <= $config->max_width || $imagedim[1] <= $config->max_height)
					continue;

				$destination_path = PATH_APP . $file->getThumbnailPath($config->max_width, $config->max_height);

				copy($destination_path, $source_path);
			} 
		}	

		/**
		 * Awaiting deprecation
		 */
		
		protected function createModuleInfo() {
			return $this->get_info();
		}
		
		public function subscribeEvents() {
			return $this->subscribe_events();
		}
		
		public function buildPermissionsUi($host) {
			return $this->build_ui_permissions($host);
		}
		
		public function listTabs($tab_collection) {
			return $this->list_tabs($tab_collection);
		}
	}