<?php

namespace App\RequestDto;

use Illuminate\Http\Request;

class CheckoutDto
{
    /**
     * @var string
     */
    private $registrationNumber;

    public function __construct(Request $request)
    {
        $this->registrationNumber = $request->get('registration_number');
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

}
