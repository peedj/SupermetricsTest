<?php
namespace ApiConnect;

use ApiConnect\collections\StatsCollection;
use ApiConnect\connectors\ApiCall;
use ApiConnect\repositories\PostRepository;

class ApiConnect
{
    /**
     * @var PostRepository
     */
    private PostRepository $repository;
    /**
     * @var StatsCollection
     */
    private StatsCollection $stats;

    public function __construct()
    {
        $this->repository = new PostRepository(new ApiCall());
        $this->stats = new StatsCollection();
    }

    /**
     * @param $pagesToLoad
     * @return array
     */
    public function getPostsStats($pagesToLoad):array {
        for ($page = 1; $page <= $pagesToLoad; $page++) {
            $this->stats->appendPostsData($this->repository->loadPageData($page));
        }

        return $this->stats->getInfo();
    }

}