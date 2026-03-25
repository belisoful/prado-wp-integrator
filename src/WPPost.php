<?php

namespace PradoWpIntegrator;

class WPPost extends Prado\TApplicationComponent
{
	protected $_postData;
	
	protected $_metaData;
	
	public function __construct($data, $meta)
	{
		$this->_postData = $data;
		$this->_metaData = $meta;
		parent::__construct();
	}
	
	public function getId()
	{
		return $this->_postData['ID'];
	}
	
	public function getAuthor()
	{
		return $this->_postData['ID'];
	}
	
	public function getDate()
	{
		return $this->_postData['post_date'];
	}
	
	public function getDateGMT()
	{
		return $this->_postData['post_date_gmt'];
	}
	
	public function getContent()
	{
		return $this->_postData['post_content'];
	}
	
	public function getTitle()
	{
		return $this->_postData['post_title'];
	}
	
	public function getExcerpt()
	{
		return $this->_postData['post_excerpt'];
	}
	
	public function getStatus()
	{
		return $this->_postData['post_status'];
	}
	
	public function getCommentStatus()
	{
		return $this->_postData['comment_status'];
	}
	
	public function getPingStatus()
	{
		return $this->_postData['ping_status'];
	}
	
	public function getHasPassword()
	{
		return $this->_postData['post_password'] != '';
	}
	
	public function getCheckPassword($password)
	{
		return $this->_postData['post_password'] == $password;
	}
	
	public function getName()
	{
		return $this->_postData['post_name'];
	}
	
	public function getModified()
	{
		return $this->_postData['post_modified'];
	}
	
	public function getModifiedGMT()
	{
		return $this->_postData['post_modified_gmt'];
	}
	
	public function getParent()
	{
		return $this->_postData['post_parent'];
	}
	
	public function getGUID()
	{
		return $this->_postData['guid'];
	}
	
	public function getType()
	{
		return $this->_postData['post_type'];
	}
	
	public function getMimeType()
	{
		return $this->_postData['post_mime_type'];
	}
	
	public function getCommentCount()
	{
		return $this->_postData['comment_count'];
	}
	
	public function getMeta($key)
	{
		return $this->_metaData[$key] ?? null;
	}
	
	
}