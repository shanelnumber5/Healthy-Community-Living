<?php
/**
 * Helper class for font settings.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sinatra_Fonts' ) ) :

	/**
	 * Sinatra helper class to handle fonts.
	 *
	 * @since 1.0.0
	 */
	class Sinatra_Fonts {

		/**
		 * System Fonts
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $system_fonts = array();

		/**
		 * Google Fonts
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $google_fonts = array();

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
		}

		/**
		 * Get System Fonts.
		 *
		 * @since 1.0.0
		 *
		 * @return Array All the system fonts in Sinatra
		 */
		public function get_system_fonts() {

			if ( empty( $this->system_fonts ) ) {
				$this->system_fonts = array(
					'Helvetica' => array(
						'fallback' => 'Verdana, Arial, sans-serif',
						'variants' => array(
							'300',
							'400',
							'700',
						),
					),
					'Verdana'   => array(
						'fallback' => 'Helvetica, Arial, sans-serif',
						'variants' => array(
							'300',
							'400',
							'700',
						),
					),
					'Arial'     => array(
						'fallback' => 'Helvetica, Verdana, sans-serif',
						'variants' => array(
							'300',
							'400',
							'700',
						),
					),
					'Times'     => array(
						'fallback' => 'Georgia, serif',
						'variants' => array(
							'300',
							'400',
							'700',
						),
					),
					'Georgia'   => array(
						'fallback' => 'Times, serif',
						'variants' => array(
							'300',
							'400',
							'700',
						),
					),
					'Courier'   => array(
						'fallback' => 'monospace',
						'variants' => array(
							'300',
							'400',
							'700',
						),
					),
				);
			}

			return apply_filters( 'sinatra_system_fonts', $this->system_fonts );
		}

		/**
		 * Return an array of standard websafe fonts.
		 *
		 * @return array    Standard websafe fonts.
		 */
		public function get_standard_fonts() {

			$standard_fonts = array(
				'Serif'      => array(
					'fallback' => 'Georgia, Times, "Times New Roman", serif',
					'variants' => array(
						'300',
						'400',
						'700',
					),
				),
				'Sans Serif' => array(
					'fallback' => 'Helvetica, Arial, sans-serif',
					'variants' => array(
						'300',
						'400',
						'700',
					),
				),
				'Monospace'  => array(
					'fallback' => 'Monaco, "Lucida Sans Typewriter", "Lucida Typewriter", "Courier New", Courier, monospace',
					'variants' => array(
						'300',
						'400',
						'700',
					),
				),
			);

			return apply_filters( 'sinatra_standard_fonts', $standard_fonts );
		}

		/**
		 * Default system font.
		 *
		 * @since  1.0.0
		 *
		 * @return string Default system font.
		 */
		public function get_default_system_font() {

			$font = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;';

			return apply_filters( 'sinatra_default_system_font', $font );
		}

		/**
		 * Google Fonts.
		 * Array is generated from the google-fonts.json file.
		 *
		 * @since  1.0.0
		 *
		 * @return Array Array of Google Fonts.
		 */
		public function get_google_fonts() {

			if ( empty( $this->google_fonts ) ) {

				$google_fonts_file = apply_filters( 'sinatra_google_fonts_json_file', SINATRA_THEME_PATH . '/assets/fonts/google-fonts.json' );

				if ( ! file_exists( $google_fonts_file ) ) {
					return array();
				}

				global $wp_filesystem;
				if ( empty( $wp_filesystem ) ) {
					require_once ABSPATH . '/wp-admin/includes/file.php'; // phpcs:ignore
					WP_Filesystem();
				}

				$file_contants     = $wp_filesystem->get_contents( $google_fonts_file );
				$google_fonts_json = json_decode( $file_contants, 1 );

				foreach ( $google_fonts_json as $key => $font ) {

					$name = key( $font );

					foreach ( $font[ $name ] as $font_key => $single_font ) {

						if ( 'variants' === $font_key ) {

							foreach ( $single_font as $variant_key => $variant ) {

								if ( 'regular' === $variant ) {
									$font[ $name ][ $font_key ][ $variant_key ] = '400';
								}

								if ( 'italic' === $variant ) {
									$font[ $name ][ $font_key ][ $variant_key ] = '400italic';
								}

								if ( strpos( $font[ $name ][ $font_key ][ $variant_key ], 'italic' ) ) {
									unset( $font[ $name ][ $font_key ][ $variant_key ] );
								}
							}
						}

						$this->google_fonts[ $name ] = $font[ $name ];
					}
				}
			}

			return apply_filters( 'sinatra_google_fonts', $this->google_fonts );
		}

		/**
		 * Google Font subsets.
		 *
		 * @since  1.0.0
		 *
		 * @return Array Array of Google Fonts.
		 */
		public function get_google_font_subsets() {

			$subsets = array(
				'arabic'              => esc_html__( 'Arabic', 'sinatra' ),
				'bengali'             => esc_html__( 'Bengali', 'sinatra' ),
				'chinese-hongkong'    => esc_html__( 'Chinese (Hong Kong)', 'sinatra' ),
				'chinese-simplified'  => esc_html__( 'Chinese (Simplified)', 'sinatra' ),
				'chinese-traditional' => esc_html__( 'Chinese (Traditional)', 'sinatra' ),
				'cyrillic'            => esc_html__( 'Cyrillic', 'sinatra' ),
				'cyrillic-ext'        => esc_html__( 'Cyrillic Extended', 'sinatra' ),
				'devanagari'          => esc_html__( 'Devanagari', 'sinatra' ),
				'greek'               => esc_html__( 'Greek', 'sinatra' ),
				'greek-ext'           => esc_html__( 'Greek Extended', 'sinatra' ),
				'gujarati'            => esc_html__( 'Gujarati', 'sinatra' ),
				'gurmukhi'            => esc_html__( 'Gurmukhi', 'sinatra' ),
				'hebrew'              => esc_html__( 'Hebrew', 'sinatra' ),
				'japanese'            => esc_html__( 'Japanese', 'sinatra' ),
				'kannada'             => esc_html__( 'Kannada', 'sinatra' ),
				'khmer'               => esc_html__( 'Khmer', 'sinatra' ),
				'korean'              => esc_html__( 'Korean', 'sinatra' ),
				'latin'               => esc_html__( 'Latin', 'sinatra' ),
				'latin-ext'           => esc_html__( 'Latin Extended', 'sinatra' ),
				'malayalam'           => esc_html__( 'Malayalam', 'sinatra' ),
				'myanmar'             => esc_html__( 'Myanmar', 'sinatra' ),
				'oriya'               => esc_html__( 'Oriya', 'sinatra' ),
				'sinhala'             => esc_html__( 'Sinhala', 'sinatra' ),
				'tamil'               => esc_html__( 'Tamil', 'sinatra' ),
				'telugu'              => esc_html__( 'Telugu', 'sinatra' ),
				'thai'                => esc_html__( 'Thai', 'sinatra' ),
				'vietnamese'          => esc_html__( 'Vietnamese', 'sinatra' ),
			);

			return apply_filters( 'sinatra_google_font_subsets', $subsets );
		}

		/**
		 * Return an array of backup fonts based on the font-category.
		 *
		 * @return array
		 */
		public function get_backup_fonts() {

			$backup_fonts = array(
				'sans-serif'  => 'Helvetica, Arial, sans-serif',
				'serif'       => 'Georgia, serif',
				'display'     => '"Comic Sans MS", cursive, sans-serif',
				'handwriting' => '"Comic Sans MS", cursive, sans-serif',
				'monospace'   => '"Lucida Console", Monaco, monospace',
			);

			return apply_filters( 'sinatra_backup_fonts', $backup_fonts );
		}

		/**
		 * Enqueue Google fonts.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_google_fonts() {

			$fonts = get_transient( 'sinatra_google_fonts_enqueue' );

			if ( false === $fonts || empty( $fonts ) ) {
				return;
			}

			$url     = '//fonts.googleapis.com/css';
			$family  = array();
			$subsets = array();

			foreach ( $fonts as $font_family => $font ) {

				if ( ! empty( $font['weight'] ) ) {
					$family[] = $font_family . ':' . implode( ',', $font['weight'] );
				} else {
					$family[] = $font_family;
				}

				$subsets = array_unique( array_merge( $subsets, $font['subsets'] ) );
			}

			$family  = implode( '|', $family );
			$subsets = implode( ',', $subsets );

			$url = add_query_arg(
				array(
					'family'  => $family,
					'display' => 'swap',
					'subsets' => $subsets,
				),
				$url
			);

			// Enqueue.
			wp_enqueue_style(
				'sinatra-google-fonts',
				$url,
				false,
				SINATRA_THEME_VERSION,
				false
			);
		}

		/**
		 * Check if font familu is a Google font.
		 *
		 * @since  1.0.0
		 * @param  string $font_family Font Family.
		 * @return boolean
		 */
		public function is_google_font( $font_family ) {

			$google_fonts = $this->get_google_fonts();

			return isset( $google_fonts[ $font_family ] );
		}

		/**
		 * Store list of Google fonts to enqueue.
		 *
		 * @since  1.0.0
		 * @param  string $family Font Family.
		 * @param  array  $args Array of font details.
		 * @return void
		 */
		public function enqueue_google_font( $family, $args = array() ) {

			$fonts = get_transient( 'sinatra_google_fonts_enqueue' );

			$fonts = $fonts ? $fonts : array();

			// Default args.
			$args = wp_parse_args(
				$args,
				array(
					'weight'  => array( '400' ),
					'style'   => array( 'normal' ),
					'subsets' => array( 'latin' ),
				)
			);

			// Convert all args to arrays.
			foreach ( $args as $key => $value ) {
				if ( ! is_array( $args[ $key ] ) ) {
					$args[ $key ] = array( $value );
				}
			}

			if ( in_array( 'italic', $args['style'], true ) ) {
				foreach ( $args['weight'] as $weight ) {
					$args['weight'][] = $weight . 'i';
				}
			}

			// Remove uneccesary info.
			unset( $args['style'] );

			// Sanitize key.
			$family = str_replace( ' ', '+', $family );

			// Check if we previously enqueued this font.
			if ( ! isset( $fonts[ $family ] ) ) {
				$fonts[ $family ] = $args;
			} else {
				foreach ( $args as $key => $value ) {
					$fonts[ $family ][ $key ] = array_unique(
						array_merge(
							$fonts[ $family ][ $key ],
							$value
						)
					);
				}
			}

			set_transient( 'sinatra_google_fonts_enqueue', $fonts );
		}

		/**
		 * Get All Fonts.
		 *
		 * @since 1.0.0
		 *
		 * @return Array All the system fonts in Sinatra
		 */
		public function get_fonts() {

			$fonts = array();

			$fonts['standard_fonts'] = array(
				'name'  => esc_html__( 'Standard', 'sinatra' ),
				'fonts' => self::get_standard_fonts(),
			);

			$fonts['system_fonts'] = array(
				'name'  => esc_html__( 'System Fonts', 'sinatra' ),
				'fonts' => self::get_system_fonts(),
			);

			$fonts['google_fonts'] = array(
				'name'  => esc_html__( 'Google Fonts', 'sinatra' ),
				'fonts' => self::get_google_fonts(),
			);

			return apply_filters( 'sinatra_get_fonts', $fonts );
		}

		/**
		 * Get complete font family stack.
		 *
		 * @since 1.0.0
		 *
		 * @param  string $font Font family.
		 * @return string Font family including backup families.
		 */
		public function get_font_family( $font ) {

			if ( 'default' === $font ) {
				$font = $this->get_default_system_font();
			} else {

				$fonts  = $this->get_fonts();
				$backup = '';

				if ( isset( $fonts['system_fonts']['fonts'][ $font ] ) ) {
					$backup = $fonts['system_fonts']['fonts'][ $font ]['fallback'];
				} elseif ( isset( $fonts['google_fonts']['fonts'][ $font ] ) ) {
					$backups  = $this->get_backup_fonts();
					$category = $fonts['google_fonts']['fonts'][ $font ]['category'];
					$backup   = isset( $backups[ $category ] ) ? $backups[ $category ] : '';
				} elseif ( isset( $fonts['standard_fonts']['fonts'][ $font ] ) ) {
					$backup = $fonts['standard_fonts']['fonts'][ $font ]['fallback'];
					$font   = '';
				}

				if ( false !== strpos( $font, ' ' ) ) {
					$font = '"' . $font . '"';
				}

				$font = $font . ', ' . $backup;
				$font = trim( $font, ', ' );
			}

			return apply_filters( 'sinatra_font_family', $font );
		}
	}

endif;
