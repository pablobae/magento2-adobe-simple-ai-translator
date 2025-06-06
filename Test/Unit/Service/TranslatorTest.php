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

namespace Pablobae\SimpleAiTranslator\Test\Unit\Service;

use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Pablobae\SimpleAiTranslator\Api\TranslatorAdapterInterface;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use Pablobae\SimpleAiTranslator\Service\Translator;
use RuntimeException;

class TranslatorTest extends TestCase
{
    private ConfigProvider $configProvider;
    private Translator $translator;

    protected function setUp(): void
    {
        $this->configProvider = $this->createMock(ConfigProvider::class);
        $this->translator = new Translator($this->configProvider, []);
    }

    public function testTranslateSuccess(): void
    {
        $storeId = '1';
        $text = 'Hello';
        $translatedText = 'Hola';
        $aiEngine = 'deepl';

        $adapter = $this->createMock(TranslatorAdapterInterface::class);
        $adapter->expects($this->once())
            ->method('translate')
            ->with($text, $storeId)
            ->willReturn($translatedText);

        $this->configProvider->expects($this->once())
            ->method('isModuleEnabled')
            ->with($storeId)
            ->willReturn(true);

        $this->configProvider->expects($this->once())
            ->method('getAiEngine')
            ->willReturn($aiEngine);

        $translator = new Translator($this->configProvider, [$aiEngine => $adapter]);
        $result = $translator->translate($text, $storeId);

        $this->assertEquals($translatedText, $result);
    }

    public function testTranslateWhenModuleDisabled(): void
    {
        $storeId = '1';
        $text = 'Hello';

        $this->configProvider->expects($this->once())
            ->method('isModuleEnabled')
            ->with($storeId)
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Extension is not enabled');

        $this->translator->translate($text, $storeId);
    }

    public function testTranslateWhenAdapterNotFound(): void
    {
        $storeId = '1';
        $text = 'Hello';
        $aiEngine = 'unknown';

        $this->configProvider->expects($this->once())
            ->method('isModuleEnabled')
            ->with($storeId)
            ->willReturn(true);

        $this->configProvider->expects($this->once())
            ->method('getAiEngine')
            ->willReturn($aiEngine);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage("Translation adapter 'unknown' not found.");

        $this->translator->translate($text, $storeId);
    }

    public function testTranslateToLanguageSuccess(): void
    {
        $text = 'Hello';
        $targetLang = 'es';
        $translatedText = 'Hola';
        $aiEngine = 'deepl';

        $adapter = $this->createMock(TranslatorAdapterInterface::class);
        $adapter->expects($this->once())
            ->method('translateToLanguage')
            ->with($text, $targetLang)
            ->willReturn($translatedText);

        $this->configProvider->expects($this->once())
            ->method('isModuleEnabled')
            ->willReturn(true);

        $this->configProvider->expects($this->once())
            ->method('getAiEngine')
            ->willReturn($aiEngine);

        $translator = new Translator($this->configProvider, [$aiEngine => $adapter]);
        $result = $translator->translateToLanguage($text, $targetLang);

        $this->assertEquals($translatedText, $result);
    }

    public function testTranslateToLanguageWhenModuleDisabled(): void
    {
        $text = 'Hello';
        $targetLang = 'es';

        $this->configProvider->expects($this->once())
            ->method('isModuleEnabled')
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Extension is not enabled');

        $this->translator->translateToLanguage($text, $targetLang);
    }

    public function testTranslateToLanguageWhenAdapterNotFound(): void
    {
        $text = 'Hello';
        $targetLang = 'es';
        $aiEngine = 'unknown';

        $this->configProvider->expects($this->once())
            ->method('isModuleEnabled')
            ->willReturn(true);

        $this->configProvider->expects($this->once())
            ->method('getAiEngine')
            ->willReturn($aiEngine);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage("Translation adapter 'unknown' not found.");

        $this->translator->translateToLanguage($text, $targetLang);
    }

    public function testTranslateWithMultipleAdapters(): void
    {
        $storeId = '1';
        $text = 'Hello';
        $translatedText = 'Hola';
        $aiEngine = 'deepl';

        $deeplAdapter = $this->createMock(TranslatorAdapterInterface::class);
        $chatGptAdapter = $this->createMock(TranslatorAdapterInterface::class);

        $deeplAdapter->expects($this->once())
            ->method('translate')
            ->with($text, $storeId)
            ->willReturn($translatedText);

        $chatGptAdapter->expects($this->never())
            ->method('translate');

        $this->configProvider->expects($this->once())
            ->method('isModuleEnabled')
            ->with($storeId)
            ->willReturn(true);

        $this->configProvider->expects($this->once())
            ->method('getAiEngine')
            ->willReturn($aiEngine);

        $translator = new Translator(
            $this->configProvider,
            [
                'deepl' => $deeplAdapter,
                'chatgpt' => $chatGptAdapter
            ]
        );

        $result = $translator->translate($text, $storeId);
        $this->assertEquals($translatedText, $result);
    }
}
