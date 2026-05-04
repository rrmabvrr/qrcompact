<?php

namespace App\Console\Commands;

use App\Models\Link;
use App\Services\SafeBrowsingService;
use Illuminate\Console\Command;

class ScanLinksCommand extends Command
{
    protected $signature = 'links:scan
                            {--delete : Deletar automaticamente os links perigosos}
                            {--chunk=100 : Quantidade de links processados por lote}';

    protected $description = 'Escaneia todos os links do banco de dados usando a Google Safe Browsing API';

    public function handle(SafeBrowsingService $safeBrowsing): int
    {
        $shouldDelete = (bool) $this->option('delete');
        $chunkSize = max(1, (int) $this->option('chunk'));

        $total = Link::query()->count();

        if ($total === 0) {
            $this->info('Nenhum link encontrado no banco de dados.');
            return Command::SUCCESS;
        }

        $this->info("Iniciando escaneamento de {$total} link(s)...");
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $dangerousIds = [];

        Link::query()->orderBy('id')->chunk($chunkSize, function ($links) use ($safeBrowsing, $bar, &$dangerousIds) {
            foreach ($links as $link) {
                if (! $safeBrowsing->isSafe($link->original_url)) {
                    $dangerousIds[$link->id] = "[{$link->slug}] {$link->original_url}";
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $count = count($dangerousIds);

        if ($count === 0) {
            $this->info('Escaneamento concluido. Nenhum link perigoso encontrado.');
            return Command::SUCCESS;
        }

        $this->error("Encontrado(s) {$count} link(s) perigoso(s):");
        foreach ($dangerousIds as $description) {
            $this->line("  {$description}");
        }

        $this->newLine();

        if ($shouldDelete) {
            Link::query()->whereIn('id', array_keys($dangerousIds))->delete();
            $this->warn("{$count} link(s) removido(s) do banco de dados.");
        } else {
            $this->line('Use <comment>--delete</comment> para remover os links perigosos automaticamente.');
        }

        return Command::SUCCESS;
    }
}
