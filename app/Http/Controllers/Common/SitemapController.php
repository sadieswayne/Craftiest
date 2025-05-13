<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Spatie\Sitemap\SitemapGenerator;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = SitemapGenerator::create(config('app.url'))->writeToFile(public_path('sitemap.xml'));
        // add sitemap.xml to the robots.txt file
        $robots = public_path('robots.txt');
        $robot = file_get_contents($robots);
        if (strpos($robot, 'sitemap.xml') === false) {
            $robot .= "\nSitemap: " . url('sitemap.xml');
            file_put_contents($robots, $robot);
        } else {
            $robot = str_replace('Sitemap: ' . url('sitemap.xml'), '', $robot);
            $robot .= "\nSitemap: " . url('sitemap.xml');
            file_put_contents($robots, $robot);
        }

        return response()->file(public_path('sitemap.xml'));
    }
}
