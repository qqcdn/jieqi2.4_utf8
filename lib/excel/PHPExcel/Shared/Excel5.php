<?php

class PHPExcel_Shared_Excel5
{
	static public function sizeCol($sheet, $col = 'A')
	{
		$font = $sheet->getParent()->getDefaultStyle()->getFont();
		$columnDimensions = $sheet->getColumnDimensions();
		if (isset($columnDimensions[$col]) && ($columnDimensions[$col]->getWidth() != -1)) {
			$columnDimension = $columnDimensions[$col];
			$width = $columnDimension->getWidth();
			$pixelWidth = PHPExcel_Shared_Drawing::cellDimensionToPixels($width, $font);
		}
		else if ($sheet->getDefaultColumnDimension()->getWidth() != -1) {
			$defaultColumnDimension = $sheet->getDefaultColumnDimension();
			$width = $defaultColumnDimension->getWidth();
			$pixelWidth = PHPExcel_Shared_Drawing::cellDimensionToPixels($width, $font);
		}
		else {
			$pixelWidth = PHPExcel_Shared_Font::getDefaultColumnWidthByFont($font, true);
		}

		if (isset($columnDimensions[$col]) && !$columnDimensions[$col]->getVisible()) {
			$effectivePixelWidth = 0;
		}
		else {
			$effectivePixelWidth = $pixelWidth;
		}

		return $effectivePixelWidth;
	}

	static public function sizeRow($sheet, $row = 1)
	{
		$font = $sheet->getParent()->getDefaultStyle()->getFont();
		$rowDimensions = $sheet->getRowDimensions();
		if (isset($rowDimensions[$row]) && ($rowDimensions[$row]->getRowHeight() != -1)) {
			$rowDimension = $rowDimensions[$row];
			$rowHeight = $rowDimension->getRowHeight();
			$pixelRowHeight = (int) ceil((4 * $rowHeight) / 3);
		}
		else if ($sheet->getDefaultRowDimension()->getRowHeight() != -1) {
			$defaultRowDimension = $sheet->getDefaultRowDimension();
			$rowHeight = $defaultRowDimension->getRowHeight();
			$pixelRowHeight = PHPExcel_Shared_Drawing::pointsToPixels($rowHeight);
		}
		else {
			$pointRowHeight = PHPExcel_Shared_Font::getDefaultRowHeightByFont($font);
			$pixelRowHeight = PHPExcel_Shared_Font::fontSizeToPixels($pointRowHeight);
		}

		if (isset($rowDimensions[$row]) && !$rowDimensions[$row]->getVisible()) {
			$effectivePixelRowHeight = 0;
		}
		else {
			$effectivePixelRowHeight = $pixelRowHeight;
		}

		return $effectivePixelRowHeight;
	}

	static public function getDistanceX(PHPExcel_Worksheet $sheet, $startColumn = 'A', $startOffsetX = 0, $endColumn = 'A', $endOffsetX = 0)
	{
		$distanceX = 0;
		$startColumnIndex = PHPExcel_Cell::columnIndexFromString($startColumn) - 1;
		$endColumnIndex = PHPExcel_Cell::columnIndexFromString($endColumn) - 1;

		for ($i = $startColumnIndex; $i <= $endColumnIndex; ++$i) {
			$distanceX += self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($i));
		}

		$distanceX -= (int) floor((self::sizeCol($sheet, $startColumn) * $startOffsetX) / 1024);
		$distanceX -= (int) floor(self::sizeCol($sheet, $endColumn) * (1 - ($endOffsetX / 1024)));
		return $distanceX;
	}

	static public function getDistanceY(PHPExcel_Worksheet $sheet, $startRow = 1, $startOffsetY = 0, $endRow = 1, $endOffsetY = 0)
	{
		$distanceY = 0;

		for ($row = $startRow; $row <= $endRow; ++$row) {
			$distanceY += self::sizeRow($sheet, $row);
		}

		$distanceY -= (int) floor((self::sizeRow($sheet, $startRow) * $startOffsetY) / 256);
		$distanceY -= (int) floor(self::sizeRow($sheet, $endRow) * (1 - ($endOffsetY / 256)));
		return $distanceY;
	}

	static public function oneAnchor2twoAnchor($sheet, $coordinates, $offsetX, $offsetY, $width, $height)
	{
		list($column, $row) = PHPExcel_Cell::coordinateFromString($coordinates);
		$col_start = PHPExcel_Cell::columnIndexFromString($column) - 1;
		$row_start = $row - 1;
		$x1 = $offsetX;
		$y1 = $offsetY;
		$col_end = $col_start;
		$row_end = $row_start;

		if (self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_start)) <= $x1) {
			$x1 = 0;
		}

		if (self::sizeRow($sheet, $row_start + 1) <= $y1) {
			$y1 = 0;
		}

		$width = ($width + $x1) - 1;
		$height = ($height + $y1) - 1;

		while (self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_end)) <= $width) {
			$width -= self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_end));
			++$col_end;
		}

		while (self::sizeRow($sheet, $row_end + 1) <= $height) {
			$height -= self::sizeRow($sheet, $row_end + 1);
			++$row_end;
		}

		if (self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_start)) == 0) {
			return NULL;
		}

		if (self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_end)) == 0) {
			return NULL;
		}

		if (self::sizeRow($sheet, $row_start + 1) == 0) {
			return NULL;
		}

		if (self::sizeRow($sheet, $row_end + 1) == 0) {
			return NULL;
		}

		$x1 = ($x1 / self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_start))) * 1024;
		$y1 = ($y1 / self::sizeRow($sheet, $row_start + 1)) * 256;
		$x2 = (($width + 1) / self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_end))) * 1024;
		$y2 = (($height + 1) / self::sizeRow($sheet, $row_end + 1)) * 256;
		$startCoordinates = PHPExcel_Cell::stringFromColumnIndex($col_start) . ($row_start + 1);
		$endCoordinates = PHPExcel_Cell::stringFromColumnIndex($col_end) . ($row_end + 1);
		$twoAnchor = array('startCoordinates' => $startCoordinates, 'startOffsetX' => $x1, 'startOffsetY' => $y1, 'endCoordinates' => $endCoordinates, 'endOffsetX' => $x2, 'endOffsetY' => $y2);
		return $twoAnchor;
	}
}


?>
