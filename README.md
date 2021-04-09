## About Parking calculator App

This is a test Laravel project that is being experimented on for personal development purpose.

The use of the codebase in not guaranteed.    

### Installation

Checkout repo. `cd lab/ && ./vendor/bin/sail up`

#### Checkin endpoint
POST http://localhost:85/api/ticket
```json
{
    "registration_number": "AF1235",
    "category": "c",
    "discount_card": "card-gold"
}
```

#### Checkout endpoint
PATCH http://localhost:85/api/checkout
```json
{
    "registration_number": "cf1235"
}
```

#### Get availability endpoint
GET http://localhost:85/api/availability

#### Get price endpoint
GET http://localhost:85/api/price/AF1235
