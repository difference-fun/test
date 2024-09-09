<?php

// Example test case
test('example test', function () {
    expect(true)->toBeTrue();
});

// Test for a function
test('addition works correctly', function () {
    $result = 1 + 1;
    expect($result)->toBe(2);
});

// Grouping related tests using describe
describe('String operations', function () {
    test('string contains substring', function () {
        $string = "Hello, world!";
        expect($string)->toContain("world");
    });

    test('string length is correct', function () {
        $string = "Hello, world!";
        expect(strlen($string))->toBe(13);
    });
});