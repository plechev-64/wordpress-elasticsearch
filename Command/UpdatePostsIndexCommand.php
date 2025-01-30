<?php

namespace Gosweb\Module\ElasticSearch\Command;

use Doctrine\ORM\EntityManagerInterface;
use Gosweb\Core\Entity\Post;
use Gosweb\Core\Helper\Paginator;
use Gosweb\Core\Transformer\TransformerManager;
use Gosweb\Module\ElasticSearch\DTO\Model\PostDTO;
use Gosweb\Module\ElasticSearch\ElasticService;
use Gosweb\Module\ElasticSearch\Index\PostIndex;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'elastic:posts:update', description: 'Обновление индекса публикаций в ElasticSearch')]
class UpdatePostsIndexCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TransformerManager $transformerManager,
        private readonly ElasticService $service
    )
    {
        parent::__construct();
    }

    #[NoReturn] protected function execute(
        InputInterface  $input,
        OutputInterface $output
    ): int
    {

        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);

        $postTypes = $this->service->getSupportPostTypes();

        $page = 1;
        $limit = 100;
        $paginator = new Paginator();
        $query = $this->entityManager
            ->getRepository(Post::class)
            ->createQueryBuilder('p')
            ->where('p.postType IN (\''.implode('\',\'', $postTypes).'\')');
        $paginator->paginate($query, $page, $limit);

        if ($paginator->getTotal()) {

            $progressBar = new ProgressBar($output, $paginator->getTotal());
            $progressBar->start();

            while (true) {

                /** @var Post $post */
                foreach ($paginator->getItems() as $post) {
                    $progressBar->advance();

                    /** @var PostDTO $postIndex */
                    $post = $this->transformerManager->transform($post, PostDTO::class);

                    $this->service->indexPost(
                        new PostIndex($post)
                    );
                }

                if ($page === $paginator->getLastPage()) {
                    break;
                }

                $page++;
                $paginator->paginate($query, $page, $limit);

            }

            $progressBar->finish();

            $io->success('Индекс публикаций успешно обновлен');

        } else {
            $io->error('Публикации не были получены из БД');
        }

        return 0;
    }

}