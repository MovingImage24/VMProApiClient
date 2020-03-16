<?php

namespace MovingImage\Test\Client\VMPro\ApiClient\Methods;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Entity\Attachment;
use MovingImage\Test\Fixtures\Fixture;
use MovingImage\TestCase\ApiClientTestCase;

class GetChannelAttachmentsTest extends ApiClientTestCase
{
    public function testSearchChannels()
    {
        $httpClient = $this->createMockGuzzleClient(200, [], Fixture::getApiResponse('getChannelAttachments'));

        $client = $this->createApiClient($httpClient, $this->createSerializer());
        /** @var ArrayCollection<Attachment> $attachments */
        $attachments = $client->getChannelAttachments(1, 1);

        $this->assertCount(2, $attachments);

        $attachment1 = $attachments->current();
        $this->assertInstanceOf(Attachment::class, $attachment1);
        $this->assertSame('attachment1.jpg', $attachment1->getFileName());
        $this->assertSame('http://example.org/attachment1.jpg', $attachment1->getDownloadUrl());

        $attachment2 = $attachments->next();
        $this->assertInstanceOf(Attachment::class, $attachment2);
        $this->assertSame('attachment2.jpg', $attachment2->getFileName());
        $this->assertSame('http://example.org/attachment2.jpg', $attachment2->getDownloadUrl());
    }
}
