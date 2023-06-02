<?php

namespace App\Domain\Query\GetFeed;

use FeedBundle\Service\FeedService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class Handler implements MessageHandlerInterface
{
    public function __construct(
        private readonly FeedService $feedService,
    ) {
    }

    public function __invoke(GetFeedQuery $query): GetFeedQueryResult
    {
        return new GetFeedQueryResult(
            $this->feedService->getFeed($query->getUserId(), $query->getCount())
        );
    }
}
