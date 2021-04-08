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


    /**
     * @var ?string
     */
    private $discountCard;

    public function __construct(Request $request)
    {
        $this->registrationNumber = $request->get('registration_number');
        $this->category = $request->get('category');
        $this->discountCard = $request->get('discount_card');
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getDiscountCard(): ?string
    {
        return $this->discountCard;
    }

}
