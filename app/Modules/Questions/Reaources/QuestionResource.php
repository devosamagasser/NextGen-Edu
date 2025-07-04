<?php

namespace App\Modules\Questions\Reaources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'answers' => $this->whenLoaded('answers', function (){
                return AnswerResource::collection($this->answers);
            }),
        ];
    }
}
