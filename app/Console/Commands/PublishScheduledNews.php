<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use Carbon\Carbon;

class PublishScheduledNews extends Command
{
    protected $signature = 'news:publish-scheduled';
    protected $description = 'Publica automáticamente las noticias cuya fecha de programación ya se cumplió.';

    public function handle()
    {
        $newsToPublish = News::where('status', 'programado')
                             ->whereNotNull('scheduled_at')
                             ->where('scheduled_at', '<=', Carbon::now())
                             ->get();

        foreach ($newsToPublish as $news) {
            $news->update([
                'status' => 'publicado',
                'scheduled_at' => null, 
            ]);
            $this->info("Noticia publicada automáticamente: ID {$news->id} - {$news->title}");
        }

        $this->info('Revisión de noticias programadas finalizada con éxito.');
    }
}