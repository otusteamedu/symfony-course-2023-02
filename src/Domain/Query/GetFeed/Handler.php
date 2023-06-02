<?php

namespace App\Domain\Query\GetFeed;

use Doctrine\ORM\EntityManagerInterface;
use FeedBundle\Entity\Feed;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class Handler implements MessageHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(GetFeedQuery $query): GetFeedQueryResult
    {
        $feedRepository = $this->entityManager->getRepository(Feed::class);
        $feed = $feedRepository->findOneBy(['readerId' => $query->getUserId()]);
        if ($feed === null) {
            $tweets = [];
        } else {
            $tweets = array_slice($feed->getTweets(), -$query->getCount());
        }

        return new GetFeedQueryResult($tweets);
    }
}
