<?php


class WPPostContent extends TTemplateControl
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
	 * Loads the template associated with this control class.
	 * @return \Prado\Web\UI\ITemplate the parsed template structure
	 */
	protected function loadTemplate()
	{
		$module = $this->getPluginModule();
		Prado::trace("Loading WordPress template " . get_class($this), '\Prado\Web\UI\TTemplateControl');
		
		$post = $module->getWPPost($this->getPostId());
		$template = null;
		
		//post cannot be password protected, must be published, 
		if($post->getStatus() == 'publish' && !$post->getHasPassword() && ($post->getType() == 'post' || $post->getType() == 'page'))
			$template = new WPTemplate($post->getContent(), null);
		
		return $template;
	}
	
	public function onLoad($param)
	{
		
	}
}
