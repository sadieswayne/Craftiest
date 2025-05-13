<?php

namespace App\Http\Controllers\Api;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Engine\Services\FalAIService;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Enums\BedrockEngine;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OpenAIGenerator;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\Usage;
use App\Models\User;
use App\Models\UserOpenai;
use App\Services\Bedrock\BedrockRuntimeService;
use App\Services\Youtube\YoutubeTranscriptService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JsonException;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;
use Random\RandomException;
use RuntimeException;
use Throwable;

class AIController extends Controller
{
    protected $client;

    protected $settings;

    protected $settings_two;

    public const STORAGE_S3 = 's3';

    public const CLOUDFLARE_R2 = 'r2';

    public const LOADING_GIF = '/themes/default/assets/img/loading.svg';

    public function __construct(BedrockRuntimeService $bedrockService)
    {
        $this->bedrockService = $bedrockService;
        $this->middleware(function (Request $request, $next) {
            ApiHelper::setOpenAiKey();

            return $next($request);
        });

        // Settings
        $this->settings = Setting::getCache();
        $this->settings_two = SettingTwo::getCache();
        set_time_limit(120);
    }

    /**
     * @OA\Post(
     *      path="/api/aiwriter/generate",
     *      operationId="buildOutputApi",
     *      tags={"AI Writer"},
     *      security={{ "passport": {} }},
     *      summary="Build AI-generated content please see the function there is custom inputs foreach template",
     *      description="Build AI-generated content based on user input.",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *
     *              @OA\Schema(
     *                  type="object",
     *
     *                  @OA\Property(
     *                      property="post_type",
     *                      type="string",
     *                      enum={"post_title_generator", "article_generator", "summarize_text", "product_description", "product_name", "testimonial_review", "problem_agitate_solution", "blog_section", "blog_post_ideas", "blog_intros", "blog_conclusion", "facebook_ads", "youtube_video_description", "youtube_video_title", "youtube_video_tag", "instagram_captions", "instagram_hashtag", "social_media_post_tweet", "social_media_post_business", "facebook_headlines", "google_ads_headlines", "google_ads_description", "content_rewrite", "paragraph_generator", "pros_cons", "meta_description", "faq_generator", "email_generator", "email_answer_generator", "newsletter_generator", "grammar_correction", "tldr_summarization", "ai_image_generator", "ai_code_generator"},
     *                      example="summarize_text"
     *                  ),
     *                  @OA\Property(
     *                      property="maximum_length",
     *                      description="Maximum length of the generated text",
     *                      type="integer",
     *                      example=200
     *                  ),
     *                  @OA\Property(
     *                      property="number_of_results",
     *                      description="Number of summary results to generate",
     *                      type="integer",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="creativity",
     *                      description="Creativity level for the generated content (0 to 1)",
     *                      type="number",
     *                      example=0.75
     *                  ),
     *                  @OA\Property(
     *                      property="tone_of_voice",
     *                      description="Tone of voice for the generated content",
     *                      type="string",
     *                      example="Professional"
     *                  ),
     *                  @OA\Property(
     *                      property="language",
     *                      description="Language code for the input text",
     *                      type="string",
     *                      example="en-US"
     *                  ),
     *                  @OA\Property(
     *                      property="text_to_summary",
     *                      description="Text to be summarized",
     *                      type="string",
     *                      example="dfg"
     *                  )
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="message_id", type="integer", description="ID of the generated content message"),
     *              @OA\Property(property="workbook", type="object", description="Details about the generated content workbook"),
     *              @OA\Property(property="creativity", type="number", description="Creativity factor (between 0 and 1)"),
     *              @OA\Property(property="maximum_length", type="integer", description="Maximum length of the generated content"),
     *              @OA\Property(property="number_of_results", type="integer", description="Number of desired results"),
     *              @OA\Property(property="inputPrompt", type="string", description="User input prompt"),
     *              @OA\Property(property="generated_content", type="string", description="Generated content"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="errors", type="string", description="Error message"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server error",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="error", type="string", description="Internal Server Error"),
     *          ),
     *      ),
     * )
     */
    public function buildOutput(Request $request)
    {
        $user = $request->user();
        $image_generator = $request->image_generator;
        $post_type = $request->post_type;
        // SETTINGS
        $number_of_results = $request->number_of_results;
        $maximum_length = $request->maximum_length;
        $creativity = $request->creativity;
        $number_of_results = $request->number_of_results ?? 1;
        $maximum_length = $request->maximum_length ?? $this->settings->openai_max_input_length;
        $creativity = $request->creativity ?? $this->settings->openai_default_creativity;

        $language = $request->language;

        try {
            $language = explode('-', $language);
            if (count($language) > 1 && config('localization.supportedLocales.' . $language[0] . '.name')) {
                $ek = $language[1];
                $language = config('localization.supportedLocales.' . $language[0] . '.name');
                $language .= " $ek";
            } else {
                $language = $request->language;
            }
        } catch (Throwable $th) {
            $language = $request->language;
            Log::error($language);
        }

        $negative_prompt = $request->negative_prompt;
        $tone_of_voice = $request->tone_of_voice;
        if ($request->tone_of_voice_custom) {
            $tone_of_voice = $request->tone_of_voice_custom;
        }
        if (! $tone_of_voice) {
            $tone_of_voice = $this->settings->openai_default_tone_of_voice;
        }

        // POST GENERATOR
        if ($post_type === 'post_generator') {
            $description = $request->description;
            $prompt = "Write a post about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different posts. Tone of voice must be $tone_of_voice";
        }

        // POST TITLE GENERATOR
        if ($post_type === 'post_title_generator') {
            $your_description = $request->your_description;
            $prompt = "Post title about $your_description in language $language .Generate $number_of_results post titles. Tone $tone_of_voice.";
        }

        // ARTICLE GENERATOR
        if ($post_type === 'article_generator') {
            $article_title = $request->article_title;
            $focus_keywords = $request->focus_keywords;
            $prompt = "Generate article about $article_title. Focus on $focus_keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice";
        }

        // SUMMARY GENERATOR SUMMARIZER SUMMARIZE TEXT
        if ($post_type === 'summarize_text') {
            $text_to_summary = $request->text_to_summary;
            $tone_of_voice = $request->tone_of_voice;

            $prompt = "Summarize the following text: $text_to_summary in $language using a tone of voice that is $tone_of_voice. The summary should be no longer than $maximum_length words and set the creativity to $creativity in terms of creativity. Generate $number_of_results different summaries.";
        }

        // PRODUCT DESCRIPTION
        if ($post_type === 'product_description') {
            $product_name = $request->product_name;
            $description = $request->description;

            $prompt = "Write product description for $product_name. The language is $language. Maximum length is $maximum_length. Creativity is $creativity between 0 to 1. see the following information as a starting point: $description. Generate $number_of_results different product descriptions. Tone $tone_of_voice.";
        }

        // PRODUCT NAME
        if ($post_type === 'product_name') {
            $seed_words = $request->seed_words;
            $product_description = $request->product_description;

            $prompt = "Generate product names that will appeal to customers who are interested in $seed_words. These products should be related to $product_description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different product names. Tone of voice must be $tone_of_voice";
        }

        // TESTIMONIAL REVIEW GENERATOR
        if ($post_type === 'testimonial_review') {
            $subject = $request->subject;
            $prompt = "Generate testimonial for $subject. Include details about how it helped you and what you like best about it. Be honest and specific, and feel free to get creative with your wording Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different testimonials. Tone of voice must be $tone_of_voice";
        }

        // PROBLEM AGITATE SOLUTION
        if ($post_type === 'problem_agitate_solution') {
            $description = $request->description;

            $prompt = "Write Problem-Agitate-Solution copy for the $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. problem-agitate-solution. Tone of voice must be $tone_of_voice Generate $number_of_results different Problem-Afitate-Solution.";
        }

        // BLOG SECTION
        if ($post_type === 'blog_section') {
            $description = $request->description;

            $prompt = " Write me blog section about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog sections. Tone of voice must be $tone_of_voice";
        }

        // BLOG POST IDEAS
        if ($post_type === 'blog_post_ideas') {
            $description = $request->description;

            $prompt = "Write blog post article ideas about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog post ideas. Tone of voice must be $tone_of_voice";
        }

        // BLOG INTROS
        if ($post_type === 'blog_intros') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write blog post intro about title: $title. And the description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog intros. Tone of voice must be $tone_of_voice";
        }

        // BLOG CONCLUSION
        if ($post_type === 'blog_conclusion') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write blog post conclusion about title: $title. And the description is $description.Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog conclusions. Tone of voice must be $tone_of_voice";
        }

        // FACEBOOK ADS
        if ($post_type === 'facebook_ads') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write facebook ads text about title: $title. And the description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different facebook ads text. Tone of voice must be $tone_of_voice";
        }

        // YOUTUBE VIDEO DESCRIPTION
        if ($post_type === 'youtube_video_description') {
            $title = $request->title;

            $prompt = "write youtube video description about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video descriptions. Tone of voice must be $tone_of_voice";
        }

        // YOUTUBE VIDEO TITLE
        if ($post_type === 'youtube_video_title') {
            $description = $request->description;

            $prompt = "Craft captivating, attention-grabbing video titles about $description for YouTube rankings. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video titles. Tone of voice must be $tone_of_voice";
        }

        // YOUTUBE VIDEO TAG
        if ($post_type === 'youtube_video_tag') {
            $title = $request->title;

            $prompt = "Generate tags and keywords about $title for youtube video. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video tags. Tone of voice must be $tone_of_voice";
        }

        // INSTAGRAM CAPTIONS
        if ($post_type === 'instagram_captions') {
            $title = $request->title;

            $prompt = "Write instagram post caption about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different instagram captions. Tone of voice must be $tone_of_voice";
        }

        // INSTAGRAM HASHTAG
        if ($post_type === 'instagram_hashtag') {
            $keywords = $request->keywords;

            $prompt = "Write instagram hastags for $keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different instagram hashtags. Tone of voice must be $tone_of_voice";
        }

        // SOCIAL MEDIA POST TWEET
        if ($post_type === 'social_media_post_tweet') {
            $title = $request->title;

            $prompt = "Write in 1st person tweet about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different tweets. Tone of voice must be $tone_of_voice";
        }

        // SOCIAL MEDIA POST BUSINESS
        if ($post_type === 'social_media_post_business') {
            $company_name = $request->company_name;
            $provide = $request->provide;
            $description = $request->description;

            $prompt = "Write in company social media post, company name: $company_name. About: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different social media posts. Tone of voice must be $tone_of_voice";
        }

        // FACEBOOK HEADLINES
        if ($post_type === 'facebook_headlines') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write Facebook ads title about title: $title. And description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different facebook ads title. Tone of voice must be $tone_of_voice";
        }

        // GOOGLE ADS HEADLINES
        if ($post_type === 'google_ads_headlines') {
            $product_name = $request->product_name;
            $description = $request->description;
            $audience = $request->audience;

            $prompt = "Write Google ads headline product name: $product_name. Description is $description. Audience is $audience. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different google ads headlines. Tone of voice must be $tone_of_voice";
        }

        // GOOGLE ADS DESCRIPTION
        if ($post_type === 'google_ads_description') {
            $product_name = $request->product_name;
            $description = $request->description;
            $audience = $request->audience;

            $prompt = "Write google ads description product name: $product_name. Description is $description. Audience is $audience. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different google ads description. Tone of voice must be $tone_of_voice";
        }

        // CONTENT REWRITE
        if ($post_type === 'content_rewrite') {
            $contents = $request->contents;

            $prompt = "Rewrite content:  '$contents'. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different rewrited content. Tone of voice must be $tone_of_voice";
        }

        // PARAGRAPH GENERATOR
        if ($post_type === 'paragraph_generator') {
            $description = $request->description;
            $keywords = $request->keywords;

            $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice";
        }

        // Pros & Cons
        if ($post_type === 'pros_cons') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Generate pros & cons about title:  '$title'. Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different pros&cons. Tone of voice must be $tone_of_voice";
        }

        // META DESCRIPTION
        if ($post_type === 'meta_description') {
            $title = $request->title;
            $description = $request->description;
            $keywords = $request->keywords;

            $prompt = "Generate website meta description site name: $title. Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different meta descriptions. Tone of voice must be $tone_of_voice";
        }

        // FAQ Generator (All datas)
        if ($post_type === 'faq_generator') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Answer like faq about subject: $title Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different faqs. Tone of voice must be $tone_of_voice";
        }

        // Email Generator
        if ($post_type === 'email_generator') {
            $subject = $request->subject;
            $description = $request->description;

            $prompt = "Write email about title: $subject, description: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different emails. Tone of voice must be $tone_of_voice";
        }

        // Email Answer Generator
        if ($post_type === 'email_answer_generator') {
            $description = $request->description;

            $prompt = "answer this email content: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different email answers. Tone of voice must be $tone_of_voice";
        }

        // Newsletter Generator
        if ($post_type === 'newsletter_generator') {
            $description = $request->description;
            $subject = $request->subject;
            $title = $request->title;

            $prompt = "generate newsletter template about product_title: $title, reason: $subject description: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different newsletter template. Tone of voice must be $tone_of_voice";
        }

        // Grammar Correction
        if ($post_type === 'grammar_correction') {
            $description = $request->description;

            $prompt = "Correct this to standard $language. Text is '$description'. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different grammar correction. Tone of voice must be $tone_of_voice";
        }

        // TL;DR summarization
        if ($post_type === 'tldr_summarization') {
            $description = $request->description;

            $prompt = "$description. Tl;dr Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different tl;dr. Tone of voice must be $tone_of_voice";
        }

        // AI Rewriter
        if ($post_type === 'ai_rewriter') {
            $content_rewrite = $request->content_rewrite;
            $rewrite_mode = $request->rewrite_mode;

            $prompt = "Original Content: $content_rewrite.\n\n\nMust Rewrite content with $rewrite_mode mode differently with original content. Result language is $language \n";
        }

        if ($post_type === 'ai_image_generator') {
            $imageParam = $request->all();
        }

        if ($post_type === 'ai_video') {
            $videoParam = $request->all();
        }

        if ($post_type === 'ai_code_generator') {
            $description = $request->description;
            $code_language = $request->code_language;
            $prompt = "Write a code about $description, in $code_language";
        }

        $post = OpenAIGenerator::where('slug', $post_type)->first();

        if ($post->custom_template == 1) {
            // $custom_template = OpenAIGenerator::find($request->openai_id);
            $custom_template = $post;
            $prompt = $custom_template->prompt;
            foreach (json_decode($custom_template->questions) as $question) {
                $question_name = '**' . $question->name . '**';
                $prompt = str_replace($question_name, $request[$question->name], $prompt);
            }

            $prompt .= " in $language language. Number of results should be $number_of_results. And the maximum length of $maximum_length characters";
            if ($creativity !== 'undefined') {
                $prompt .= " Creativity is $creativity between 0 and 1.";
            }
            if ($tone_of_voice !== 'undefined') {
                $prompt .= " Tone of voice must be $tone_of_voice.";
            }
        }

        if ($post->type == 'youtube') {
            $language = $request->language;
            $youtube_action = $request->youtube_action;
            if ($youtube_action == 'blog') {
                $prompt = "You are blog writer. Turn the given transcript text into a blog post in and translate to {$language} language. Group the content and create a subheading (with HTML-h2) for each group (without HTML body or head tags or backticks ```html). Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action == 'short') {
                $prompt = "You are transcript editor. Make sense of the given content and explain the main idea. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action == 'list') {
                $prompt = "You are transcript editor. Make sense of the given content and make a list main ideas. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action == 'tldr') {
                $prompt = "You are transcript editor. Make short TLDR. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action == 'prons_cons') {
                $prompt = "You are transcript editor. Make short pros and cons. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            }

            $videoUrl = $request->url;

            $data = (new youtubeTranscriptService)
                ->getTranscript($videoUrl);
            $transcripts = json_decode($data->content(), true);

            if (isset($transcripts['captions'])) {
                $prompt .= $transcripts['captions'] . "\n";
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => __('There are no captions available in the video.'),
                ], 401);
            }
        }

        if ($post->type == 'rss') {
            $language = $request->language;
            $prompt = "write blog post about {$request->title}. Group the content and create a subheading (with HTML-h2) for each group (without HTML body or head tags or backticks ```html).";
            $prompt .= "Your result must be in $language language. Number of results should be $number_of_results. And the maximum length of $maximum_length characters. Tone of voice must be $tone_of_voice. Creativity is $creativity between 0 and 1.";
        }

        // check if there is a company input included in the request
        if ($request->company) {
            $company = Company::find($request->company);
            $product = Product::find($request->product);
            if ($company) {
                if (! isset($prompt)) {
                    $prompt = '';
                }
                $type = $product->type == 0 ? 'Service' : 'Product';
                $prompt .= ".\n Focus on my company and {$type}'s information: \n";
                // Company information
                if ($company->name) {
                    $prompt .= "The company's name is {$company->name}. ";
                }
                // explode industry
                $industry = explode(',', $company->industry);
                $count = count($industry);
                if ($count > 0) {
                    $prompt .= 'The company is in the ';
                    foreach ($industry as $index => $ind) {
                        $prompt .= $ind;
                        if ($index < $count - 1) {
                            $prompt .= ' and ';
                        }
                    }
                }

                if ($company->website) {
                    $prompt .= ". The company's website is {$company->website}. ";
                }

                if ($company->target_audience) {
                    $prompt .= "The company's target audience is: {$company->target_audience}. ";
                }

                if ($company->tagline) {
                    $prompt .= "The company's tagline is {$company->tagline}. ";
                }

                if ($company->description) {
                    $prompt .= "The company's description is {$company->description}. ";
                }
                if ($product) {
                    // Product information
                    if ($product->key_features) {
                        $prompt .= "The {$product->type}'s key features are {$product->key_features}. ";
                    }

                    if ($product->name) {
                        $prompt .= "The {$product->type}'s name is {$product->name}. \n";
                    }
                }
            }
        }

        if ($post->type === 'text' || $post->type === 'rss' || $post->type === 'youtube') {
            return $this->textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user);
        }

        if ($post->type === 'code') {
            return $this->codeOutput($prompt, $post, $user);
        }

        if ($post->type === 'image') {
            return $this->imageOutput($imageParam, $post, $user);
        }

        if ($post->type === 'video') {
            return $this->videoOutput($videoParam, $post, $user);
        }

        if ($post->type === 'audio') {
            $file = $request->file('file');

            return $this->audioOutput($file, $post, $user);
        }

        if ($post->type === 'isolator') {
            $request->validate([
                'file' => 'required|file|mimes:ogg,mpga,mp3,mp4,mpeg,m4a,wav,webm',
            ], [
                'file.mimes' => __('Invalid file extension, accepted extensions are ogg, mp3, mp4, mpeg, mpga, m4a, wav, and webm.'),
                'file.max'   => __('The file size must not exceed 500 MB.'),
            ]);
            $file = $request->file('file');

            return $this->audioIsolator($file, $post, $user);
        }
    }

    public function textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user): JsonResponse
    {
        $entry = UserOpenai::create([
            'team_id'   => $user->team_id,
            'title'     => request('title') ?: null,
            'slug'      => str()->random(7) . str($user?->fullName())->slug() . '-workbook',
            'user_id'   => $user->id,
            'openai_id' => $post->id,
            'input'     => $prompt,
            'response'  => null,
            'output'    => null,
            'hash'      => str()->random(256),
            'credits'   => 0,
            'words'     => 0,
        ]);

        $message_id = $entry->id;
        $workbook = $entry;
        $inputPrompt = $prompt;

        return response()->json(compact('message_id', 'creativity', 'maximum_length', 'number_of_results', 'inputPrompt', 'workbook'));
    }

    /**
     * @throws JsonException
     */
    public function codeOutput($prompt, $post, $user): JsonResponse
    {
        $driver = Entity::driver();
        $driver->redirectIfNoCreditBalance();
        if ($driver->enum()->value === EntityEnum::TEXT_DAVINCI_003->value) {
            $response = FacadesOpenAI::completions()->create([
                'model'      => $driver->enum()->value,
                'prompt'     => $prompt,
                'max_tokens' => (int) $this->settings->openai_max_output_length,
            ]);
        } else {
            $response = FacadesOpenAI::chat()->create([
                'model'    => $driver->enum()->value,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);
        }
        $entry = new UserOpenai([
            'team_id'     => $user->team_id,
            'title'       => request('title') ?: null,
            'slug'        => Str::random(7) . Str::slug($user?->fullName()) . '-workbook',
            'user_id'     => Auth::id(),
            'openai_id'   => $post->id,
            'input'       => $prompt,
            'response'    => json_encode($response->toArray(), JSON_THROW_ON_ERROR),
        ]);

        if ($driver->enum()->value === EntityEnum::TEXT_DAVINCI_003->value) {
            $entry->output = $response['choices'][0]['text'];
        } else {
            $entry->output = $response->choices[0]->message->content;
        }
        $entry->hash = Str::random(256);
        $entry->credits = countWords($entry->output);
        $entry->words = 0;
        $entry->save();

        $driver->input($entry->output)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());

        return response()->json(compact('post', 'entry'));
    }

    public function imageOutput($param, $post, $user): JsonResponse
    {
        $lockKey = 'generate_image_output_lock';
        if (! Cache::lock($lockKey, 10)->get()) { // Attempt to acquire lock
            return response()->json(['message' => 'Image generation in progress. Please try again later.'], 409);
        }

        $engineCheck = match ($param['image_generator']) {
            'flux-pro' => EngineEnum::FAL_AI->value,
            default    => $param['image_generator'],
        };
        $entries = [];

        try {
            $engine = EngineEnum::fromSlug($engineCheck);
            $model = $this->getDefaultModel($engine);
            $code = $this->getEngineCode($engine);
            $number_of_images = (int) $param['image_number_of_images'];

            $driver = Entity::driver($model)->inputImageCount($number_of_images)->calculateCredit();
            $chkLmt = Helper::checkImageDailyLimit();
            if ($chkLmt->getStatusCode() === 429) {
                return $chkLmt;
            }
            $driver->redirectIfNoCreditBalance();
            $apiKey = $this->getOpenAiApiKey($user);
            config(['openai.api_key' => $apiKey]);
            set_time_limit(120);

            for ($i = 0; $i < $number_of_images; $i++) {
                $imageDetails = $this->processImageGeneration($engine, $model, $param);
                $savePath = $this->saveImageOutputToStorage($imageDetails);
                $entry = $this->saveEntryToDatabase($imageDetails, $user, $post, $code, $savePath);
                $entry->img_id = 'img-' . $entry->response . '-' . $entry->id;
                $entries[] = $entry;
            }
            $driver->decreaseCredit();
            Usage::getSingle()->updateImageCounts($driver->calculate());
            Cache::lock($lockKey)->release();

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        } finally {
            Cache::lock($lockKey)->forceRelease();
        }

        return response()->json(['status' => 'success', 'images' => $entries, 'image_storage' => $this->settings_two->ai_image_storage]);
    }

    public function audioOutput($file, $post, $user)
    {
        $driver = Entity::driver(EntityEnum::WHISPER_1);
        $driver->redirectIfNoCreditBalance();
        $path = 'uploads/audio/';
        $file_name = Str::random(4) . '-' . Str::slug($user?->fullName()) . '-audio.' . $file->getClientOriginalExtension();

        // Audio Extension Control
        $imageTypes = ['mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm'];
        if (! in_array(Str::lower($file->getClientOriginalExtension()), $imageTypes)) {
            $data = [
                'errors' => ['Invalid extension, accepted extensions are mp3, mp4, mpeg, mpga, m4a, wav, and webm.'],
            ];

            return response()->json($data, 419);
        }

        $file->move($path, $file_name);
        $fullPath = $path . $file_name;

        $response = FacadesOpenAI::audio()->transcribe([
            'file'            => fopen($fullPath, 'rb'),
            'model'           => EntityEnum::WHISPER_1->value,
            'response_format' => 'verbose_json',
        ]);

        $text = $response->text;

        UserOpenai::create([
            'team_id'   => $user->team_id,
            'title'     => request('title') ?: null,
            'slug'      => Str::random(7) . Str::slug($user?->fullName()) . '-speech-to-text-workbook',
            'user_id'   => $user->id,
            'openai_id' => $post->id,
            'input'     => $fullPath,
            'response'  => json_encode($response->toArray(), JSON_THROW_ON_ERROR),
            'output'    => $text,
            'hash'      => Str::random(256),
            'credits'   => countWords($text),
            'words'     => countWords($text),
        ]);

        $driver->input($text)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());

        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
        $openai = OpenAIGenerator::find($post->id);

        return response()->json(compact('userOpenai', 'openai'));
    }

    public function finalizeOutput($post, $entry): JsonResponse // not used. see line 546
    {
        // Workbook add-on
        $workbook = $entry;
        $html = view('panel.user.openai.documents_workbook_textarea', compact('workbook'))->render();
        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
        $openai = OpenAIGenerator::find($post->id);
        $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();

        return response()->json(compact('html', 'html2'));
    }

    private function getOpenAiApiKey(?User $user): string
    {
        return ApiHelper::setOpenAiKey();
    }

    private function getStableApiKey(): string
    {
        $stableDiffusionKeys = explode(',', $this->settings_two->stable_diffusion_api_key);

        return $stableDiffusionKeys[array_rand($stableDiffusionKeys)];
    }

    /**
     * Get the default model based on the AI engine.
     *
     * @throws Exception
     */
    private function getDefaultModel(?EngineEnum $engine): ?EntityEnum
    {
        return match ($engine) {
            EngineEnum::OPEN_AI          => $this->getDefaultOpenAiImageModel(),
            EngineEnum::STABLE_DIFFUSION => $this->getStableDiffusionDefaultModel(),
            EngineEnum::FAL_AI           => $this->getDefaultFalAiModel(),

            default                      => throw new Exception(__('Invalid AI Engine')),
        };
    }

    /**
     * Process image generation based on the AI engine.
     *
     * @throws Exception|GuzzleException
     */
    private function processImageGeneration(?EngineEnum $engine, ?EntityEnum $model, array $param): array
    {
        return match ($engine) {
            EngineEnum::OPEN_AI          => $this->processOpenAIImage($model, $param),
            EngineEnum::STABLE_DIFFUSION => $this->processStableDiffusionImage($model, $param),
            EngineEnum::FAL_AI           => $this->processFalAIImage($model, $param),

            default                      => throw new Exception(__('Invalid AI Engine')),
        };
    }

    private function processOpenAIImage(?EntityEnum $model, array $param): array
    {
        $is_demo = Helper::appIsDemo();
        $size = $param['size'];
        $description = $param['description'];
        $style = $param['image_style'] ?? null;
        $lighting = $param['image_lighting'] ?? null;
        $mood = $param['mood'] ?? null;
        $quality = $param['quality'];
        $prompt = $description;
        if (is_null($prompt)) {
            throw new RuntimeException(__('You must provide a prompt'));
        }
        $attributes = [
            'style'    => $style ? "$style style" : null,
            'lighting' => $lighting ? "$lighting lighting" : null,
            'mood'     => $mood ? "$mood mood" : null,
        ];
        $prompt .= ' ' . implode(' ', array_filter($attributes));
        $response = FacadesOpenAI::images()->create([
            'model'           => $model,
            'prompt'          => $prompt,
            'size'            => $is_demo ? $this->getDemoImageSize($model) : $size,
            'response_format' => 'b64_json',
            'quality'         => $is_demo ? 'standard' : $quality,
            'n'               => 1,
        ]);
        $contents = base64_decode($response['data'][0]['b64_json']);
        $nameOfImage = Str::random(12) . '-DALL-E-' . Str::slug(explode(' ', mb_substr($prompt, 0, 15))[0]) . '.png';

        return [
            'prompt'                => $prompt,
            'imageContent'          => $contents,
            'nameOfImage'           => $nameOfImage,
        ];
    }

    /**
     * @throws GuzzleException
     * @throws RandomException
     * @throws JsonException
     */
    private function processStableDiffusionImage(?EntityEnum $model, array $param): array
    {
        $stable_type = $param['type'];
        $prompt = $param['stable_description'];
        if (is_null($prompt)) {
            throw new RuntimeException(__('You must provide a prompt'));
        }

        $negative_prompt = $param['negative_prompt'];
        $style_preset = $param['style_preset'];
        $sampler = $param['sampler'];
        $clip_guidance_preset = $param['clip_guidance_preset'];
        $image_resolution = $param['image_resolution'];
        $init_image = $param['image_src'] ?? null;
        $mood = $param['mood'] ?? null;
        $defaultSdModel = $this->getStableDiffusionDefaultModel()->value;

        $v2BetaModels = [
            EntityEnum::SD_3->value,
            EntityEnum::SD_3_TURBO->value,
            EntityEnum::SD_3_MEDIUM->value,
            EntityEnum::SD_3_LARGE->value,
            EntityEnum::SD_3_LARGE_TURBO->value,
            EntityEnum::CORE->value,
            EntityEnum::ULTRA->value,
        ];
        $width = (int) explode('x', $image_resolution)[0];
        $height = (int) explode('x', $image_resolution)[1];

        if ($defaultSdModel === BedrockEngine::BEDROCK->value && $stable_type === 'text-to-image') {
            $response = $this->bedrockService->invokeStableDiffusion($prompt, random_int(1, 1000000), $width, $height);
            $nameOfImage = Str::random(12) . '-AWS-SD-' . Str::slug(explode(' ', mb_substr($prompt, 0, 15))[0]) . '.png';

            return [
                'prompt'                => $prompt,
                'imageContent'          => $response,
                'nameOfImage'           => $nameOfImage,
            ];
        }
        $stableDiffusionKey = $this->getStableApiKey();
        if (empty($stableDiffusionKey)) {
            throw new RuntimeException(__('You must provide a StableDiffusion API Key.'));
        }

        $sd3Payload = [];
        $baseUri = in_array($defaultSdModel, $v2BetaModels, true) && in_array($stable_type, ['text-to-image', 'image-to-image'], true)
            ? 'https://api.stability.ai/v2beta/stable-image/generate/'
            : 'https://api.stability.ai/v1/generation/';
        $contentType = ($stable_type === 'upscale' || $stable_type === 'image-to-image') ? 'multipart/form-data' : 'application/json';
        $client = new Client([
            'base_uri' => $baseUri,
            'headers'  => [
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $stableDiffusionKey,
                'Accept'        => 'application/json',
            ],
        ]);
        $payload = [
            'cfg_scale'            => 7,
            'clip_guidance_preset' => $clip_guidance_preset ?? 'NONE',
            'samples'              => 1,
            'steps'                => 50,
        ];
        if ($sampler) {
            $payload['sampler'] = $sampler;
        }
        if ($style_preset) {
            $payload['style_preset'] = $style_preset;
        }
        $content_type = 'json';
        switch ($stable_type) {
            case 'multi-prompt':
                $stable_url = 'text-to-image';
                $payload['width'] = $width;
                $payload['height'] = $height;
                $arr = [];
                foreach ($prompt as $p) {
                    $arr[] = [
                        'text'   => $p . ($mood === null ? '' : (' ' . $mood . ' mood.')),
                        'weight' => 1,
                    ];
                }
                $prompt = $arr;
                $payload['text_prompts'] = $prompt;

                break;
            case 'upscale':
                $content_type = 'multipart';
                $stable_url = 'image-to-image/upscale';
                $defaultSdModel = 'esrgan-v1-x2plus';
                $payload = [];
                $payload['image'] = $init_image->get();
                $prompt = [
                    [
                        'text'   => $prompt . '-' . Str::random(16),
                        'weight' => 1,
                    ],
                ];

                break;
            case 'image-to-image':
                $content_type = 'multipart';
                $stable_url = $stable_type;
                $payload['init_image'] = $init_image->get();
                $sd3Payload = [
                    [
                        'name'     => 'prompt',
                        'contents' => $prompt,
                    ],
                    [
                        'name'     => 'mode',
                        'contents' => 'image-to-image',
                    ],
                    [
                        'name'     => 'strength',
                        'contents' => 0,
                    ],
                    [
                        'name'     => 'image',
                        'contents' => $init_image->get(),
                        'filename' => $init_image->getClientOriginalName(),
                    ],
                ];
                $prompt = [
                    [
                        'text'   => $prompt . ($mood === null ? '' : (' ' . $mood . ' mood.')),
                        'weight' => 1,
                    ],
                ];
                $payload['text_prompts'] = $prompt;

                break;
            default:
                $stable_url = $stable_type;
                $payload['width'] = $width;
                $payload['height'] = $height;
                $sd3Payload = [
                    [
                        'name'     => 'prompt',
                        'contents' => $prompt,
                    ],
                    [
                        'name'     => 'file',
                        'contents' => 'no',
                    ],
                    [
                        'name'     => 'output_format',
                        'contents' => 'png',
                    ],
                ];
                $prompt = [
                    [
                        'text'   => $prompt . ($mood === null ? '' : (' ' . $mood . ' mood.')),
                        'weight' => 1,
                    ],
                ];
                $payload['text_prompts'] = $prompt;

                break;
        }
        if ($negative_prompt) {
            $prompt[] = ['text' => $negative_prompt, 'weight' => -1];
        }
        if ($content_type === 'multipart') {
            $multipart = [];
            foreach ($payload as $key => $value) {
                if (! is_array($value)) {
                    $multipart[] = ['name' => $key, 'contents' => $value];

                    continue;
                }
                foreach ($value as $multiKey => $multiValue) {
                    $multiName = $key . '[' . $multiKey . ']' . (is_array($multiValue) ? '[' . key($multiValue) . ']' : '') . '';
                    $multipart[] = ['name' => $multiName, 'contents' => (is_array($multiValue) ? reset($multiValue) : $multiValue)];
                }
            }
            $payload = $multipart;
        }

        try {
            if (in_array($defaultSdModel, $v2BetaModels, true) && in_array($stable_type, ['text-to-image', 'image-to-image'], true)) {
                $defaultSdModel = 'sd3';
                $sd3Payload[] = ['name' => 'model', 'contents' => $defaultSdModel];
                $response = $client->post($defaultSdModel, [
                    'headers'   => ['accept' => 'application/json'],
                    'multipart' => $sd3Payload,
                ]);
            } else {
                $defaultSdModel = $stable_type === 'multi-prompt' ? EntityEnum::STABLE_DIFFUSION_V_1_6->value : $defaultSdModel;
                $response = $client->post("$defaultSdModel/$stable_url", [
                    $content_type => $payload,
                ]);
            }
        } catch (Exception $e) {
            if ($e->hasResponse()) {
                $errorMessage = $e->getResponse()->getBody()->getContents();
                $errorData = json_decode($errorMessage, true, 512, JSON_THROW_ON_ERROR);
                if (isset($errorData['message'])) {
                    throw new RuntimeException($errorData['message']);
                }

                throw new RuntimeException($e->getMessage());
            }

            throw new RuntimeException($e->getMessage());
        }

        $body = $response->getBody();
        if ($response->getStatusCode() === 200) {
            $nameOfPrompt = explode(' ', mb_substr($prompt[0]['text'], 0, 15))[0];
            $nameOfImage = Str::random(12) . '-STABLE-' . $nameOfPrompt . '.png';
            if (
                ($stable_type === 'text-to-image' || $stable_type === 'image-to-image') && in_array($defaultSdModel, $v2BetaModels, true)
            ) {
                $contents = base64_decode(json_decode($body, false, 512, JSON_THROW_ON_ERROR)->image);
            } else {
                $contents = base64_decode(json_decode($body, false, 512, JSON_THROW_ON_ERROR)->artifacts[0]->base64);
            }
        } else {
            if ($body->status === 'error') {
                $message = $body->message;
            } else {
                $message = __('Failed, Try Again');
            }

            throw new RuntimeException($message);
        }

        return [
            'prompt'                => $prompt[0]['text'],
            'imageContent'          => $contents,
            'nameOfImage'           => $nameOfImage,
        ];
    }

    private function processFalAIImage(?EntityEnum $model, array $param): array
    {
        $prompt = $param['description_flux_pro'];
        $requestId = FalAIService::generate($prompt, $model);

        return [
            'engine'                => EngineEnum::FAL_AI,
            'requestId'             => $requestId,
            'status'                => 'IN_QUEUE',
            'output'                => asset(self::LOADING_GIF),
            'prompt'                => $prompt,
            'imageContent'          => null,
            'nameOfImage'           => Str::random(12) . '-FLUX-' . Str::slug(explode(' ', mb_substr($prompt, 0, 15))[0]) . '.png',
        ];
    }

    private function saveEntryToDatabase(array $imageDetails, User $user, OpenAIGenerator $post, string $code, string $savePath): UserOpenai
    {
        $data = [
            'team_id'   => $user->team_id,
            'title'     => $imageDetails['nameOfImage'],
            'slug'      => Str::random(7) . Str::slug($user?->fullName()) . '-workbook',
            'user_id'   => $user->id,
            'openai_id' => $post->id,
            'input'     => $imageDetails['prompt'],
            'response'  => $code,
            'output'    => ThumbImage(url($savePath)),
            'hash'      => Str::random(256),
            'credits'   => 1,
            'words'     => 0,
            'storage'   => $this->settings_two->ai_image_storage,
            'payload'   => request()?->all(),
        ];
        if (isset($imageDetails['engine']) && $imageDetails['engine'] === EngineEnum::FAL_AI) {
            $data['request_id'] = $imageDetails['requestId'];
            $data['status'] = $imageDetails['status'];
            $data['output'] = $imageDetails['output'];
        }

        return UserOpenai::create($data);
    }

    private function saveImageOutputToStorage(array $imageDetails): string
    {
        if (isset($imageDetails['engine']) && $imageDetails['engine'] === EngineEnum::FAL_AI) {
            return '/';
        }

        $image_storage = $this->settings_two->ai_image_storage;

        return match ($image_storage) {
            self::STORAGE_S3    => $this->saveImageToS3($imageDetails),
            self::CLOUDFLARE_R2 => $this->saveImageToR2($imageDetails),
            default             => $this->saveImageToLocal($imageDetails),
        };
    }

    private function saveImageToS3(array $imageDetails): string
    {
        $localPath = $this->saveImageToLocal($imageDetails);

        try {
            $uploadedFile = new File($localPath);
            $aws_path = Storage::disk('s3')->put('', $uploadedFile);
            unlink($localPath);
            $fullAWSPath = Storage::disk('s3')->url($aws_path);
        } catch (Exception $e) {
            throw new RuntimeException('AWS Error - ' . $e->getMessage());
        }

        return $fullAWSPath;
    }

    private function saveImageToLocal(array $imageDetails): string
    {
        Storage::disk('public')->put($imageDetails['nameOfImage'], $imageDetails['imageContent']);

        return 'uploads/' . $imageDetails['nameOfImage'];
    }

    private function saveImageToR2(array $imageDetails): string
    {
        Storage::disk('r2')->put($imageDetails['nameOfImage'], $imageDetails['imageContent']);

        return Storage::disk('r2')->url($imageDetails['nameOfImage']);
    }

    private function getEngineCode(?EngineEnum $engine): string
    {
        return match ($engine) {
            EngineEnum::STABLE_DIFFUSION => 'SD',
            EngineEnum::FAL_AI           => 'FL',
            default                      => 'DE',
        };
    }

    private function getDemoImageSize(?EntityEnum $model): string
    {
        return match ($model) {
            EntityEnum::DALL_E_3 => '1024x1024',
            EntityEnum::DALL_E_2 => '256x256',
            default              => '512x512',
        };
    }

    private function getDefaultOpenAiImageModel(): EntityEnum
    {
        $default = match ($this->settings_two->dalle) {
            'dalle3' => EntityEnum::DALL_E_3->slug(),
            'dalle2' => EntityEnum::DALL_E_2->slug(),
            default  => $this->settings_two->dalle,
        };

        return EntityEnum::fromSlug($default) ?? EntityEnum::DALL_E_2;
    }

    private function getStableDiffusionDefaultModel(): EntityEnum
    {
        return EntityEnum::fromSlug($this->settings_two?->stablediffusion_default_model) ?? EntityEnum::SD_3;
    }

    private function getDefaultFalAiModel(): EntityEnum
    {
        return EntityEnum::fromSlug(setting('fal_ai_default_model', EntityEnum::FLUX_PRO->value)) ?? EntityEnum::FLUX_PRO;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Throwable
     * @throws JsonException
     */
    public function audioIsolator($file, $post, $user)
    {
        $driver = Entity::driver(EntityEnum::ISOLATOR);
        $driver->redirectIfNoCreditBalance();
        set_time_limit(3000);
        $apiKey = $this->settings_two->elevenlabs_api_key;
        $mp3File = $file->getRealPath();
        $mp3FileName = $file->getClientOriginalName();
        $client = new Client;
        $response = $client->request('POST', 'https://api.elevenlabs.io/v1/audio-isolation', [
            'headers' => [
                'xi-api-key' => $apiKey,
            ],
            'multipart' => [
                [
                    'name'     => 'audio',
                    'contents' => fopen($mp3File, 'r'),
                    'filename' => $mp3FileName,
                ],
            ],
            'timeout' => 3000,
        ]);
        $resAudio = $response->getBody();
        $characterCost = $response->getHeader('character-cost');
        if (count($characterCost) > 0) {
            $characterCost = $characterCost[0];
        } else {
            $characterCost = 0;
        }
        $defaultWordChars = 5;
        $wordsCount = $characterCost / $defaultWordChars;

        $driver->input($characterCost)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());

        $audioName = $user->id . '-' . Str::random(20) . '.mp3';
        Storage::disk('public')->put($audioName, $resAudio);
        $ai = OpenAIGenerator::whereSlug('ai_voice_isolator')->first();

        $langsAndVoices['language'][] = 'en-US';
        $langsAndVoices['voices'][] = $audioName;

        $entry = UserOpenai::create([
            'team_id'   => $user->team_id,
            'output'    => $audioName,
            'input'     => $audioName,
            'title'     => __('Isolated Voice'),
            'slug'      => Str::random(20) . Str::slug($user?->fullName()) . '-isolated-voice',
            'user_id'   => $user->id,
            'hash'      => Str::random(256),
            'openai_id' => $ai->id,
        ]);

        $entry->response = json_encode($langsAndVoices, JSON_THROW_ON_ERROR);

        $entry->credits = $wordsCount;
        $entry->words = $wordsCount;
        $entry->save();

        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $ai->id)->orderBy('created_at', 'desc')->paginate(10);
        $userOpenai->withPath(route('dashboard.user.openai.generator', 'ai_voice_isolator'));
        $openai = OpenAIGenerator::where('id', $ai->id)->first();

        return response()->json(compact('userOpenai', 'openai'));
    }
}
