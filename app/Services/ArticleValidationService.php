<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ArticleValidationService
{
    /**
     * Validate the given data with the article validation rules.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, $this->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get the validation rules for article creation and update.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'title' => 'required|max:100',
            'content' => 'required',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ];
    }
}
