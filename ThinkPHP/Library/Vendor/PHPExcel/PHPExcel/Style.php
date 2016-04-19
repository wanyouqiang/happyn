<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2009 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.0, 2009-08-10
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../');
}

/** PHPExcel_Style_Color */
require_once PHPEXCEL_ROOT . 'PHPExcel/Style/Color.php';

/** PHPExcel_Style_Font */
require_once PHPEXCEL_ROOT . 'PHPExcel/Style/Font.php';

/** PHPExcel_Style_Fill */
require_once PHPEXCEL_ROOT . 'PHPExcel/Style/Fill.php';

/** PHPExcel_Style_Borders */
require_once PHPEXCEL_ROOT . 'PHPExcel/Style/Borders.php';

/** PHPExcel_Style_Alignment */
require_once PHPEXCEL_ROOT . 'PHPExcel/Style/Alignment.php';

/** PHPExcel_Style_NumberFormat */
require_once PHPEXCEL_ROOT . 'PHPExcel/Style/NumberFormat.php';

/** PHPExcel_Style_Conditional */
require_once PHPEXCEL_ROOT . 'PHPExcel/Style/Conditional.php';

/** PHPExcel_Style_Protection */
require_once PHPEXCEL_ROOT . 'PHPExcel/Style/Protection.php';

/** PHPExcel_IComparable */
require_once PHPEXCEL_ROOT . 'PHPExcel/IComparable.php';

/**
 * PHPExcel_Style
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style implements PHPExcel_IComparable
{
	/**
	 * Font
	 *
	 * @var PHPExcel_Style_Font
	 */
	private $_font;
	
	/**
	 * Fill
	 *
	 * @var PHPExcel_Style_Fill
	 */
	private $_fill;

	/**
	 * Borders
	 *
	 * @var PHPExcel_Style_Borders
	 */
	private $_borders;
	
	/**
	 * Alignment
	 *
	 * @var PHPExcel_Style_Alignment
	 */
	private $_alignment;
	
	/**
	 * Number Format
	 *
	 * @var PHPExcel_Style_NumberFormat
	 */
	private $_numberFormat;
	
	/**
	 * Conditional styles
	 *
	 * @var PHPExcel_Style_Conditional[]
	 */
	private $_conditionalStyles;
	
	/**
	 * Protection
	 *
	 * @var PHPExcel_Style_Protection
	 */
	private $_protection;

	/**
	 * Style supervisor?
	 *
	 * @var boolean
	 */
	private $_isSupervisor;

	/**
	 * Parent. Only used for style supervisor
	 *
	 * @var PHPExcel
	 */
	private $_parent;

	/**
	 * Index of style in collection. Only used for real style.
	 *
	 * @var int
	 */
	private $_index;

    /**
     * Create a new PHPExcel_Style
	 *
	 * @param boolean $isSupervisor
     */
    public function __construct($isSupervisor = false)
    {
    	// Supervisor?
		$this->_isSupervisor = $isSupervisor;

		// Initialise values
    	$this->_conditionalStyles 	= array();
		$this->_font				= new PHPExcel_Style_Font($isSupervisor);
		$this->_fill				= new PHPExcel_Style_Fill($isSupervisor);
		$this->_borders				= new PHPExcel_Style_Borders($isSupervisor);
		$this->_alignment			= new PHPExcel_Style_Alignment($isSupervisor);
		$this->_numberFormat		= new PHPExcel_Style_NumberFormat($isSupervisor);
		$this->_protection			= new PHPExcel_Style_Protection($isSupervisor);

		// bind parent if we are a supervisor
		if ($isSupervisor) {
			$this->_font->bindParent($this);
			$this->_fill->bindParent($this);
			$this->_borders->bindParent($this);
			$this->_alignment->bindParent($this);
			$this->_numberFormat->bindParent($this);
			$this->_protection->bindParent($this);
		}
    }

	/**
	 * Bind parent. Only used for supervisor
	 *
	 * @param PHPExcel $parent
	 * @return PHPExcel_Style
	 */
	public function bindParent($parent)
	{
		$this->_parent = $parent;
		return $this;
	}
	
	/**
	 * Is this a supervisor or a real style component?
	 *
	 * @return boolean
	 */
	public function getIsSupervisor()
	{
		return $this->_isSupervisor;
	}

	/**
	 * Get the shared style component for the currently active cell in currently active sheet.
	 * Only used for style supervisor
	 *
	 * @return PHPExcel_Style
	 */
	public function getSharedComponent()
	{
		$activeSheet = $this->getActiveSheet();
		$selectedCell = $this->getXActiveCell(); // e.g. 'A1'

		if ($activeSheet->cellExists($selectedCell)) {
			$cell = $activeSheet->getCell($selectedCell);
			$xfIndex = $cell->getXfIndex();
		} else {
			$xfIndex = 0;
		}

		$activeStyle = $this->_parent->getCellXfByIndex($xfIndex);
		return $activeStyle;
	}

	/**
	 * Get the currently active sheet. Only used for supervisor
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function getActiveSheet()
	{
		return $this->_parent->getActiveSheet();
	}

	/**
	 * Get the currently active cell coordinate in currently active sheet.
	 * Only used for supervisor
	 *
	 * @return string E.g. 'A1'
	 */
	public function getXSelectedCells()
	{
		return $this->_parent->getActiveSheet()->getXSelectedCells();
	}

	/**
	 * Get the currently active cell coordinate in currently active sheet.
	 * Only used for supervisor
	 *
	 * @return string E.g. 'A1'
	 */
	public function getXActiveCell()
	{
		return $this->_parent->getActiveSheet()->getXActiveCell();
	}

	/**
	 * Get parent. Only used for style supervisor
	 *
	 * @return PHPExcel
	 */
	public function getParent()
	{
		return $this->_parent;
	}

    /**
     * Apply styles from array
     * 
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray(
     * 		array(
     * 			'font'    => array(
     * 				'name'      => 'Arial',
     * 				'bold'      => true,
     * 				'italic'    => false,
     * 				'underline' => PHPExcel_Style_Font::UNDERLINE_DOUBLE,
     * 				'strike'    => false,
     * 				'color'     => array(
     * 					'rgb' => '808080'
     * 				)
     * 			),
     * 			'borders' => array(
     * 				'bottom'     => array(
     * 					'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
     * 					'color' => array(
     * 						'rgb' => '808080'
     * 					)
     * 				),
     * 				'top'     => array(
     * 					'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
     * 					'color' => array(
     * 						'rgb' => '808080'
     * 					)
     * 				)
     * 			)
     * 		)
     * );
     * </code>
     * 
     * @param	array	$pStyles	Array containing style information
     * @param 	boolean		$pAdvanced	Advanced mode for setting borders. 
     * @throws	Exception
     * @return PHPExcel_Style
     */
	public function applyFromArray($pStyles = null, $pAdvanced = true) {
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {

				$pRange = $this->getXSelectedCells();

				if (is_array($pStyles)) {
					// Uppercase coordinate
					$pRange = strtoupper($pRange);

					// Is it a cell range or a single cell?
					$rangeA 	= '';
					$rangeB 	= '';
					if (strpos($pRange, ':') === false) {
						$rangeA = $pRange;
						$rangeB = $pRange;
					} else {
						list($rangeA, $rangeB) = explode(':', $pRange);
					}

					// Calculate range outer borders
					$rangeStart = PHPExcel_Cell::coordinateFromString($rangeA);
					$rangeEnd 	= PHPExcel_Cell::coordinateFromString($rangeB);

					// Translate column into index
					$rangeStart[0]	= PHPExcel_Cell::columnIndexFromString($rangeStart[0]) - 1;
					$rangeEnd[0]	= PHPExcel_Cell::columnIndexFromString($rangeEnd[0]) - 1;

					// Make sure we can loop upwards on rows and columns
					if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
						$tmp = $rangeStart;
						$rangeStart = $rangeEnd;
						$rangeEnd = $tmp;
					}

					// Advanced mode
					if ($pAdvanced && isset($pStyles['borders'])) {

						// 'allborders' is a shorthand property for 'outline' and 'inside' and
						//		it applies to components that have not been set explicitly
						if (isset($pStyles['borders']['allborders'])) {
							foreach (array('outline', 'inside') as $component) {
								if (!isset($pStyles['borders'][$component])) {
									$pStyles['borders'][$component] = $pStyles['borders']['allborders'];
								}
							}
							unset($pStyles['borders']['allborders']); // not needed any more
						}

						// 'outline' is a shorthand property for 'top', 'right', 'bottom', 'left'
						//		it applies to components that have not been set explicitly
						if (isset($pStyles['borders']['outline'])) {
							foreach (array('top', 'right', 'bottom', 'left') as $component) {
								if (!isset($pStyles['borders'][$component])) {
									$pStyles['borders'][$component] = $pStyles['borders']['outline'];
								}
							}
							unset($pStyles['borders']['outline']); // not needed any more
						}

						// 'inside' is a shorthand property for 'vertical' and 'horizontal'
						//		it applies to components that have not been set explicitly
						if (isset($pStyles['borders']['inside'])) {
							foreach (array('vertical', 'horizontal') as $component) {
								if (!isset($pStyles['borders'][$component])) {
									$pStyles['borders'][$component] = $pStyles['borders']['inside'];
								}
							}
							unset($pStyles['borders']['inside']); // not needed any more
						}

						// width and height characteristics of selection, 1, 2, or 3 (for 3 or more)
						$xMax = min($rangeEnd[0] - $rangeStart[0] + 1, 3);
						$yMax = min($rangeEnd[1] - $rangeStart[1] + 1, 3);

						// loop through up to 3 x 3 = 9 regions
						for ($x = 1; $x <= $xMax; ++$x) {
							// start column index for region
							$colStart = ($x == 3) ? 
								PHPExcel_Cell::stringFromColumnIndex($rangeEnd[0])
									: PHPExcel_Cell::stringFromColumnIndex($rangeStart[0] + $x - 1);

							// end column index for region
							$colEnd = ($x == 1) ?
								PHPExcel_Cell::stringFromColumnIndex($rangeStart[0])
									: PHPExcel_Cell::stringFromColumnIndex($rangeEnd[0] - $xMax + $x);

							for ($y = 1; $y <= $yMax; ++$y) {

								// which edges are touching the region
								$edges = array();

								// are we at left edge
								if ($x == 1) {
									$edges[] = 'left';
								}

								// are we at right edge
								if ($x == $xMax) {
									$edges[] = 'right';
								}

								// are we at top edge?
								if ($y == 1) {
									$edges[] = 'top';
								}

								// are we at bottom edge?
								if ($y == $yMax) {
									$edges[] = 'bottom';
								}

								// start row index for region
								$rowStart = ($y == 3) ?
									$rangeEnd[1] : $rangeStart[1] + $y - 1;

								// end row index for region
								$rowEnd = ($y == 1) ?
									$rangeStart[1] : $rangeEnd[1] - $yMax + $y;

								// build range for region
								$range = $colStart . $rowStart . ':' . $colEnd . $rowEnd;
								
								// retrieve relevant style array for region
								$regionStyles = $pStyles;
								unset($regionStyles['borders']['inside']);

								// what are the inner edges of the region when looking at the selection
								$innerEdges = array_diff( array('top', 'right', 'bottom', 'left'), $edges );

								// inner edges that are not touching the region should take the 'inside' border properties if they have been set
								foreach ($innerEdges as $innerEdge) {
									switch ($innerEdge) {
										case 'top':
										case 'bottom':
											// should pick up 'horizontal' border property if set
											if (isset($pStyles['borders']['horizontal'])) {
												$regionStyles['borders'][$innerEdge] = $pStyles['borders']['horizontal'];
											} else {
												unset($regionStyles['borders'][$innerEdge]);
											}
											break;
										case 'left':
										case 'right':
											// should pick up 'vertical' border property if set
											if (isset($pStyles['borders']['vertical'])) {
												$regionStyles['borders'][$innerEdge] = $pStyles['borders']['vertical'];
											} else {
												unset($regionStyles['borders'][$innerEdge]);
											}
											break;
									}
								}

								// apply region style to region by calling applyFromArray() in simple mode
								$this->getActiveSheet()->getStyle($range)->applyFromArray($regionStyles, false);
							}
						}
						return;
					}

					// Simple mode
					
					// First loop through cells to find out which styles are affected by this operation
					$oldXfIndexes = array();
					for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
						for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
							$oldXfIndexes[$this->getActiveSheet()->getCellByColumnAndRow($col, $row)->getXfIndex()] = true;
						}
					}

					// clone each of the affected styles, apply the style arrray, and add the new styles to the workbook
					$workbook = $this->getActiveSheet()->getParent();
					foreach ($oldXfIndexes as $oldXfIndex => $dummy) {
						$style = $workbook->getCellXfByIndex($oldXfIndex);
						$newStyle = clone $style;
						$newStyle->applyFromArray($pStyles);
						
						if ($existingStyle = $workbook->getCellXfByHashCode($newStyle->getHashCode())) {
							// there is already such cell Xf in our collection
							$newXfIndexes[$oldXfIndex] = $existingStyle->getIndex();
						} else {
							// we don't have such a cell Xf, need to add
							$workbook->addCellXf($newStyle);
							$newXfIndexes[$oldXfIndex] = $newStyle->getIndex();
						}
					}
					
					// Loop through cells again and update the XF index
					for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
						for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
							$cell = $this->getActiveSheet()->getCellByColumnAndRow($col, $row);
							$oldXfIndex = $cell->getXfIndex();
							$cell->setXfIndex($newXfIndexes[$oldXfIndex]);
						}
					}

				} else {
					throw new Exception("Invalid style array passed.");
				}
				
			} else {
				if (array_key_exists('fill', $pStyles)) {
					$this->getFill()->applyFromArray($pStyles['fill']);
				}
				if (array_key_exists('font', $pStyles)) {
					$this->getFont()->applyFromArray($pStyles['font']);
				}
				if (array_key_exists('borders', $pStyles)) {
					$this->getBorders()->applyFromArray($pStyles['borders']);
				}
				if (array_key_exists('alignment', $pStyles)) {
					$this->getAlignment()->applyFromArray($pStyles['alignment']);
				}
				if (array_key_exists('numberformat', $pStyles)) {
					$this->getNumberFormat()->applyFromArray($pStyles['numberformat']);
				}
				if (array_key_exists('protection', $pStyles)) {
					$this->getProtection()->applyFromArray($pStyles['protection']);
				}
			}
		} else {
			throw new Exception("Invalid style array passed.");
		}
		return $this;
	}

    /**
     * Get Fill
     *
     * @return PHPExcel_Style_Fill
     */
    public function getFill() {
		return $this->_fill;
    }
    
    /**
     * Get Font
     *
     * @return PHPExcel_Style_Font
     */
    public function getFont() {
		return $this->_font;
    }

	/**
	 * Set font
	 *
	 * @param PHPExcel_Style_Font $font
	 * @return PHPExcel_Style
	 */
	public function setFont(PHPExcel_Style_Font $font)
	{
		$this->_font = $font;
		return $this;
	}

    /**
     * Get Borders
     *
     * @return PHPExcel_Style_Borders
     */
    public function getBorders() {
		return $this->_borders;
    }
    
    /**
     * Get Alignment
     *
     * @return PHPExcel_Style_Alignment
     */
    public function getAlignment() {
		return $this->_alignment;
    }
    
    /**
     * Get Number Format
     *
     * @return PHPExcel_Style_NumberFormat
     */
    public function getNumberFormat() {
		return $this->_numberFormat;
    }
    
    /**
     * Get Conditional Styles. Only used on supervisor.
     *
     * @return PHPExcel_Style_Conditional[]
     */
    public function getConditionalStyles() {
		return $this->getActiveSheet()->getConditionalStyles($this->getXActiveCell());
    }
       
    /**
     * Set Conditional Styles. Only used on supervisor.
     *
     * @param PHPExcel_Style_Conditional[]	$pValue	Array of condtional styles
     * @return PHPExcel_Style
     */
    public function setConditionalStyles($pValue = null) {
		if (is_array($pValue)) {
			foreach (PHPExcel_Cell::extractAllCellReferencesInRange($this->getXSelectedCells()) as $cellReference) {
				$this->getActiveSheet()->setConditionalStyles($cellReference, $pValue);
			}
		}
		return $this;
    }
    
    /**
     * Get Protection
     *
     * @return PHPExcel_Style_Protection
     */
    public function getProtection() {
		return $this->_protection;
    }
   
	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
		$hashConditionals = '';
		foreach ($this->_conditionalStyles as $conditional) {
			$hashConditionals .= $conditional->getHashCode();
		}
		
    	return md5(
    		  $this->getFill()->getHashCode()
    		. $this->getFont()->getHashCode()
    		. $this->getBorders()->getHashCode()
    		. $this->getAlignment()->getHashCode()
    		. $this->getNumberFormat()->getHashCode()
    		. $hashConditionals
    		. $this->getProtection()->getHashCode()
    		. __CLASS__
    	);
    }
    
    /**
     * Hash index
     *
     * @var string
     */
    private $_hashIndex;
    
	/**
	 * Get hash index
	 * 
	 * Note that this index may vary during script execution! Only reliable moment is
	 * while doing a write of a workbook and when changes are not allowed.
	 *
	 * @return string	Hash index
	 */
	public function getHashIndex() {
		return $this->_hashIndex;
	}
	
	/**
	 * Set hash index
	 * 
	 * Note that this index may vary during script execution! Only reliable moment is
	 * while doing a write of a workbook and when changes are not allowed.
	 *
	 * @param string	$value	Hash index
	 */
	public function setHashIndex($value) {
		$this->_hashIndex = $value;
	}

	/**
	 * Get own index in style collection
	 *
	 * @return int
	 */
	public function getIndex()
	{
		return $this->_index;
	}

	/**
	 * Set own index in style collection
	 *
	 * @param int $pValue
	 */
	public function setIndex($pValue)
	{
		$this->_index = $pValue;
	}

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
}
?>