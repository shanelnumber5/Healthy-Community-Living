<?php

/**
 * Useful functions
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      1.2.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/includes
 * @author     Frédéric GILLES
 */

/**
 * Useful functions class
 *
 */

if ( !class_exists('FG_Drupal_to_WordPress_Tools', false) ) {
	class FG_Drupal_to_WordPress_Tools {
		/**
		 * Convert string to latin
		 */
		public static function convert_to_latin($string) {
			if ( function_exists('transliterator_transliterate') ) {
				$string = transliterator_transliterate('Any-Latin; Latin-ASCII', $string);
				
			} else {
				$string = self::greek_to_latin($string); // For Greek characters
				$string = self::cyrillic_to_latin($string); // For Cyrillic characters
				$string = self::arabic_to_latin($string); // For Arabic characters
				$string = self::bengali_to_latin($string); // For Bengali characters
				$string = self::devanagari_to_latin($string); // For Devanagari characters
				$string = remove_accents($string); // For accented characters
			}
			return $string;
		}
		
		/**
		 * Convert Greek characters to latin
		 * 
		 * @param string $string String
		 * @return string String with Greek characters converted to latin
		 */
		private static function greek_to_latin($string) {
			static $from = array('Α','Ά','Β','Γ','Δ','Ε','Έ','Ζ','Η','Θ','Ι','Κ','Λ','Μ','Ν','Ξ','Ο','Π','Ρ','Σ','Τ','Υ','Φ','Χ','Ψ','Ω','α','ά','β','γ','δ','ε','έ','ζ','η','ή','θ','ι','ί','ϊ','κ','λ','μ','ν','ξ','ο','ό','π','ρ','ς','σ','τ','υ','ύ','φ','χ','ψ','ω','ώ','ϑ','ϒ','ϖ');
			static $to = array('A','A','V','G','D','E','E','Z','I','TH','I','K','L','M','N','X','O','P','R','S','T','Y','F','CH','PS','O','a','a','v','g','d','e','e','z','i','i','th','i','i','i','k','l','m','n','x','o','o','p','r','s','s','t','y','y','f','ch','ps','o','o','th','y','p');
			return str_replace($from, $to, $string);
		}

		/**
		 * Convert Cyrillic (Russian) characters to latin
		 * 
		 * @param string $string String
		 * @return string String with Cyrillic characters converted to latin
		 */
		private static function cyrillic_to_latin($string) {
			static $from = array('ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я', 'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я');
			static $to = array('zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x', 'q', 'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', 'Q');
			return str_replace($from, $to, $string);
		}

		/**
		 * Convert Arabic characters to latin
		 * 
		 * @param string $string String
		 * @return string String with Arabic characters converted to latin
		 */
		private static function arabic_to_latin($string) {
			static $from = array('أ', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي');
			static $to = array('a', 'b', 't', 'th', 'g', 'h', 'kh', 'd', 'th', 'r', 'z', 's', 'sh', 's', 'd', 't', 'th', 'aa', 'gh', 'f', 'k', 'k', 'l', 'm', 'n', 'h', 'o', 'y');
			return str_replace($from, $to, $string);
		}

		/**
		 * Convert Bengali characters to latin
		 * 
		 * @param string $string String
		 * @return string String with Bengali characters converted to latin
		 */
		private static function bengali_to_latin($string) {
			static $from = array('অ', 'আ', 'ই', 'ঈ', 'উ', 'ঊ', 'ঋ', 'ৠ', 'ঌ', 'এ', 'ঐ', 'ও', 'ঔ',
				'ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ', 'ট', 'ঠ', 'ড', 'ড়', 'ঢ', 'ঢ়', 'ণ', 'ত', 'ৎ', 'থ', 'দ', 'ধ', 'ন', 'প', 'ফ', 'ব','ভ', 'ম', 'য', 'য়', 'র', 'ল', 'ব', 'শ', 'ষ', 'স', 'হ', 'ং', 'ঃ', 'ঁ', 'ऽ',
				'০', '১', '২', '৩', '৪', '৫', '৬', '	৭', '৮', '৯');
			static $to = array('a', 'ā', 'I', 'ī', 'u', 'ū', 'ri', 'rri', 'li', 'e', 'ai', 'o', 'au',
				'ka', 'kha', 'ga', 'gha', 'ṅa', 'ca', 'cha', 'ja', 'jha', 'ña', 'ṭa', 'ṭha', 'ḍa', 'ṛa', 'ḍha', 'ṛha', 'ṇa', 'ta', 't', 'tha', 'da', 'dha', 'na', 'pa', 'pha', 'ba', 'bha', 'ma', 'ya', 'ẏa', 'ra', 'la', 'ba', 'śa', 'sha', 'sa', 'ha', 'ṃ', 'ḥ', 'n', "'",
				'0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
			return str_replace($from, $to, $string);
		}

		/**
		 * Convert Devanagari characters to latin
		 * 
		 * @param string $string String
		 * @return string String with Devanagari characters converted to latin
		 */
		private static function devanagari_to_latin($string) {
			static $from = array('ॐ', 'ऀ', 'ँ', 'ं', 'ः', 'अ', 'आ', 'इ', 'ई', 'उ', 'ऊ', 'ऋ', 'ॠ', 'ऌ', 'ॡ', 'ऍ', 'ऎ', 'ए', 'ऐ', 'ऑ', 'ऒ', 'ओ', 'औ', 'ा', 'ि', 'ी', 'ु', 'ू', 'ृ', 'ॄ', 'ॢ', 'ॣ', 'ॅ', 'े', 'ै', 'ॉ', 'ो', 'ौ', 'क़', 'क', 'ख़', 'ख', 'ग़', 'ग', 'ॻ', 'घ', 'ङ', 'च', 'छ', 'ज़', 'ज', 'ॼ', 'झ', 'ञ', 'ट', 'ठ', 'ड़', 'ड', 'ॸ', 'ॾ', 'ढ़', 'ढ', 'ण', 'त', 'थ', 'द', 'ध', 'न', 'प', 'फ़', 'फ', 'ब', 'ॿ', 'भ', 'म', 'य', 'र', 'ल', 'ळ', 'व', 'श', 'ष', 'स', 'ह', 'ऽ', '०', '१', '२', '३', '४', '५', '६', '७', '८', '९', 'ꣳ', '।', '॥');
			static $to = array('oṁ', 'ṁ', 'ṃ', 'ṃ', 'ḥ', 'a', 'ā', 'i', 'ī', 'u', 'ū', 'r̥', ' r̥̄', 'l̥', ' l̥̄', 'ê', 'e', 'e', 'ai', 'ô', 'o', 'o', 'au', 'ā', 'i', 'ī', 'u', 'ū', 'r̥', ' r̥̄', 'l̥', ' l̥̄', 'ê', 'e', 'ai', 'ô', 'o', 'au', 'q', 'k', 'x', 'kh', 'ġ', 'g', 'g', 'gh', 'ṅ', 'c', 'ch', 'z', 'j', 'j', 'jh', 'ñ', 'ṭ', 'ṭh', 'ṛ', 'ḍ', 'ḍ', 'd', 'ṛh', 'ḍh', 'ṇ', 't', 'th', 'd', 'dh', 'n', 'p', 'f', 'ph', 'b', 'b', 'bh', 'm', 'y', 'r', 'l', 'ḷ', 'v', 'ś', 'ṣ', 's', 'h', '\'', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'ṁ', '.', '..');
			return str_replace($from, $to, $string);
		}

		/**
		 * Build an English plural
		 * 
		 * @since 3.4.1
		 * 
		 * @param string $singular Singular
		 * @return string Plural
		 */
		public static function plural($singular) {
			$plural = (substr($singular, -1, 1) == 's')? $singular : $singular . 's'; // Add an "s"
			$plural = preg_replace('/([^aeiou])ys$/', '$1ies', $plural); // "stories" instead of "storys"
			return $plural;
		}

	}
}
