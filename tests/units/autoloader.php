<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../runners/autorunner.php');

/** @isolation off */
class autoloader extends atoum\test
{
	public function testGetPath()
	{
		$this->assert->variable(atoum\autoloader::getPath(uniqid()))->isNull();
		$this->assert->variable(atoum\autoloader::getPath('\mageekguy\atoum'))->isNull();
	}
}

?>
