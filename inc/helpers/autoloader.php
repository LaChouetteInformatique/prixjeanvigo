<?php
/**
 * Autoloader file for theme.
 *
 * @package DB_Plugin
 */

namespace DB_PLUGIN\Inc\Helpers;

// Include every php file present in helpers directory
foreach (glob(DB_PLUGIN_DIR_PATH . "/inc/helpers/*.php") as $filename) {
	include_once $filename;
}

/**
 * Auto loader function.
 *
 * @param string $resource Source namespace.
 *
 * @return void
 */
function autoloader($resource = '')
{

	$resource_path = false;
	$namespace_root = 'DB_PLUGIN\\';
	$resource = trim($resource, '\\');


	if (empty($resource) || strpos($resource, '\\') === false || strpos($resource, $namespace_root) !== 0) {
		// Not our namespace, bail out.
		return;
	}

	// Remove our root namespace.
	$resource = str_replace($namespace_root, '', $resource);

	$path = explode(
		'\\',
		str_replace('_', '-', strtolower($resource))
	);

	/**
	 * Time to determine which type of resource path it is,
	 * so that we can deduce the correct file path for it.
	 */
	if (empty($path[0]) || empty($path[1])) {
		return;
	}

	$directory = '';
	$file_name = '';



	if ('inc' === $path[0]) {

		switch ($path[1]) {
			
			case 'traits':
				$directory = 'traits';
				// $file_name = sprintf('trait-%s', trim(strtolower($path[2])));
				$file_name = trim(strtolower($path[2]));
				break;

			case 'widgets':
			case 'blocks': // phpcs:ignore PSR2.ControlStructures.SwitchDeclaration.TerminatingComment

				if (!empty($path[2])) {
					$directory = trim(strtolower($path[1]));
					$file_name = trim(strtolower($path[2]));
					// echo '<pre>';
					// print_r($directory);
					// echo '</pre>';
					// echo '<pre>';
					// print_r($resource);
					// echo '</pre>';
					// echo '<pre>';
					// print_r($file_name);
					// echo '</pre>';
					// wp_die();
					break;
				}
			default:
				$directory = 'classes';
				$file_name = trim(strtolower($path[1]));
				break;
		}

		$resource_path = sprintf('%s/inc/%s/%s.php', untrailingslashit(DB_PLUGIN_DIR_PATH), $directory, $file_name);
		// $resource_path = sprintf('%s/%s/%s.php', untrailingslashit(DB_PLUGIN_DIR_PATH), $directory, $file_name);

	}

	/**
	 * If $is_valid_file has 0 means valid path or 2 means the file path contains a Windows drive path.
	 */
	$is_valid_file = validate_file($resource_path);

	if (!empty($resource_path) && file_exists($resource_path) && (0 === $is_valid_file || 2 === $is_valid_file)) {
		// We already making sure that file is exists and valid.
		require_once($resource_path); // phpcs:ignore
	}

}

spl_autoload_register('\DB_PLUGIN\Inc\Helpers\autoloader');