<?php

namespace App\Command;

use App\Service\TelegramApiManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:telegram:setup',
    description: 'Setup telegram webhook',
)]
class TelegramSetupCommand extends Command
{
    
    /**
     * @param TelegramApiManager $telegramApiManager
     * @param LoggerInterface    $logger
     * @param string|null        $name
     */
    public function __construct(
        private TelegramApiManager $telegramApiManager,
        private LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        try{
            $result = $this->telegramApiManager->setupWebhook();
        } catch (Throwable $e){
            $io->error($e->getMessage());
            $this->logger->error('Executing app:telegram:setup error:' . $e->getMessage() . '. Trace: ' . $e->getTraceAsString());
            
            return Command::FAILURE;
        }
        
        $io->success($result);
        
        return Command::SUCCESS;
    }
}
