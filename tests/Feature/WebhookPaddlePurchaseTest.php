<?php

use App\Jobs\HandlePaddlePurchaseJob;
use Illuminate\Support\Carbon;
use Spatie\WebhookClient\Models\WebhookCall;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertSame;

it('can create a valid Paddle webhook signature', function () {
    // Arrange
    $originalTimestamp = 1718139311;
    [$originalArrBody, $originalSigHeader, $originalRawJsonBody] = getValidPaddleWebhookRequest();

    // Assert
    [$body, $header] = generateValidSignedPaddleWebhookRequest($originalArrBody, $originalTimestamp);
    assertSame(json_encode($body), $originalRawJsonBody);
    assertSame($header, $originalSigHeader);
});

it('stores a paddle purchase request', function () {
    // Arrange
    Queue::fake();
    assertDatabaseCount(WebhookCall::class, 0);
    [$arrData] = getValidPaddleWebhookRequest();

    // We will have to generate a fresh signature because the timestamp cannot be older
    // than 5 seconds, or our webhook signature validator middleware will block the request
    [$requestBody, $requestHeaders] = generateValidSignedPaddleWebhookRequest($arrData);

    // Act
    // needed to prevent the checkout url slashes from being escaped
    postJson('webhooks', $requestBody, $requestHeaders);

    // Assert
    assertDatabaseCount(WebhookCall::class, 1);

});

it('does not store invalid paddle purchase request', function () {
    // Arrange
    assertDatabaseCount(WebhookCall::class, 0);

    // Act
    post('webhooks', getInvalidPaddleWebhookRequest());

    // Assert
    assertDatabaseCount(WebhookCall::class, 0);

});

it('dispatches a job for a valid paddle request', function () {
    // Arrange
    Queue::fake();

    // Act
    [$arrData] = getValidPaddleWebhookRequest();
    [$requestBody, $requestHeaders] = generateValidSignedPaddleWebhookRequest($arrData);
    postJson('webhooks', $requestBody, $requestHeaders);

    // Assert
    Queue::assertPushed(HandlePaddlePurchaseJob::class);

});

it('does not dispatch a job for invalid paddle request', function () {
    // Arrange
    Queue::fake();

    // Act
    post('webhooks', getInvalidPaddleWebhookRequest());

    // Assert
    Queue::assertNotPushed(HandlePaddlePurchaseJob::class);
});

function getValidPaddleWebhookRequest(): array
{
    $sigHeader = ['Paddle-Signature' => 'ts=1718139311;h1=8a46fdcc3c91acde3cf8c5b0a522ec6f3c7b42417adff9755c782ae2a7978fda'];

    $parsedData = [
        'event_id' => 'evt_01jhqxaer8cjdkgrtdwmxz7spx',
        'event_type' => 'transaction.completed',
        'occurred_at' => '2025-01-16T15:57:12.840792Z',
        'notification_id' => 'ntf_01jhqxaexajec24bn97ezkkzqg',
        'data' => [
            'id' => 'txn_01jhqx6gt9h4yseyxtb7h1n3dc',
            'items' => [
                [
                    'price' => [
                        'id' => 'pri_01j449tat6p71xg1yx22pwnrjt',
                        'name' => 'laravel-for-beginners-price',
                        'type' => 'standard',
                        'status' => 'active',
                        'quantity' => [
                            'maximum' => 10000,
                            'minimum' => 1,
                        ],
                        'tax_mode' => 'account_setting',
                        'created_at' => '2024-07-31T11:46:43.654835Z',
                        'product_id' => 'pro_01j449j1rwpm6e7y7ts4mp2wn4',
                        'unit_price' => [
                            'amount' => '1500',
                            'currency_code' => 'EUR',
                        ],
                        'updated_at' => '2024-08-02T16:08:12.209358Z',
                        'custom_data' => null,
                        'description' => 'pago unico',
                        'trial_period' => null,
                        'billing_cycle' => null,
                        'unit_price_overrides' => [
                        ],
                    ],
                    'price_id' => 'pri_01j449tat6p71xg1yx22pwnrjt',
                    'quantity' => 1,
                    'proration' => null,
                ],
            ],
            'origin' => 'web',
            'status' => 'completed',
            'details' => [
                'totals' => [
                    'fee' => '124',
                    'tax' => '260',
                    'total' => '1500',
                    'credit' => '0',
                    'balance' => '0',
                    'discount' => '0',
                    'earnings' => '1116',
                    'subtotal' => '1240',
                    'grand_total' => '1500',
                    'currency_code' => 'EUR',
                    'credit_to_balance' => '0',
                ],
                'line_items' => [
                    [
                        'id' => 'txnitm_01jhqx7sw4wz9b9cz0n5cmjt86',
                        'totals' => [
                            'tax' => '260',
                            'total' => '1500',
                            'discount' => '0',
                            'subtotal' => '1240',
                        ],
                        'item_id' => null,
                        'product' => [
                            'id' => 'pro_01j449j1rwpm6e7y7ts4mp2wn4',
                            'name' => 'Laravel For Beginners',
                            'type' => 'standard',
                            'status' => 'active',
                            'image_url' => null,
                            'created_at' => '2024-07-31T11:42:12.252Z',
                            'updated_at' => '2024-08-05T11:45:29.476Z',
                            'custom_data' => [
                                'name' => null,
                                'email' => null,
                            ],
                            'description' => 'Laravel For Beginners',
                            'tax_category' => 'standard',
                        ],
                        'price_id' => 'pri_01j449tat6p71xg1yx22pwnrjt',
                        'quantity' => 1,
                        'tax_rate' => '0.21',
                        'unit_totals' => [
                            'tax' => '260',
                            'total' => '1500',
                            'discount' => '0',
                            'subtotal' => '1240',
                        ],
                        'is_tax_exempt' => false,
                        'revised_tax_exempted' => false,
                    ],
                ],
                'payout_totals' => [
                    'fee' => '125',
                    'tax' => '262',
                    'total' => '1512',
                    'credit' => '0',
                    'balance' => '0',
                    'discount' => '0',
                    'earnings' => '1125',
                    'fee_rate' => '0.05',
                    'subtotal' => '1250',
                    'grand_total' => '1512',
                    'currency_code' => 'USD',
                    'exchange_rate' => '1.0081210999999999',
                    'credit_to_balance' => '0',
                ],
                'tax_rates_used' => [
                    [
                        'totals' => [
                            'tax' => '260',
                            'total' => '1500',
                            'discount' => '0',
                            'subtotal' => '1240',
                        ],
                        'tax_rate' => '0.21',
                    ],
                ],
                'adjusted_totals' => [
                    'fee' => '124',
                    'tax' => '260',
                    'total' => '1500',
                    'earnings' => '1116',
                    'subtotal' => '1240',
                    'grand_total' => '1500',
                    'currency_code' => 'EUR',
                ],
            ],
            'checkout' => [
                'url' => 'https://localhost?_ptxn=txn_01jhqx6gt9h4yseyxtb7h1n3dc',
            ],
            'payments' => [
                [
                    'amount' => '1500',
                    'status' => 'captured',
                    'created_at' => '2025-01-16T15:57:07.718255Z',
                    'error_code' => null,
                    'captured_at' => '2025-01-16T15:57:09.815395Z',
                    'method_details' => [
                        'card' => [
                            'type' => 'visa',
                            'last4' => '4242',
                            'expiry_year' => 2025,
                            'expiry_month' => 5,
                            'cardholder_name' => 'Pepito palotes',
                        ],
                        'type' => 'card',
                    ],
                    'payment_method_id' => 'paymtd_01jhqxa9qd0vzhet4z0tv3jq9j',
                    'payment_attempt_id' => '70e25efd-7115-43dc-8dc0-cb7de87882ad',
                    'stored_payment_method_id' => '5046da5e-8d64-4b3b-abdd-46b0e3a828b9',
                ],
            ],
            'billed_at' => '2025-01-16T15:57:10.121533Z',
            'address_id' => 'add_01jhqx7smjns4n9cwf75x0zc5m',
            'created_at' => '2025-01-16T15:55:03.964925Z',
            'invoice_id' => 'inv_01jhqxacja1jr0e2rwt29tv67w',
            'updated_at' => '2025-01-16T15:57:12.283350561Z',
            'business_id' => null,
            'custom_data' => null,
            'customer_id' => 'ctm_01jhqx7sm3ybw77xa7wpj5q9sj',
            'discount_id' => null,
            'receipt_data' => null,
            'currency_code' => 'EUR',
            'billing_period' => null,
            'invoice_number' => '8169-10008',
            'billing_details' => null,
            'collection_mode' => 'automatic',
            'subscription_id' => null,
        ],
    ];

    $rawJsonBody = '{"event_id":"evt_01jhqxaer8cjdkgrtdwmxz7spx","event_type":"transaction.completed","occurred_at":"2025-01-16T15:57:12.840792Z","notification_id":"ntf_01jhqxaexajec24bn97ezkkzqg","data":{"id":"txn_01jhqx6gt9h4yseyxtb7h1n3dc","items":[{"price":{"id":"pri_01j449tat6p71xg1yx22pwnrjt","name":"laravel-for-beginners-price","type":"standard","status":"active","quantity":{"maximum":10000,"minimum":1},"tax_mode":"account_setting","created_at":"2024-07-31T11:46:43.654835Z","product_id":"pro_01j449j1rwpm6e7y7ts4mp2wn4","unit_price":{"amount":"1500","currency_code":"EUR"},"updated_at":"2024-08-02T16:08:12.209358Z","custom_data":null,"description":"pago unico","trial_period":null,"billing_cycle":null,"unit_price_overrides":[]},"price_id":"pri_01j449tat6p71xg1yx22pwnrjt","quantity":1,"proration":null}],"origin":"web","status":"completed","details":{"totals":{"fee":"124","tax":"260","total":"1500","credit":"0","balance":"0","discount":"0","earnings":"1116","subtotal":"1240","grand_total":"1500","currency_code":"EUR","credit_to_balance":"0"},"line_items":[{"id":"txnitm_01jhqx7sw4wz9b9cz0n5cmjt86","totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"item_id":null,"product":{"id":"pro_01j449j1rwpm6e7y7ts4mp2wn4","name":"Laravel For Beginners","type":"standard","status":"active","image_url":null,"created_at":"2024-07-31T11:42:12.252Z","updated_at":"2024-08-05T11:45:29.476Z","custom_data":{"name":null,"email":null},"description":"Laravel For Beginners","tax_category":"standard"},"price_id":"pri_01j449tat6p71xg1yx22pwnrjt","quantity":1,"tax_rate":"0.21","unit_totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"is_tax_exempt":false,"revised_tax_exempted":false}],"payout_totals":{"fee":"125","tax":"262","total":"1512","credit":"0","balance":"0","discount":"0","earnings":"1125","fee_rate":"0.05","subtotal":"1250","grand_total":"1512","currency_code":"USD","exchange_rate":"1.0081210999999999","credit_to_balance":"0"},"tax_rates_used":[{"totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"tax_rate":"0.21"}],"adjusted_totals":{"fee":"124","tax":"260","total":"1500","earnings":"1116","subtotal":"1240","grand_total":"1500","currency_code":"EUR"}},"checkout":{"url":"https:\/\/localhost?_ptxn=txn_01jhqx6gt9h4yseyxtb7h1n3dc"},"payments":[{"amount":"1500","status":"captured","created_at":"2025-01-16T15:57:07.718255Z","error_code":null,"captured_at":"2025-01-16T15:57:09.815395Z","method_details":{"card":{"type":"visa","last4":"4242","expiry_year":2025,"expiry_month":5,"cardholder_name":"Pepito palotes"},"type":"card"},"payment_method_id":"paymtd_01jhqxa9qd0vzhet4z0tv3jq9j","payment_attempt_id":"70e25efd-7115-43dc-8dc0-cb7de87882ad","stored_payment_method_id":"5046da5e-8d64-4b3b-abdd-46b0e3a828b9"}],"billed_at":"2025-01-16T15:57:10.121533Z","address_id":"add_01jhqx7smjns4n9cwf75x0zc5m","created_at":"2025-01-16T15:55:03.964925Z","invoice_id":"inv_01jhqxacja1jr0e2rwt29tv67w","updated_at":"2025-01-16T15:57:12.283350561Z","business_id":null,"custom_data":null,"customer_id":"ctm_01jhqx7sm3ybw77xa7wpj5q9sj","discount_id":null,"receipt_data":null,"currency_code":"EUR","billing_period":null,"invoice_number":"8169-10008","billing_details":null,"collection_mode":"automatic","subscription_id":null}}';

    return [$parsedData, $sigHeader, $rawJsonBody];
}

function generateValidSignedPaddleWebhookRequest(array $data, ?int $timestamp = null): array
{
    $ts = $timestamp ?? Carbon::now()->unix();
    $secret = config('services.paddle.notification-endpoint-secret-key');

    $rawJsonBody = json_encode($data);

    $calculatedSig = hash_hmac('sha256', "{$ts}:{$rawJsonBody}", $secret);

    $header = [
        'Paddle-Signature' => "ts={$ts};h1={$calculatedSig}",
    ];

    return [$data, $header];
}

function getInvalidPaddleWebhookRequest(): array
{
    return [];
}
