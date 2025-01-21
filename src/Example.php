<?php

namespace Meest\Integration;

use Meest\Integration\Integration;

class Example
{
    private $integration;

    public function __construct()
    {
        $this->integration = new Integration([
            'url' => 'https://dev-integration.meest.com',
            'login' => '',
            'password' => '',
        ]);
    }

    public function userCreate()
    {
        $data = [
            'phone' => '+3806333333333',
            'mail' => 'test@test.com',
            'firstName' => 'firstName',
            'lastName' => 'lastName',
            'middleName' => 'middleName',
            'language' => 'UK'
        ];

        return $this->integration->userCreate($data);
    }

    public function userAddressCreate()
    {
        $data = [
            'userId' => 586672,
            'status' => 'VERIFIED',
            'phone' => '+3809133333333',
            'email' => 'test@test.com',
            'name' => 'test',
            'firstName' => 'zood fname',
            'lastName' => 'zood lname',
            'middleName' => 'zood mname',
            'birthdayDate' => '2024-12-03',
            'country' => 'UZ',
            'state' => 'Taskant',
            'zip' => '100037',
            'region' => 'Taskant',
            'regionId' => null,
            'city' => 'Taskant',
            'streetId' => null,
            'street' => 'Zelena',
            'house' => '100',
            'apartment' => '',
            'address' => '',
            'deliveryCarrier' => null,
            'senderIsReceiver' => true,
            'main' => true,
            'passportCountryCode' => 'UZB',
            'passportSeries' => '',
            'passportNumber' => '',
            'passportIssuedBy' => '',
            'passportIssuedDate' => '2024-12-03',
            'vatin' => '',
            'branchId' => ''
        ];

        return $this->integration->userAddressCreate($data);
    }

    public function shipmentCreate()
    {
        $data = [
            'userId' => 586672,
            'addressId' => 2058086,
            'warehouseId' => 11,
            'paymentType' => 'CASH',
            'shippingMethod' => 'AIR',
            'shippingType' => 'BRANCH_DELIVERY',
            'weight' => 5.5,
            'length' => 30,
            'width' => 20,
            'height' => 10,
            'sendOneClick' => true,
            'description' => 'Handle with care.',
            'packages' => [
                [
                    'trackingNumber' => 'TRACK987654321',
                    'notes' => 'Fragile items, handle with care.',
                    'goods' => [
                        [
                            'categoryId' => 6203,
                            'quantity' => 2,
                            'link' => 'http://example.com/product-info',
                            'price' => 49.99,
                            'description' => 'Premium quality electronic component'
                        ]
                    ]
                ],
                [
                    'trackingNumber' => 'TRACK987654322',
                    'notes' => 'Fragile items, handle with care.',
                    'goods' => [
                        [
                            'categoryId' => 6203,
                            'quantity' => 3,
                            'link' => 'http://example.com/product-info',
                            'price' => 23.12,
                            'description' => 'Premium quality electronic component'
                        ]
                    ]
                ]
            ]
        ];

        return $this->integration->shipmentCreate($data);
    }

    public function labelGet()
    {
        return $this->integration->labelGet(7355218);
    }
}
