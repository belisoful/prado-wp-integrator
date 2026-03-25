<?php


class WPPostContentTitle extends TLabel
{
	public function getPostId()
	{
		return $this->getViewState('PostId', '');
	}
	
	public function setPostId($value)
	{
		$this->setViewState('PostId', $value, '');
	}
	
	
	/**
   * writes the difference in time that the application started to the moment of this method call.
   */
	public function renderContents($writer)
	{
		$module = $this->getPluginModule();
		Prado::trace("Loading WordPress template " . get_class($this), '\Prado\Web\UI\TTemplateControl');
		
		$post = $module->getWPPost($this->getPostId());
		
		//post cannot be password protected, must be published, 
		if($post->getStatus() == 'publish' && !$post->getHasPassword() && ($post->getType() == 'post' || $post->getType() == 'page'))
			$writer->write($post->getTitle());
		
	}

	
}
