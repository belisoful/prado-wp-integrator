<?php

class WPTest extends BasicPage
{
	public function onLoad($param) {
		
		parent::onLoad($param);
		
		if(!$this->getIsPostBack()) {
		}
	}
}

