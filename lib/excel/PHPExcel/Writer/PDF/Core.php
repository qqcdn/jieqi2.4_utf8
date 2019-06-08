<?php

abstract class PHPExcel_Writer_PDF_Core extends PHPExcel_Writer_HTML
{
	/**
     * Temporary storage directory
     *
     * @var string
     */
	protected $_tempDir = '';
	/**
     * Font
     *
     * @var string
     */
	protected $_font = 'freesans';
	/**
     * Orientation (Over-ride)
     *
     * @var string
     */
	protected $_orientation;
	/**
     * Paper size (Over-ride)
     *
     * @var int
     */
	protected $_paperSize;
	/**
     * Temporary storage for Save Array Return type
     *
     * @var string
     */
	private $_saveArrayReturnType;
	/**
     * Paper Sizes xRef List
     *
     * @var array
     */
	static protected $_paperSizes = array(
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER                        => 'LETTER',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_SMALL                  => 'LETTER',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_TABLOID                       => array(792, 1224),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEDGER                        => array(1224, 792),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL                         => 'LEGAL',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_STATEMENT                     => array(396, 612),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_EXECUTIVE                     => 'EXECUTIVE',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3                            => 'A3',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4                            => 'A4',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4_SMALL                      => 'A4',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A5                            => 'A5',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_B4                            => 'B4',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_B5                            => 'B5',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO                         => 'FOLIO',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_QUARTO                        => array(609.45, 779.53),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_STANDARD_1                    => array(720, 1008),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_STANDARD_2                    => array(792, 1224),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_NOTE                          => 'LETTER',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_NO9_ENVELOPE                  => array(279, 639),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_NO10_ENVELOPE                 => array(297, 684),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_NO11_ENVELOPE                 => array(324, 747),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_NO12_ENVELOPE                 => array(342, 792),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_NO14_ENVELOPE                 => array(360, 828),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_C                             => array(1224, 1584),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_D                             => array(1584, 2448),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_E                             => array(2448, 3168),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_DL_ENVELOPE                   => array(311.81, 623.62),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_C5_ENVELOPE                   => 'C5',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_C3_ENVELOPE                   => 'C3',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_C4_ENVELOPE                   => 'C4',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_C6_ENVELOPE                   => 'C6',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_C65_ENVELOPE                  => array(323.15, 649.13),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_B4_ENVELOPE                   => 'B4',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_B5_ENVELOPE                   => 'B5',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_B6_ENVELOPE                   => array(498.9, 354.33),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_ITALY_ENVELOPE                => array(311.81, 651.97),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_MONARCH_ENVELOPE              => array(279, 540),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_6_3_4_ENVELOPE                => array(261, 468),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_US_STANDARD_FANFOLD           => array(1071, 792),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_GERMAN_STANDARD_FANFOLD       => array(612, 864),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_GERMAN_LEGAL_FANFOLD          => 'FOLIO',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_ISO_B4                        => 'B4',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_JAPANESE_DOUBLE_POSTCARD      => array(566.93, 419.53),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_STANDARD_PAPER_1              => array(648, 792),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_STANDARD_PAPER_2              => array(720, 792),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_STANDARD_PAPER_3              => array(1080, 792),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_INVITE_ENVELOPE               => array(623.62, 623.62),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_EXTRA_PAPER            => array(667.8, 864),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL_EXTRA_PAPER             => array(667.8, 1080),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_TABLOID_EXTRA_PAPER           => array(841.68, 1296),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4_EXTRA_PAPER                => array(668.98, 912.76),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_TRANSVERSE_PAPER       => array(595.8, 792),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4_TRANSVERSE_PAPER           => 'A4',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_EXTRA_TRANSVERSE_PAPER => array(667.8, 864),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_SUPERA_SUPERA_A4_PAPER        => array(643.46, 1009.13),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_SUPERB_SUPERB_A3_PAPER        => array(864.57, 1380.47),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_PLUS_PAPER             => array(612, 913.68),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4_PLUS_PAPER                 => array(595.28, 935.43),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A5_TRANSVERSE_PAPER           => 'A5',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_JIS_B5_TRANSVERSE_PAPER       => array(515.91, 728.5),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3_EXTRA_PAPER                => array(912.76, 1261.42),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A5_EXTRA_PAPER                => array(493.23, 666.14),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_ISO_B5_EXTRA_PAPER            => array(569.76, 782.36),
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A2_PAPER                      => 'A2',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3_TRANSVERSE_PAPER           => 'A3',
		PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3_EXTRA_TRANSVERSE_PAPER     => array(912.76, 1261.42)
		);

	public function __construct(PHPExcel $phpExcel)
	{
		parent::__construct($phpExcel);
		$this->setUseInlineCss(true);
		$this->_tempDir = PHPExcel_Shared_File::sys_get_temp_dir();
	}

	public function getFont()
	{
		return $this->_font;
	}

	public function setFont($fontName)
	{
		$this->_font = $fontName;
		return $this;
	}

	public function getPaperSize()
	{
		return $this->_paperSize;
	}

	public function setPaperSize($pValue = PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER)
	{
		$this->_paperSize = $pValue;
		return $this;
	}

	public function getOrientation()
	{
		return $this->_orientation;
	}

	public function setOrientation($pValue = PHPExcel_Worksheet_PageSetup::ORIENTATION_DEFAULT)
	{
		$this->_orientation = $pValue;
		return $this;
	}

	public function getTempDir()
	{
		return $this->_tempDir;
	}

	public function setTempDir($pValue = '')
	{
		if (is_dir($pValue)) {
			$this->_tempDir = $pValue;
		}
		else {
			throw new PHPExcel_Writer_Exception('Directory does not exist: ' . $pValue);
		}

		return $this;
	}

	protected function prepareForSave($pFilename = NULL)
	{
		$this->_phpExcel->garbageCollect();
		$this->_saveArrayReturnType = PHPExcel_Calculation::getArrayReturnType();
		PHPExcel_Calculation::setArrayReturnType(PHPExcel_Calculation::RETURN_ARRAY_AS_VALUE);
		$fileHandle = fopen($pFilename, 'w');

		if ($fileHandle === false) {
			throw new PHPExcel_Writer_Exception('Could not open file ' . $pFilename . ' for writing.');
		}

		$this->_isPdf = true;
		$this->buildCSS(true);
		return $fileHandle;
	}

	protected function restoreStateAfterSave($fileHandle)
	{
		fclose($fileHandle);
		PHPExcel_Calculation::setArrayReturnType($this->_saveArrayReturnType);
	}
}

?>
