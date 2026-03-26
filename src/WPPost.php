<?php

/**
 * WPPost class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator;

require 'composer.php';

/**
 * WPPost class
 *
 * WordPress post class for integration with PRADO.
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @since 0.0.1
 */
class WPPost extends \Prado\TApplicationComponent
{
	/**
	 * @var array The post data
	 */
	protected $_postData;

	/**
	 * @var array The post meta data
	 */
	protected $_metaData;

	/**
	 * Constructor.
	 * 
	 * @param array $data The post data
	 * @param array $meta The post meta data
	 * @return void
	 */
	public function __construct($data, $meta)
	{
		$this->_postData = $data;
		$this->_metaData = $meta;
		parent::__construct();
	}

	/**
	 * Gets the post ID.
	 * 
	 * @return int The post ID
	 */
	public function getId()
	{
		return $this->_postData['ID'];
	}

	/**
	 * Gets the post author ID.
	 * 
	 * @return int The post author ID
	 */
	public function getAuthor()
	{
		return $this->_postData['ID'];
	}

	/**
	 * Gets the post date.
	 * 
	 * @return string The post date
	 */
	public function getDate()
	{
		return $this->_postData['post_date'];
	}

	/**
	 * Gets the post date in GMT.
	 * 
	 * @return string The post date in GMT
	 */
	public function getDateGMT()
	{
		return $this->_postData['post_date_gmt'];
	}

	/**
	 * Gets the post content.
	 * 
	 * @return string The post content
	 */
	public function getContent()
	{
		return $this->_postData['post_content'];
	}

	/**
	 * Gets the post title.
	 * 
	 * @return string The post title
	 */
	public function getTitle()
	{
		return $this->_postData['post_title'];
	}

	/**
	 * Gets the post excerpt.
	 * 
	 * @return string The post excerpt
	 */
	public function getExcerpt()
	{
		return $this->_postData['post_excerpt'];
	}

	/**
	 * Gets the post status.
	 * 
	 * @return string The post status
	 */
	public function getStatus()
	{
		return $this->_postData['post_status'];
	}

	/**
	 * Gets the comment status.
	 * 
	 * @return string The comment status
	 */
	public function getCommentStatus()
	{
		return $this->_postData['comment_status'];
	}

	/**
	 * Gets the ping status.
	 * 
	 * @return string The ping status
	 */
	public function getPingStatus()
	{
		return $this->_postData['ping_status'];
	}

	/**
	 * Checks if the post has a password.
	 * 
	 * @return bool Whether the post has a password
	 */
	public function getHasPassword()
	{
		return $this->_postData['post_password'] != '';
	}

	/**
	 * Checks if the provided password matches the post password.
	 * 
	 * @param string $password The password to check
	 * @return bool Whether the password matches
	 */
	public function getCheckPassword($password)
	{
		return $this->_postData['post_password'] == $password;
	}

	/**
	 * Gets the post name.
	 * 
	 * @return string The post name
	 */
	public function getName()
	{
		return $this->_postData['post_name'];
	}

	/**
	 * Gets the post modified date.
	 * 
	 * @return string The post modified date
	 */
	public function getModified()
	{
		return $this->_postData['post_modified'];
	}

	/**
	 * Gets the post modified date in GMT.
	 * 
	 * @return string The post modified date in GMT
	 */
	public function getModifiedGMT()
	{
		return $this->_postData['post_modified_gmt'];
	}

	/**
	 * Gets the post parent ID.
	 * 
	 * @return int The post parent ID
	 */
	public function getParent()
	{
		return $this->_postData['post_parent'];
	}

	/**
	 * Gets the post GUID.
	 * 
	 * @return string The post GUID
	 */
	public function getGUID()
	{
		return $this->_postData['guid'];
	}

	/**
	 * Gets the post type.
	 * 
	 * @return string The post type
	 */
	public function getType()
	{
		return $this->_postData['post_type'];
	}

	/**
	 * Gets the post mime type.
	 * 
	 * @return string The post mime type
	 */
	public function getMimeType()
	{
		return $this->_postData['post_mime_type'];
	}

	/**
	 * Gets the comment count.
	 * 
	 * @return int The comment count
	 */
	public function getCommentCount()
	{
		return $this->_postData['comment_count'];
	}

	/**
	 * Gets a meta value by key.
	 * 
	 * @param string $key The meta key
	 * @return mixed The meta value or null if not found
	 */
	public function getMeta($key)
	{
		return $this->_metaData[$key] ?? null;
	}
}
