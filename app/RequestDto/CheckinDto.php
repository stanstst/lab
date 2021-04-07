<?php

namespace App\RequestDto;

use Illuminate\Http\Request;

class CheckinDto
{
    /**
     * @var string
     */
    private $registrationNumber;

    /**
     * @var string
     */
    private $category;

    public function __construct(Request $request)
    {
        $this->registrationNumber = $request->get('registration_number');
        $this->category = $request->get('category');
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

}
