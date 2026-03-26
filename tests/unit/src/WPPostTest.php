<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use PradoWpIntegrator\WPPost;

/**
 * Test class for WPPost
 */
class WPPostTest extends TestCase
{
    public function testConstructor()
    {
        $postData = [
            'ID' => 1,
            'post_title' => 'Test Post',
            'post_content' => 'This is test content',
            'post_date' => '2023-01-01 00:00:00'
        ];
        $metaData = [
            'custom_field' => 'custom_value'
        ];
        $post = new WPPost($postData, $metaData);
        
        $this->assertInstanceOf(WPPost::class, $post);
        $this->assertEquals(1, $post->getId());
        $this->assertEquals('Test Post', $post->getTitle());
        $this->assertEquals('This is test content', $post->getContent());
    }

    public function testGetId()
    {
        $postData = ['ID' => 42];
        $post = new WPPost($postData, []);
        $this->assertEquals(42, $post->getId());
    }

    public function testGetAuthor()
    {
        $postData = ['ID' => 42];
        $post = new WPPost($postData, []);
        $this->assertEquals(42, $post->getAuthor());
    }

    public function testGetDate()
    {
        $postData = ['post_date' => '2023-01-01 12:00:00'];
        $post = new WPPost($postData, []);
        $this->assertEquals('2023-01-01 12:00:00', $post->getDate());
    }

    public function testGetDateGMT()
    {
        $postData = ['post_date_gmt' => '2023-01-01 12:00:00'];
        $post = new WPPost($postData, []);
        $this->assertEquals('2023-01-01 12:00:00', $post->getDateGMT());
    }

    public function testGetContent()
    {
        $postData = ['post_content' => 'Test content'];
        $post = new WPPost($postData, []);
        $this->assertEquals('Test content', $post->getContent());
    }

    public function testGetTitle()
    {
        $postData = ['post_title' => 'Test Title'];
        $post = new WPPost($postData, []);
        $this->assertEquals('Test Title', $post->getTitle());
    }

    public function testGetExcerpt()
    {
        $postData = ['post_excerpt' => 'Test excerpt'];
        $post = new WPPost($postData, []);
        $this->assertEquals('Test excerpt', $post->getExcerpt());
    }

    public function testGetStatus()
    {
        $postData = ['post_status' => 'publish'];
        $post = new WPPost($postData, []);
        $this->assertEquals('publish', $post->getStatus());
    }

    public function testGetCommentStatus()
    {
        $postData = ['comment_status' => 'open'];
        $post = new WPPost($postData, []);
        $this->assertEquals('open', $post->getCommentStatus());
    }

    public function testGetPingStatus()
    {
        $postData = ['ping_status' => 'open'];
        $post = new WPPost($postData, []);
        $this->assertEquals('open', $post->getPingStatus());
    }

    public function testGetHasPassword()
    {
        $postData = ['post_password' => ''];
        $post = new WPPost($postData, []);
        $this->assertFalse($post->getHasPassword());
        
        $postData = ['post_password' => 'secret'];
        $post = new WPPost($postData, []);
        $this->assertTrue($post->getHasPassword());
    }

    public function testGetCheckPassword()
    {
        $postData = ['post_password' => 'secret'];
        $post = new WPPost($postData, []);
        $this->assertTrue($post->getCheckPassword('secret'));
        $this->assertFalse($post->getCheckPassword('wrong'));
    }

    public function testGetName()
    {
        $postData = ['post_name' => 'test-post'];
        $post = new WPPost($postData, []);
        $this->assertEquals('test-post', $post->getName());
    }

    public function testGetModified()
    {
        $postData = ['post_modified' => '2023-01-01 12:00:00'];
        $post = new WPPost($postData, []);
        $this->assertEquals('2023-01-01 12:00:00', $post->getModified());
    }

    public function testGetModifiedGMT()
    {
        $postData = ['post_modified_gmt' => '2023-01-01 12:00:00'];
        $post = new WPPost($postData, []);
        $this->assertEquals('2023-01-01 12:00:00', $post->getModifiedGMT());
    }

    public function testGetParent()
    {
        $postData = ['post_parent' => 10];
        $post = new WPPost($postData, []);
        $this->assertEquals(10, $post->getParent());
    }

    public function testGetGUID()
    {
        $postData = ['guid' => 'http://example.com/test'];
        $post = new WPPost($postData, []);
        $this->assertEquals('http://example.com/test', $post->getGUID());
    }

    public function testGetType()
    {
        $postData = ['post_type' => 'post'];
        $post = new WPPost($postData, []);
        $this->assertEquals('post', $post->getType());
    }

    public function testGetMimeType()
    {
        $postData = ['post_mime_type' => 'text/html'];
        $post = new WPPost($postData, []);
        $this->assertEquals('text/html', $post->getMimeType());
    }

    public function testGetCommentCount()
    {
        $postData = ['comment_count' => 5];
        $post = new WPPost($postData, []);
        $this->assertEquals(5, $post->getCommentCount());
    }

    public function testGetMeta()
    {
        $postData = [];
        $metaData = ['custom_field' => 'custom_value'];
        $post = new WPPost($postData, $metaData);
        $this->assertEquals('custom_value', $post->getMeta('custom_field'));
        $this->assertNull($post->getMeta('nonexistent_field'));
    }
}