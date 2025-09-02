<?php

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

class ProductCest
{
    public function testGetAllProducts(ApiTester $I)
    {
        $I->sendGET('/api/products');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"success":true');
        $I->seeResponseJsonMatchesJsonPath('$.data[*].name');
        $I->seeResponseJsonMatchesJsonPath('$.data[*].price');
    }
    public function testGetSingleProduct(ApiTester $I)
    {
        $I->sendGET('/api/products/1');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }
    public function testProductResponse(ApiTester $I)
    {
        $I->sendGET('/api/products/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseJsonMatchesJsonPath('$.data.name');
    }
    public function testSpecificProduct(ApiTester $I)
    {
        $I->sendGET('/api/products/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('"name":"Wireless Headphones"');
        $I->seeResponseContains('"price":"199.99"');
    }
    public function testGetProductsWithWrongMethod(ApiTester $I)
    {
        $I->sendPOST('/api/products');
        $I->seeResponseCodeIs(200);   
    }
    public function testProductNotFound(ApiTester $I)
    {
        $I->sendGET('/api/products/999999');
        $I->seeResponseIsJson();
    }
    public function testProductCategories(ApiTester $I)
    {
        $I->sendGET('/api/product/categories');
        $I->seeResponseCodeIs(200);
    }
    public function testProductSearch(ApiTester $I)
    {
        $I->sendGET('/api/products/search?q=laptop');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    public function testProductPricing(ApiTester $I)
    {
        $I->sendGET('/api/products/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('"stock_quantity":"50"');
        $I->seeResponseContains('"category_id":"1"'); 
    }
    public function testProductListPagination(ApiTester $I)
    {
        $I->sendGET('/api/products?limit=5&offset=0');
        $I->seeResponseCodeIs(200);
        $response1 = $I->grabResponse();
        
        $I->sendGET('/api/products?limit=5&offset=5');
        $response2 = $I->grabResponse();

        $I->assertNotEquals($response1, $response2);
    }
}
