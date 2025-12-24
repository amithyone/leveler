<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Page;

class UpdateHeroSlidesContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hero-slides:update-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update hero slides with titles and subtitles for slides that are missing them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $page = Page::where('slug', 'home')->first();

        if (!$page) {
            $this->error('Home page not found!');
            return 1;
        }

        $heroSlides = $page->hero_slides ?? [];

        if (empty($heroSlides)) {
            $this->error('No hero slides found!');
            return 1;
        }

        $updated = false;

        foreach ($heroSlides as $index => &$slide) {
            // Skip if it already has a title (slide 0)
            if (!empty($slide['title'])) {
                continue;
            }

            // Generate content for slides without titles
            if ($index === 1) {
                $slide['title'] = 'Empowering Businesses<br>Through Strategic Growth';
                $slide['subtitle'] = 'We deliver value-driven solutions that support your business growth aspirations';
                $updated = true;
                $this->info("Updated slide {$index} with title: Empowering Businesses Through Strategic Growth");
            } elseif ($index === 2) {
                $slide['title'] = 'Transform Your Workforce<br>With Expert Training';
                $slide['subtitle'] = 'Professional development programs designed to accelerate your team\'s success';
                $updated = true;
                $this->info("Updated slide {$index} with title: Transform Your Workforce With Expert Training");
            }
        }

        if ($updated) {
            $page->hero_slides = $heroSlides;
            $page->save();
            $this->info('Hero slides updated successfully!');
        } else {
            $this->info('No slides needed updating.');
        }

        return 0;
    }
}
