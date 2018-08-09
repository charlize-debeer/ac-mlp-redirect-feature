<?php

namespace AC_Plugin_Boilerplate\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class Install_Command extends Command {

	/**
	 * Configure the command.
	 */
	protected function configure() {
		$this->setName( 'plugin:create' );
		$this->setDescription( 'Create a plugin!' );
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return int|null|void
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$helper = $this->getHelper( 'question' );

		$plugin_name_question = new Question( 'Plugin name: ' );
		$plugin_name = $helper->ask( $input, $output, $plugin_name_question );

		if ( empty( $plugin_name ) ) {
			$output->writeln( '<error>Please provide a plugin name!</error>' );
			return;
		}

		$plugin_slug = strtolower( preg_replace( '/\s/', '-', $plugin_name ) );
		$plugin_slug = preg_replace( '/[^a-zA-Z-]/', '', $plugin_slug );
		$plugin_slug_question = new Question( 'Plugin slug (' . $plugin_slug . '): ', $plugin_slug );
		$plugin_slug = $helper->ask( $input, $output, $plugin_slug_question );

		$plugin_namespace = ucwords( strtolower( $plugin_name ) );
		$plugin_namespace = preg_replace( '/\s/', '_', $plugin_namespace );
		$plugin_namespace = preg_replace( '/[^a-zA-Z_]/', '', $plugin_namespace );
		$plugin_namespace_question = new Question( 'Plugin namespace (' . $plugin_namespace . '): ', $plugin_namespace );
		$plugin_namespace = $helper->ask( $input, $output, $plugin_namespace_question );

		$plugin_desc_question = new Question( 'Plugin description: ' );
		$plugin_desc = $helper->ask( $input, $output, $plugin_desc_question );

		$js_question = new ConfirmationQuestion( 'Do you want to add JS with your plugin? Y/n ' );
		$js = $helper->ask( $input, $output, $js_question );

		$css_question = new ConfirmationQuestion( 'Do you want to add CSS with your plugin? Y/n ' );
		$css = $helper->ask( $input, $output, $css_question );

		$confirm = new ConfirmationQuestion( sprintf( "
==========================
Ok, this is what I've got:
==========================

Plugin name: '%s'
slug: '%s'
namespace: '%s'
description: '%s'

Use JS: %s,
Use CSS: %s

===============
Good to go? Y/n
===============
",
			$plugin_name,
			$plugin_slug,
			$plugin_namespace,
			$plugin_desc,
			$js ? 'Yes' : 'No',
			$css ? 'Yes' : 'No'
		) );

		if ( ! $helper->ask( $input, $output, $confirm ) ) {
			$output->writeln( '<info>Ok, sorry I asked :P</info>' );
			return;
		}

		if ( file_exists( dirname( dirname( __DIR__ ) ) . '/includes/class-plugin.php' ) ) {
			$output->writeln( '<error>Plugin file exists, exiting</error>' );
			return;
		}

		$created = $this->create_plugin_files([
			'plugin_name' => $plugin_name,
			'plugin_slug' => $plugin_slug,
			'plugin_namespace' => $plugin_namespace,
			'plugin_desc' => $plugin_desc,
			'scripts' => $js,
			'styles' => $css,
		]);

		$this->rename_plugin_dir( $plugin_slug );

		if ( true === $created ) {
			$output->writeln( '<info>Plugin created :)</info>' );
		} else {
			$output->writeln( '<error>NOOOO! Something borked :(</error>' );
		}
	}

	/**
	 * @param array $args
	 *
	 * @return bool
	 */
	protected function create_plugin_files( array $args = [] ) : bool {
		if ( empty( $args ) ) {
			return false;
		}

		$result = $this->create_file( $args, 'class-plugin.php.tmp', 'includes/class-plugin.php' );
		if ( ! $result ) {
			return false;
		}

		$result = $this->create_file( $args, 'ac-plugin-boilerplate-v2.php.tmp', $args['plugin_slug'] . '.php' );
		if ( ! $result ) {
			return false;
		}

		$result = $this->create_file( $args, 'bootstrap.php.tmp', 'tests/bootstrap.php' );
		if ( ! $result ) {
			return false;
		}

		$result = $this->create_file( $args, 'composer.json.tmp', 'composer.json' );
		if ( ! $result ) {
			return false;
		}

		$result = $this->create_file( $args, 'test-plugin.php.tmp', 'tests/test-plugin.php' );
		if ( ! $result ) {
			return false;
		}

		if ( true === $args['scripts'] || true === $args['styles'] ) {
			mkdir( dirname( dirname( __DIR__ ) ) . '/assets' );
		}

		if ( true === $args['scripts'] ) {
			mkdir( dirname( dirname( __DIR__ ) ) . '/assets/javascript' );
			touch( dirname( dirname( __DIR__ ) ) . '/assets/javascript/' . $args['plugin_slug'] . '.js' );
		}

		if ( true === $args['styles'] ) {
			mkdir( dirname( dirname( __DIR__ ) ) . '/assets/css' );
			touch( dirname( dirname( __DIR__ ) ) . '/assets/css/' . $args['plugin_slug'] . '.css' );
		}

		return true;
	}

	/**
	 * @param array $args
	 * @param string $template_path
	 * @param string $file_path
	 *
	 * @return bool
	 */
	protected function create_file( array $args = [], $template_path, $file_path ) : bool {
		$m = new \Mustache_Engine([
			'escape' => function ( $val ) {
				return $val;
			},
		] );
		$template_file = file_get_contents( dirname( __DIR__ ) . '/templates/' . $template_path );
		$result = $m->render( $template_file, $args );

		return (bool) file_put_contents( dirname( dirname( __DIR__ ) ) . '/' . $file_path, $result );
	}

	/**
	 * @param string $plugin_slug
	 *
	 * @return bool
	 */
	protected function rename_plugin_dir( $plugin_slug ) : bool {
		// Rename this directory to the plugin slug.
		$plugin_dir = dirname( dirname( __DIR__ ) );

		return rename( $plugin_dir, dirname( dirname( dirname( __DIR__ ) ) ) . '/' . $plugin_slug );
	}

}
