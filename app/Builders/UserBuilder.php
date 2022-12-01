<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Class UserBuilder
 * @package App\Builders
 * @see https://timacdonald.me/dedicated-eloquent-model-query-builders/
 */
class UserBuilder extends Builder
{
    /**
     * @param string $location
     * @return $this
     */
    public function location(string $location): self
    {
        switch ($location) {
            case 'all':
                return $this;
                break;
            case 'nearby':
                return $this->whereHas('location', function (Builder $query) {
                    $query->where('city', 'like', \Auth::user()->location->city);
                });
                break;
            case 'country':
                return $this->whereHas('location', function (Builder $query) {
                    $query->where('country', 'like', \Auth::user()->location->country);
                });
                break;
            case 'city':
                return $this->whereHas('location', function (Builder $query) {
                    $query->where('city', 'like', \Auth::user()->location->city);
                });
                break;
        }
    }

    /**
     * @param string $online
     * @return $this
     */
    public function online(string $online): self
    {
        switch ($online) {
            case 'all':
                return $this;
                break;
            case 'online':
                return $this->where('last_online_at', '>', Carbon::now()->subMinutes(5));
                break;
            case 'recently':
                return $this->where('last_online_at', '>', Carbon::now()->subDay());
                break;
        }
    }
}
