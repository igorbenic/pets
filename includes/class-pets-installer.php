<?php

/**
 * Pets Installer.
 */

namespace Pets;

if( ! defined( 'ABSPATH') ) {
	return;
}

/**
 * Class to perform creating database and other stuff
 */
class Pets_Installer {

	/**
	 * Array of versions and function names.
	 *
	 * @var array
	 */
	public $updates = array(
		'0.3.0' => 'pets_upgrade_030',
		'0.5.0' => 'pets_upgrade_050',
		'1.3.0' => 'pets_upgrade_130',
	);

	/**
	 * Start the installation
	 *
	 * @return void
	 */
	public function install() {

		if ( ! defined( 'PETS_INSTALLING' ) ) {
			define( 'PETS_INSTALLING', true );
		}

		$this->create_db();

	}

	/**
	 * Start the installation
	 *
	 * @param string $from_version Version from which we update (not the newest).
	 * @return void
	 */
	public function update( $from_version ) {

		if ( ! defined( 'PETS_UPDATING' ) ) {
			define( 'PETS_UPDATING', true );
		}

		foreach ( $this->updates as $version => $update_function ) {
			if ( version_compare( $from_version, $version, '<' ) ) {
				$update_function();
			}
		}

		update_option( 'pets_version', PETS_VERSION );

	}

	/**
	 * Create the Database
	 * @return void
	 */
	public function create_db() {
		\Pets\DB\Fields::install();
		\Pets\DB\Fields_Sections::install();
	}
}
