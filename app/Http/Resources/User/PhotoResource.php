<?php

namespace App\Http\Resources\User;

use App\Models\Like;
use App\Models\Media;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PhotoResource
 * @package App\Http\Resources\User
 * @mixin User
 *
 * @OA\Schema(schema="PhotoResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="full_url", type="string", example="/path/to/file", description="Photo full size"),
 *     @OA\Property(property="thumb_url", type="string", example="/path/to/file", description="Photo mini size"),
 *     @OA\Property(property="main", type="boolean", example="true / false"),
 *     @OA\Property(property="top", type="boolean", example="true / false"),
 *     @OA\Property(property="top_place", type="integer", example="5"),
 *     @OA\Property(property="liked", type="boolean", example="true / false", description="If this photo liked by auth user"),
 *     @OA\Property(property="likes", type="integer", example="10", description="Likes count, if this is auth user"),
 *     @OA\Property(property="created_at", type="date", example="2020-01-11 12:01:05"),
 * )
 */
class PhotoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'full_url' => $this->getUrl(),
            'thumb_url' => $this->getUrl('thumb'),
            'main' => $this->getCustomProperty('main'),
            'top' => $this->getCustomProperty('top'),
            'top_place' => $this->when($this->model_id == \Auth::id(), $this->getTopPlace()),
            'liked' => $this->when($this->model_id !== \Auth::id(), Like::where([['media_id', $this->id], ['from_user_id', \Auth::id()]])->first() ? true : false),
            'likes' => Like::where([['media_id', $this->id]])->count(),
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
        ];
    }

    public function getTopPlace()
    {
        $top = (new Media)->getByRating()->pluck('id');

        $topPlace = $top->search($this->getKey());

        if($topPlace !== false) return $topPlace + 1;
    }
}
