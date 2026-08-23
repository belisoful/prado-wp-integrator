<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use Prado\TComponent;
use PradoWpIntegrator\WPPost;

/**
 * Test class for WPPost.
 */
class WPPostTest extends TestCase
{
    /**
     * A complete row as the module reads it out of wp_posts.
     *
     * @return array<string, mixed> the post row
     */
    private function postData(): array
    {
        return [
            'ID' => 42,
            'post_author' => 7,
            'post_date' => '2023-01-01 12:00:00',
            'post_date_gmt' => '2023-01-01 20:00:00',
            'post_content' => 'This is test content',
            'post_title' => 'Test Post',
            'post_excerpt' => 'Test excerpt',
            'post_status' => 'publish',
            'comment_status' => 'open',
            'ping_status' => 'closed',
            'post_password' => '',
            'post_name' => 'test-post',
            'post_modified' => '2023-02-02 12:00:00',
            'post_modified_gmt' => '2023-02-02 20:00:00',
            'post_parent' => 3,
            'guid' => 'http://example.com/?p=42',
            'post_type' => 'post',
            'post_mime_type' => 'text/html',
            'comment_count' => 5,
        ];
    }

    /**
     * @param array $overrides columns to override
     * @param array $meta the post meta
     * @return WPPost the post under test
     */
    private function makePost(array $overrides = [], array $meta = []): WPPost
    {
        return new WPPost($overrides + $this->postData(), $meta);
    }

    public function testConstructor()
    {
        $post = $this->makePost([], ['custom_field' => 'custom_value']);

        $this->assertInstanceOf(WPPost::class, $post);
        $this->assertInstanceOf(TComponent::class, $post);
    }

    /**
     * Every column-backed accessor, as (getter, expected value).
     *
     * @return array<string, array{0: string, 1: mixed}> the accessor cases
     */
    public static function accessorProvider(): array
    {
        return [
            'Id' => ['getId', 42],
            'Author' => ['getAuthor', 7],
            'Date' => ['getDate', '2023-01-01 12:00:00'],
            'DateGMT' => ['getDateGMT', '2023-01-01 20:00:00'],
            'Content' => ['getContent', 'This is test content'],
            'Title' => ['getTitle', 'Test Post'],
            'Excerpt' => ['getExcerpt', 'Test excerpt'],
            'Status' => ['getStatus', 'publish'],
            'CommentStatus' => ['getCommentStatus', 'open'],
            'PingStatus' => ['getPingStatus', 'closed'],
            'Name' => ['getName', 'test-post'],
            'Modified' => ['getModified', '2023-02-02 12:00:00'],
            'ModifiedGMT' => ['getModifiedGMT', '2023-02-02 20:00:00'],
            'Parent' => ['getParent', 3],
            'GUID' => ['getGUID', 'http://example.com/?p=42'],
            'Type' => ['getType', 'post'],
            'MimeType' => ['getMimeType', 'text/html'],
            'CommentCount' => ['getCommentCount', 5],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('accessorProvider')]
    public function testAccessorsReadTheirColumn(string $getter, $expected)
    {
        $this->assertSame($expected, $this->makePost()->{$getter}());
    }

    public function testAuthorIsThePostAuthorNotThePostId()
    {
        $post = $this->makePost(['ID' => 42, 'post_author' => 7]);

        $this->assertSame(7, $post->getAuthor());
    }

    public function testHasPasswordIsFalseForAnEmptyPassword()
    {
        $this->assertFalse($this->makePost(['post_password' => ''])->getHasPassword());
    }

    public function testHasPasswordIsTrueForAPassword()
    {
        $this->assertTrue($this->makePost(['post_password' => 'secret'])->getHasPassword());
    }

    public function testCheckPasswordAcceptsTheRightPassword()
    {
        $this->assertTrue($this->makePost(['post_password' => 'secret'])->getCheckPassword('secret'));
    }

    public function testCheckPasswordRejectsTheWrongPassword()
    {
        $this->assertFalse($this->makePost(['post_password' => 'secret'])->getCheckPassword('wrong'));
    }

    public function testGetMetaReturnsTheValue()
    {
        $post = $this->makePost([], ['custom_field' => 'custom_value']);

        $this->assertSame('custom_value', $post->getMeta('custom_field'));
    }

    public function testGetMetaReturnsNullForAnUnknownKey()
    {
        $this->assertNull($this->makePost([], [])->getMeta('no_such_key'));
    }
}
