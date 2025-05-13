<?php

namespace App\Http\Middleware;

use App\Models\UserOpenai;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ViewSharedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {

            if (
                ! Cache::has('total_words_' . Auth::id())
                or ! Cache::has('total_documents_' . Auth::id())
                or ! Cache::has('total_text_documents_' . Auth::id())
                or ! Cache::has('total_image_documents_' . Auth::id())
            ) {
                $total_documents_finder = UserOpenai::where('user_id', Auth::id())->get();
                $total_words = UserOpenai::where('user_id', Auth::id())->sum('credits');
                Cache::put('total_words_' . Auth::id(), $total_words, now()->addMinutes(360));
                $total_documents = count($total_documents_finder);
                Cache::put('total_documents_' . Auth::id(), $total_documents, now()->addMinutes(360));
                $total_text_documents = count($total_documents_finder->where('credits', '!=', 1));
                Cache::put('total_text_documents_' . Auth::id(), $total_text_documents, now()->addMinutes(360));
                $total_image_documents = count($total_documents_finder->where('credits', '==', 1));
                Cache::put('total_image_documents_' . Auth::id(), $total_image_documents, now()->addMinutes(360));
            }
            $total_words = Cache::get('total_words_' . Auth::id()) ?? 0;
            View::share('total_words', $total_words);
            $total_documents = Cache::get('total_documents_' . Auth::id()) ?? 0;
            View::share('total_documents', $total_documents);
            $total_text_documents = Cache::get('total_text_documents_' . Auth::id()) ?? 0;
            View::share('total_text_documents', $total_text_documents);
            $total_image_documents = Cache::get('total_image_documents_' . Auth::id()) ?? 0;
            View::share('total_image_documents', $total_image_documents);
        }

        return $next($request);
    }
}
