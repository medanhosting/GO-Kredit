<?php

namespace Thunderlabid\Socialmedia\Listeners;
use Thunderlabid\Socialmedia\Models\Analysis;

class AnalysisListener
{
    /**
     * Handle user logout events.
     */
    public function onAnalysisCompleted($event) 
    {
        // Newly created analysis
        $new_analysis = $event->analysis;

        // Get Previous Analysis
        $prev_analysis = Analysis::between($new_analysis->created_at->copy()->subMonth(12), $new_analysis->created_at)->where('id', '!=', $new_analysis->id)->latest()->first();

        if (!$prev_analysis)
        {
            $new_analysis->unfollower_list = null;
            $new_analysis->new_follower_list = null;
            $new_analysis->save();
            return;
        }

        // Find Unfollower
        $new_analysis->unfollower_list = $prev_analysis->follower_list->filter(function($item) use ($new_analysis) { return $new_analysis->follower_list->where('id', $item->id)->count() == 0; })->toArray();
        $new_analysis->unfollowers = $new_analysis->unfollower_list->count();

        // Find new follower
        $new_analysis->new_follower_list = $new_analysis->follower_list->filter(function($item) use ($prev_analysis) { return $prev_analysis->follower_list->where('id', $item->id)->count() == 0; })->toArray();
        $new_analysis->new_followers = $new_analysis->new_follower_list->count();

        $new_analysis->save();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen('Thunderlabid\Socialmedia\Events\AnalysisCreated', 'Thunderlabid\Socialmedia\Listeners\AnalysisListener@onAnalysisCompleted');
    }

}