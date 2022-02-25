<?php

namespace DgoraWcas\Integrations\Themes\Xstore;

use DgoraWcas\Abstracts\ThemeIntegration;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Xstore extends ThemeIntegration {

	public function __construct() {
		$this->themeSlug = 'xstore';
		$this->themeName = 'XStore';

		parent::__construct();
	}
}
