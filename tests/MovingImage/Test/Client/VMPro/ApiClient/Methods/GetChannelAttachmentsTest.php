<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use MovingImage\Client\VMPro\Entity\Attachment;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class GetChannelAttachmentsTest extends ApiClientTestCase
{
    /**
     * @covers \AbstractApiClient::getChannelAttachments()
     */
    public function testSearchChannels()
    {
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('getChannelAttachments'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        /** @var Attachment[] $attachments */
        $attachments = $client->getChannelAttachments(1, 1);
        $this->assertCount(2, $attachments);
        $attachment1 = current($attachments);
        $this->assertInstanceOf(Attachment::class, $attachment1);
        $this->assertSame('attachment1.jpg', $attachment1->getFileName());
        $this->assertSame('http://example.org/attachment1.jpg', $attachment1->getDownloadUrl());

        $attachment2 = next($attachments);
        $this->assertInstanceOf(Attachment::class, $attachment1);
        $this->assertSame('attachment2.jpg', $attachment2->getFileName());
        $this->assertSame('http://example.org/attachment2.jpg', $attachment2->getDownloadUrl());
    }
}
