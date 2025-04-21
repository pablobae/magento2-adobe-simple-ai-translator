<?php
/**
 * SimpleAiTranslator
 *
 * Copyright (C) 2025 Pablo César Baenas Castelló - https://www.pablobaenas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Test\Unit\Service\Translator\Deepl;

use PHPUnit\Framework\TestCase;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use Pablobae\SimpleAiTranslator\Service\Translator\Deepl\ApiParametersBuilder;
use Exception;

class ApiParametersBuilderTest extends TestCase
{
    private ConfigProvider $mockConfigProvider;

    /**
     * Setup a mock ConfigProvider object.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockConfigProvider = $this->createMock(ConfigProvider::class);
    }

    /**
     * Test the buildParametersByTargetLanguage method with default provided configuration.
     */
    public function testBuildParametersByTargetLanguage(): void
    {
        // Arrange
        $this->mockConfigProvider->method('getDeeplDefaultSourceLang')->willReturn('en');
        $this->mockConfigProvider->method('getDeeplModelType')->willReturn('base');
        $this->mockConfigProvider->method('getDeeplSplitSentences')->willReturn('1');
        $this->mockConfigProvider->method('isDeeplPreserveFormattingEnabled')->willReturn(true);
        $this->mockConfigProvider->method('getDeeplFormality')->willReturn('formal');
        $this->mockConfigProvider->method('getDeeplTagHandling')->willReturn('html');
        $this->mockConfigProvider->method('isDeeplShowBilledCharactersEnabled')->willReturn(true);

        $builder = new ApiParametersBuilder($this->mockConfigProvider);

        // Act
        $params = $builder->buildParametersByTargetLanguage('FR');

        // Assert
        $this->assertArrayHasKey('target_lang', $params);
        $this->assertEquals('FR', $params['target_lang']);
        $this->assertArrayHasKey('source_lang', $params);
        $this->assertEquals('en', $params['source_lang']);
        $this->assertArrayHasKey('model', $params);
        $this->assertEquals('base', $params['model']);
        $this->assertArrayHasKey('split_sentences', $params);
        $this->assertEquals('1', $params['split_sentences']);
        $this->assertArrayHasKey('preserve_formatting', $params);
        $this->assertEquals(1, $params['preserve_formatting']);
        $this->assertArrayHasKey('formality', $params);
        $this->assertEquals('formal', $params['formality']);
        $this->assertArrayHasKey('tag_handling', $params);
        $this->assertEquals('html', $params['tag_handling']);
        $this->assertArrayHasKey('show_billed_characters', $params);
        $this->assertEquals(1, $params['show_billed_characters']);
    }

    /**
     * Test the buildParametersByStoreId method with valid configurations
     */
    public function testBuildParametersByStoreId(): void
    {
        // Arrange
        $storeId = '1';

        $this->mockConfigProvider->method('getDeeplApiKey')->with($storeId)->willReturn('dummy-key');
        $this->mockConfigProvider->method('getDeeplDefaultSourceLang')->with($storeId)->willReturn('EN');
        $this->mockConfigProvider->method('getDeeplDefaultTargetLang')->with($storeId)->willReturn('DE');

        $builder = new ApiParametersBuilder($this->mockConfigProvider);

        // Act
        $params = $builder->buildParametersByStoreId($storeId);

        // Assert
        $this->assertArrayHasKey('auth_key', $params);
        $this->assertEquals('dummy-key', $params['auth_key']);
        $this->assertArrayHasKey('source_lang', $params);
        $this->assertEquals('EN', $params['source_lang']);
        $this->assertArrayHasKey('target_lang', $params);
        $this->assertEquals('DE', $params['target_lang']);
    }

    /**
     * Test buildParametersByStoreId throws exception for missing API key
     */
    public function testBuildParametersByStoreIdThrowsExceptionForMissingApiKey(): void
    {
        // Arrange
        $storeId = '1';
        $this->mockConfigProvider->method('getDeeplApiKey')->with($storeId)->willReturn(null);

        $builder = new ApiParametersBuilder($this->mockConfigProvider);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing DeepL API key');

        // Act
        $builder->buildParametersByStoreId($storeId);
    }
}
