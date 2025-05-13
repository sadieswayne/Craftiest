<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Usage extends Model
{
    protected $table = 'usage';

    protected $fillable = [
        'total_user_count',
        'this_week_user_count',
        'last_week_user_count',
        'total_word_count',
        'this_week_word_count',
        'last_week_word_count',
        'total_image_count',
        'this_week_image_count',
        'last_week_image_count',
    ];

    /**
     * @return mixed1
     */
    public static function getSingle()
    {
        return static::firstOrCreate([]);
    }

    public function updateWordCounts($count): void
    {
        $this->total_word_count += $count;
        $this->this_week_word_count += $count;

        // Check if a week has passed since the last update
        if (Carbon::now()->diffInWeeks($this->updated_at) >= 1) {
            // Move this week counts to last week counts
            $this->last_week_word_count = $this->this_week_word_count;
            $this->this_week_word_count = 0;
        }

        $this->save();
    }

    public function updateImageCounts($count): void
    {
        $this->total_image_count += $count;
        $this->this_week_image_count += $count;

        // Check if a week has passed since the last update
        if ($this->updated_at && Carbon::now()->diffInWeeks($this->updated_at) >= 1) {
            // Move this week counts to last week counts
            $this->last_week_image_count = $this->this_week_image_count;
            $this->this_week_image_count = 0;
        }

        $this->save();
    }

    public function updateUserCount($count = 0): void
    {
        $this->total_user_count += $count;
        if (Carbon::now()->diffInWeeks($this->updated_at) >= 1) {
            $this->last_week_user_count = $this->this_week_user_count;
            $this->this_week_user_count = 0;
        }
        $this->this_week_user_count = max(0, $this->this_week_user_count + $count);
        $this->save();
    }

    // Define method to update sales count
    public function updateSalesCount($count)
    {
        $this->total_sales += $count;
        $this->this_week_sales += $count;

        // Check if a week has passed since the last update
        if ($this->updated_at && Carbon::now()->diffInWeeks($this->updated_at) >= 1) {
            // Move this week counts to last week counts
            $this->last_week_sales = $this->this_week_sales;
            $this->this_week_sales = 0;
        }

        $this->save();
    }

    public function update(array $attributes = [], array $options = []): void
    {
        parent::update($attributes, $options);
        $this->updateCounts();
    }

    protected function updateCounts(): void
    {
        $currentWeek = Carbon::now()->weekOfYear;
        if (! $this->updated_at || $this->updated_at->weekOfYear !== $currentWeek) {
            // Move this week counts to last week counts
            $this->last_week_word_count = $this->this_week_word_count;
            $this->last_week_image_count = $this->this_week_image_count;
            $this->last_week_user_count = $this->this_week_user_count;
            $this->last_week_sales = $this->this_week_sales;
            // Reset this week counts
            $this->this_week_word_count = 0;
            $this->this_week_image_count = 0;
            $this->this_week_user_count = 0;
            $this->this_week_sales = 0;
        }
        $this->total_word_count = $this->last_week_word_count + $this->this_week_word_count;
        $this->total_image_count = $this->last_week_image_count + $this->this_week_image_count;
        $this->total_user_count = $this->last_week_user_count + $this->this_week_user_count;
        $this->total_sales = $this->last_week_sales + $this->this_week_sales;
        $this->save();
    }
}
