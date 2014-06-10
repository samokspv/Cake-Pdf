<?php
App::uses('CakePdf', 'CakePdf.Pdf');
App::uses('WkHtmlToPdfEngine', 'CakePdf.Pdf/Engine');

/**
 * WkHtmlToPdfEngineTest class
 *
 * @package       CakePdf.Test.Case.Pdf.Engine
 */
class WkHtmlToPdfEngineTest extends CakeTestCase {
	
/**
 * {@inheritdoc}
 */
	public function setUp() {
		parent::setUp();
		$this->class = new ReflectionClass('WkHtmlToPdfEngine');
		$this->Pdf = new CakePdf(array(
			'engine'  => 'WkHtmlToPdf',
			'title'   => 'CakePdf rules',
			'options' => array(
				'quiet'    => false,
				'encoding' => 'ISO-8859-1'
			)
		));
	}

/**
 * Tests that the engine generates the right command
 *
 */
	public function testGetCommand() {
		$method = $this->class->getMethod('_getCommand');
		$method->setAccessible(true);

		$this->Pdf = new CakePdf(array(
			'engine'  => 'WkHtmlToPdf',
			'title'   => 'CakePdf rules',
			'options' => array(
				'quiet'    => false,
				'encoding' => 'ISO-8859-1'
			)
		));
		$bit = $this->getBinaryBitProvider()[0][0];
		$binaryPath = App::pluginPath('CakePdf') . 'Vendor' . DS . 'wkhtmltopdf' . DS . 'bin' . DS . $bit . '-bit' . DS . 'wkhtmltopdf';
		$result = $method->invokeArgs($this->Pdf->engine(), array());
		$expected = $binaryPath . " --print-media-type --orientation 'portrait' --page-size 'A4' --encoding 'ISO-8859-1' --title 'CakePdf rules' - -";
		$this->assertEquals($expected, $result);

		$this->Pdf = new CakePdf(array(
			'engine'  => 'WkHtmlToPdf',
			'options' => array(
				'boolean' => true,
				'string'  => 'value',
				'integer' => 42
			)
		));
		$result = $method->invokeArgs($this->Pdf->engine(), array());
		$expected = $binaryPath . " --quiet --print-media-type --orientation 'portrait' --page-size 'A4' --encoding 'UTF-8' --boolean --string 'value' --integer '42' - -";
		$this->assertEquals($expected, $result);
	}

/**
 * Tests get binary path
 *
 */
	public function testGetBinaryPath() {
		$method = $this->class->getMethod('_getBinaryPath');
		$method->setAccessible(true);

		$bit = $this->getBinaryBitProvider()[0][0];
		$expected = App::pluginPath('CakePdf') . 'Vendor' . DS . 'wkhtmltopdf' . DS . 'bin' . DS . $bit . '-bit' . DS . 'wkhtmltopdf';

		$result = $method->invokeArgs($this->Pdf->engine(), array());
		$this->assertEquals($expected, $result);
	}

/**
 * Tests get binary bit
 *
 * @param string $bit
 * @dataProvider getBinaryBitProvider
 */
	public function testGetBinaryBit($bit) {
		$method = $this->class->getMethod('_getBinaryBit');
		$method->setAccessible(true);
		
		$result = $method->invokeArgs($this->Pdf->engine(), array());
		$this->assertEquals($bit, $result);
	}

/**
 * Data provider for testGetBinaryBit
 * 
 * @return array
 */
	public function getBinaryBitProvider() {
		exec('arch', $output);
		switch ($output[0]) {
			case 'x86_64':
				$expected = '64';
				break;
			case 'i686':
				$expected = '32';
				break;
		}
		return array(
			array($expected)
		);
	}
}
