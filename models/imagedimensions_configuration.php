<?

	class ImageDimensions_Configuration extends Core_Configuration_Model {
		public $record_code = 'imagedimensions_configuration';
		
		public static function create() {
			$config = new self();
			
			return $config->load();
		}
		
		protected function build_form() {
			$this->add_field('max_width', 'Maximum Width', 'full', db_varchar)->tab('Image Dimensions')->comment('Enter an integer to specify as the maximum width (eg. 500) or "auto" to leave the width the same size.');
			$this->add_field('max_height', 'Maximum Height', 'full', db_varchar)->tab('Image Dimensions')->comment('Enter an integer to specify as the maximum height (eg. 500) or "auto" to leave the height the same size.');
		}
		
		protected function init_config_data() {
			$this->max_width = 'auto';
			$this->max_height = 'auto';
		}
	}