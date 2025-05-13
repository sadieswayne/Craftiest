<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocsFavorite extends Model
{
    protected $table = 'user_docs_favorite';

    public $timestamps = false;

    public static function getFavoriteDocs($user_id)
    {
        $favorite_docs_ids = UserDocsFavorite::where('user_id', $user_id)->pluck('user_openai_id');
        $favorite_docs = UserOpenai::whereIn('id', $favorite_docs_ids)->get()->take(5);

        return $favorite_docs;
    }
}
