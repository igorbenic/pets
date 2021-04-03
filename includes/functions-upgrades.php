<?php

/**
 * Globally available upgrade functions.
 *
 * @package Pets
 */

/**
 * Upgrade the Pets plugin to 0.3.0
 * In this upgrade we need to install the Fields Sections.
 */
function pets_upgrade_030() {
	\Pets\DB\Fields_Sections::install();
	\Pets\DB\Fields::install();
}

/**
 * Upgrading the field table.
 */
function pets_upgrade_050() {
	\Pets\DB\Fields::install();
}

/**
 * Upgrading the field table.
 */
function pets_upgrade_130() {
	\Pets\DB\Fields::install();
}
