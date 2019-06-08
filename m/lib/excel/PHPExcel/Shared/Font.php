<?php

class PHPExcel_Shared_Font
{
	const AUTOSIZE_METHOD_APPROX = 'approx';
	const AUTOSIZE_METHOD_EXACT = 'exact';
	const CHARSET_ANSI_LATIN = 0;
	const CHARSET_SYSTEM_DEFAULT = 1;
	const CHARSET_SYMBOL = 2;
	const CHARSET_APPLE_ROMAN = 77;
	const CHARSET_ANSI_JAPANESE_SHIFTJIS = 128;
	const CHARSET_ANSI_KOREAN_HANGUL = 129;
	const CHARSET_ANSI_KOREAN_JOHAB = 130;
	const CHARSET_ANSI_CHINESE_SIMIPLIFIED = 134;
	const CHARSET_ANSI_CHINESE_TRADITIONAL = 136;
	const CHARSET_ANSI_GREEK = 161;
	const CHARSET_ANSI_TURKISH = 162;
	const CHARSET_ANSI_VIETNAMESE = 163;
	const CHARSET_ANSI_HEBREW = 177;
	const CHARSET_ANSI_ARABIC = 178;
	const CHARSET_ANSI_BALTIC = 186;
	const CHARSET_ANSI_CYRILLIC = 204;
	const CHARSET_ANSI_THAI = 221;
	const CHARSET_ANSI_LATIN_II = 238;
	const CHARSET_OEM_LATIN_I = 255;
	const ARIAL = 'arial.ttf';
	const ARIAL_BOLD = 'arialbd.ttf';
	const ARIAL_ITALIC = 'ariali.ttf';
	const ARIAL_BOLD_ITALIC = 'arialbi.ttf';
	const CALIBRI = 'CALIBRI.TTF';
	const CALIBRI_BOLD = 'CALIBRIB.TTF';
	const CALIBRI_ITALIC = 'CALIBRII.TTF';
	const CALIBRI_BOLD_ITALIC = 'CALIBRIZ.TTF';
	const COMIC_SANS_MS = 'comic.ttf';
	const COMIC_SANS_MS_BOLD = 'comicbd.ttf';
	const COURIER_NEW = 'cour.ttf';
	const COURIER_NEW_BOLD = 'courbd.ttf';
	const COURIER_NEW_ITALIC = 'couri.ttf';
	const COURIER_NEW_BOLD_ITALIC = 'courbi.ttf';
	const GEORGIA = 'georgia.ttf';
	const GEORGIA_BOLD = 'georgiab.ttf';
	const GEORGIA_ITALIC = 'georgiai.ttf';
	const GEORGIA_BOLD_ITALIC = 'georgiaz.ttf';
	const IMPACT = 'impact.ttf';
	const LIBERATION_SANS = 'LiberationSans-Regular.ttf';
	const LIBERATION_SANS_BOLD = 'LiberationSans-Bold.ttf';
	const LIBERATION_SANS_ITALIC = 'LiberationSans-Italic.ttf';
	const LIBERATION_SANS_BOLD_ITALIC = 'LiberationSans-BoldItalic.ttf';
	const LUCIDA_CONSOLE = 'lucon.ttf';
	const LUCIDA_SANS_UNICODE = 'l_10646.ttf';
	const MICROSOFT_SANS_SERIF = 'micross.ttf';
	const PALATINO_LINOTYPE = 'pala.ttf';
	const PALATINO_LINOTYPE_BOLD = 'palab.ttf';
	const PALATINO_LINOTYPE_ITALIC = 'palai.ttf';
	const PALATINO_LINOTYPE_BOLD_ITALIC = 'palabi.ttf';
	const SYMBOL = 'symbol.ttf';
	const TAHOMA = 'tahoma.ttf';
	const TAHOMA_BOLD = 'tahomabd.ttf';
	const TIMES_NEW_ROMAN = 'times.ttf';
	const TIMES_NEW_ROMAN_BOLD = 'timesbd.ttf';
	const TIMES_NEW_ROMAN_ITALIC = 'timesi.ttf';
	const TIMES_NEW_ROMAN_BOLD_ITALIC = 'timesbi.ttf';
	const TREBUCHET_MS = 'trebuc.ttf';
	const TREBUCHET_MS_BOLD = 'trebucbd.ttf';
	const TREBUCHET_MS_ITALIC = 'trebucit.ttf';
	const TREBUCHET_MS_BOLD_ITALIC = 'trebucbi.ttf';
	const VERDANA = 'verdana.ttf';
	const VERDANA_BOLD = 'verdanab.ttf';
	const VERDANA_ITALIC = 'verdanai.ttf';
	const VERDANA_BOLD_ITALIC = 'verdanaz.ttf';

	static private $_autoSizeMethods = array(self::AUTOSIZE_METHOD_APPROX, self::AUTOSIZE_METHOD_EXACT);
	/**
	 * AutoSize method
	 *
	 * @var string
	 */
	static private $autoSizeMethod = self::AUTOSIZE_METHOD_APPROX;
	/**
	 * Path to folder containing TrueType font .ttf files
	 *
	 * @var string
	 */
	static private $trueTypeFontPath;
	/**
	 * How wide is a default column for a given default font and size?
	 * Empirical data found by inspecting real Excel files and reading off the pixel width
	 * in Microsoft Office Excel 2007.
	 *
	 * @var array
	 */
	static public $defaultColumnWidths = array(
		'Arial'   => array(
			1  => array('px' => 24, 'width' => 12),
			2  => array('px' => 24, 'width' => 12),
			3  => array('px' => 32, 'width' => 10.6640625),
			4  => array('px' => 32, 'width' => 10.6640625),
			5  => array('px' => 40, 'width' => 10),
			6  => array('px' => 48, 'width' => 9.59765625),
			7  => array('px' => 48, 'width' => 9.59765625),
			8  => array('px' => 56, 'width' => 9.33203125),
			9  => array('px' => 64, 'width' => 9.140625),
			10 => array('px' => 64, 'width' => 9.140625)
			),
		'Calibri' => array(
			1  => array('px' => 24, 'width' => 12),
			2  => array('px' => 24, 'width' => 12),
			3  => array('px' => 32, 'width' => 10.6640625),
			4  => array('px' => 32, 'width' => 10.6640625),
			5  => array('px' => 40, 'width' => 10),
			6  => array('px' => 48, 'width' => 9.59765625),
			7  => array('px' => 48, 'width' => 9.59765625),
			8  => array('px' => 56, 'width' => 9.33203125),
			9  => array('px' => 56, 'width' => 9.33203125),
			10 => array('px' => 64, 'width' => 9.140625),
			11 => array('px' => 64, 'width' => 9.140625)
			),
		'Verdana' => array(
			1  => array('px' => 24, 'width' => 12),
			2  => array('px' => 24, 'width' => 12),
			3  => array('px' => 32, 'width' => 10.6640625),
			4  => array('px' => 32, 'width' => 10.6640625),
			5  => array('px' => 40, 'width' => 10),
			6  => array('px' => 48, 'width' => 9.59765625),
			7  => array('px' => 48, 'width' => 9.59765625),
			8  => array('px' => 64, 'width' => 9.140625),
			9  => array('px' => 72, 'width' => 9),
			10 => array('px' => 72, 'width' => 9)
			)
		);

	static public function setAutoSizeMethod($pValue = self::AUTOSIZE_METHOD_APPROX)
	{
		if (!in_array($pValue, self::$_autoSizeMethods)) {
			return false;
		}

		self::$autoSizeMethod = $pValue;
		return true;
	}

	static public function getAutoSizeMethod()
	{
		return self::$autoSizeMethod;
	}

	static public function setTrueTypeFontPath($pValue = '')
	{
		self::$trueTypeFontPath = $pValue;
	}

	static public function getTrueTypeFontPath()
	{
		return self::$trueTypeFontPath;
	}

	static public function calculateColumnWidth(PHPExcel_Style_Font $font, $cellText = '', $rotation = 0, PHPExcel_Style_Font $defaultFont = NULL)
	{
		if ($cellText instanceof PHPExcel_RichText) {
			$cellText = $cellText->getPlainText();
		}

		if (strpos($cellText, "\n") !== false) {
			$lineTexts = explode("\n", $cellText);
			$lineWitdhs = array();

			foreach ($lineTexts as $lineText) {
				$lineWidths[] = self::calculateColumnWidth($font, $lineText, $rotation = 0, $defaultFont);
			}

			return max($lineWidths);
		}

		try {
			if (self::$autoSizeMethod == self::AUTOSIZE_METHOD_APPROX) {
				throw new PHPExcel_Exception('AutoSize method is set to approx');
			}

			$columnWidth = self::getTextWidthPixelsExact($cellText, $font, $rotation);
			$columnWidth += ceil(self::getTextWidthPixelsExact('0', $font, 0) * 1.07);
		}
		catch (PHPExcel_Exception $e) {
			$columnWidth = self::getTextWidthPixelsApprox($cellText, $font, $rotation);
			$columnWidth += self::getTextWidthPixelsApprox('n', $font, 0);
		}

		$columnWidth = PHPExcel_Shared_Drawing::pixelsToCellDimension($columnWidth, $defaultFont);
		return round($columnWidth, 6);
	}

	static public function getTextWidthPixelsExact($text, PHPExcel_Style_Font $font, $rotation = 0)
	{
		if (!function_exists('imagettfbbox')) {
			throw new PHPExcel_Exception('GD library needs to be enabled');
		}

		$fontFile = self::getTrueTypeFontFileFromFont($font);
		$textBox = imagettfbbox($font->getSize(), $rotation, $fontFile, $text);
		$lowerLeftCornerX = $textBox[0];
		$lowerLeftCornerY = $textBox[1];
		$lowerRightCornerX = $textBox[2];
		$lowerRightCornerY = $textBox[3];
		$upperRightCornerX = $textBox[4];
		$upperRightCornerY = $textBox[5];
		$upperLeftCornerX = $textBox[6];
		$upperLeftCornerY = $textBox[7];
		$textWidth = max($lowerRightCornerX - $upperLeftCornerX, $upperRightCornerX - $lowerLeftCornerX);
		return $textWidth;
	}

	static public function getTextWidthPixelsApprox($columnText, PHPExcel_Style_Font $font = NULL, $rotation = 0)
	{
		$fontName = $font->getName();
		$fontSize = $font->getSize();

		switch ($fontName) {
		case 'Calibri':
			$columnWidth = (int) (8.26 * PHPExcel_Shared_String::CountCharacters($columnText));
			$columnWidth = ($columnWidth * $fontSize) / 11;
			break;

		case 'Arial':
			$columnWidth = (int) (7 * PHPExcel_Shared_String::CountCharacters($columnText));
			$columnWidth = ($columnWidth * $fontSize) / 10;
			break;

		case 'Verdana':
			$columnWidth = (int) (8 * PHPExcel_Shared_String::CountCharacters($columnText));
			$columnWidth = ($columnWidth * $fontSize) / 10;
			break;

		default:
			$columnWidth = (int) (8.26 * PHPExcel_Shared_String::CountCharacters($columnText));
			$columnWidth = ($columnWidth * $fontSize) / 11;
			break;
		}

		if ($rotation !== 0) {
			if ($rotation == -165) {
				$columnWidth = 4;
			}
			else {
				$columnWidth = ($columnWidth * cos(deg2rad($rotation))) + (($fontSize * abs(sin(deg2rad($rotation)))) / 5);
			}
		}

		$columnWidth = (int) $columnWidth;
		return $columnWidth;
	}

	static public function fontSizeToPixels($fontSizeInPoints = 11)
	{
		return (int) ((4 / 3) * $fontSizeInPoints);
	}

	static public function inchSizeToPixels($sizeInInch = 1)
	{
		return $sizeInInch * 96;
	}

	static public function centimeterSizeToPixels($sizeInCm = 1)
	{
		return $sizeInCm * 37.795275591;
	}

	static public function getTrueTypeFontFileFromFont($font)
	{
		if (!file_exists(self::$trueTypeFontPath) || !is_dir(self::$trueTypeFontPath)) {
			throw new PHPExcel_Exception('Valid directory to TrueType Font files not specified');
		}

		$name = $font->getName();
		$bold = $font->getBold();
		$italic = $font->getItalic();

		switch ($name) {
		case 'Arial':
			$fontFile = ($bold ? ($italic ? self::ARIAL_BOLD_ITALIC : self::ARIAL_BOLD) : ($italic ? self::ARIAL_ITALIC : self::ARIAL));
			break;

		case 'Calibri':
			$fontFile = ($bold ? ($italic ? self::CALIBRI_BOLD_ITALIC : self::CALIBRI_BOLD) : ($italic ? self::CALIBRI_ITALIC : self::CALIBRI));
			break;

		case 'Courier New':
			$fontFile = ($bold ? ($italic ? self::COURIER_NEW_BOLD_ITALIC : self::COURIER_NEW_BOLD) : ($italic ? self::COURIER_NEW_ITALIC : self::COURIER_NEW));
			break;

		case 'Comic Sans MS':
			$fontFile = ($bold ? self::COMIC_SANS_MS_BOLD : self::COMIC_SANS_MS);
			break;

		case 'Georgia':
			$fontFile = ($bold ? ($italic ? self::GEORGIA_BOLD_ITALIC : self::GEORGIA_BOLD) : ($italic ? self::GEORGIA_ITALIC : self::GEORGIA));
			break;

		case 'Impact':
			$fontFile = self::IMPACT;
			break;

		case 'Liberation Sans':
			$fontFile = ($bold ? ($italic ? self::LIBERATION_SANS_BOLD_ITALIC : self::LIBERATION_SANS_BOLD) : ($italic ? self::LIBERATION_SANS_ITALIC : self::LIBERATION_SANS));
			break;

		case 'Lucida Console':
			$fontFile = self::LUCIDA_CONSOLE;
			break;

		case 'Lucida Sans Unicode':
			$fontFile = self::LUCIDA_SANS_UNICODE;
			break;

		case 'Microsoft Sans Serif':
			$fontFile = self::MICROSOFT_SANS_SERIF;
			break;

		case 'Palatino Linotype':
			$fontFile = ($bold ? ($italic ? self::PALATINO_LINOTYPE_BOLD_ITALIC : self::PALATINO_LINOTYPE_BOLD) : ($italic ? self::PALATINO_LINOTYPE_ITALIC : self::PALATINO_LINOTYPE));
			break;

		case 'Symbol':
			$fontFile = self::SYMBOL;
			break;

		case 'Tahoma':
			$fontFile = ($bold ? self::TAHOMA_BOLD : self::TAHOMA);
			break;

		case 'Times New Roman':
			$fontFile = ($bold ? ($italic ? self::TIMES_NEW_ROMAN_BOLD_ITALIC : self::TIMES_NEW_ROMAN_BOLD) : ($italic ? self::TIMES_NEW_ROMAN_ITALIC : self::TIMES_NEW_ROMAN));
			break;

		case 'Trebuchet MS':
			$fontFile = ($bold ? ($italic ? self::TREBUCHET_MS_BOLD_ITALIC : self::TREBUCHET_MS_BOLD) : ($italic ? self::TREBUCHET_MS_ITALIC : self::TREBUCHET_MS));
			break;

		case 'Verdana':
			$fontFile = ($bold ? ($italic ? self::VERDANA_BOLD_ITALIC : self::VERDANA_BOLD) : ($italic ? self::VERDANA_ITALIC : self::VERDANA));
			break;

		default:
			throw new PHPExcel_Exception('Unknown font name "' . $name . '". Cannot map to TrueType font file');
			break;
		}

		$fontFile = self::$trueTypeFontPath . $fontFile;

		if (!file_exists($fontFile)) {
			throw new PHPExcel_Exception('TrueType Font file not found');
		}

		return $fontFile;
	}

	static public function getCharsetFromFontName($name)
	{
		switch ($name) {
		case 'EucrosiaUPC':
			return self::CHARSET_ANSI_THAI;
		case 'Wingdings':
			return self::CHARSET_SYMBOL;
		case 'Wingdings 2':
			return self::CHARSET_SYMBOL;
		case 'Wingdings 3':
			return self::CHARSET_SYMBOL;
		default:
			return self::CHARSET_ANSI_LATIN;
		}
	}

	static public function getDefaultColumnWidthByFont(PHPExcel_Style_Font $font, $pPixels = false)
	{
		if (isset(self::$defaultColumnWidths[$font->getName()][$font->getSize()])) {
			$columnWidth = ($pPixels ? self::$defaultColumnWidths[$font->getName()][$font->getSize()]['px'] : self::$defaultColumnWidths[$font->getName()][$font->getSize()]['width']);
		}
		else {
			$columnWidth = ($pPixels ? self::$defaultColumnWidths['Calibri'][11]['px'] : self::$defaultColumnWidths['Calibri'][11]['width']);
			$columnWidth = ($columnWidth * $font->getSize()) / 11;

			if ($pPixels) {
				$columnWidth = (int) round($columnWidth);
			}
		}

		return $columnWidth;
	}

	static public function getDefaultRowHeightByFont(PHPExcel_Style_Font $font)
	{
		switch ($font->getName()) {
		case 'Arial':
			switch ($font->getSize()) {
			case 10:
				$rowHeight = 12.75;
				break;

			case 9:
				$rowHeight = 12;
				break;

			case 8:
				$rowHeight = 11.25;
				break;

			case 7:
				$rowHeight = 9;
				break;

			case 6:
			case 5:
				$rowHeight = 8.25;
				break;

			case 4:
				$rowHeight = 6.75;
				break;

			case 3:
				$rowHeight = 6;
				break;

			case 2:
			case 1:
				$rowHeight = 5.25;
				break;

			default:
				$rowHeight = (12.75 * $font->getSize()) / 10;
				break;
			}

			break;

		case 'Calibri':
			switch ($font->getSize()) {
			case 11:
				$rowHeight = 15;
				break;

			case 10:
				$rowHeight = 12.75;
				break;

			case 9:
				$rowHeight = 12;
				break;

			case 8:
				$rowHeight = 11.25;
				break;

			case 7:
				$rowHeight = 9;
				break;

			case 6:
			case 5:
				$rowHeight = 8.25;
				break;

			case 4:
				$rowHeight = 6.75;
				break;

			case 3:
				$rowHeight = 6;
				break;

			case 2:
			case 1:
				$rowHeight = 5.25;
				break;

			default:
				$rowHeight = (15 * $font->getSize()) / 11;
				break;
			}

			break;

		case 'Verdana':
			switch ($font->getSize()) {
			case 10:
				$rowHeight = 12.75;
				break;

			case 9:
				$rowHeight = 11.25;
				break;

			case 8:
				$rowHeight = 10.5;
				break;

			case 7:
				$rowHeight = 9;
				break;

			case 6:
			case 5:
				$rowHeight = 8.25;
				break;

			case 4:
				$rowHeight = 6.75;
				break;

			case 3:
				$rowHeight = 6;
				break;

			case 2:
			case 1:
				$rowHeight = 5.25;
				break;

			default:
				$rowHeight = (12.75 * $font->getSize()) / 10;
				break;
			}

			break;

		default:
			$rowHeight = (15 * $font->getSize()) / 11;
			break;
		}

		return $rowHeight;
	}
}


?>
