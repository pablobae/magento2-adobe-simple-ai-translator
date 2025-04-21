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

namespace Pablobae\SimpleAiTranslator\Test\Unit\Service\Translator\ChatGpt;

use Pablobae\SimpleAiTranslator\Service\Translator\ChatGpt\PromptBuilder;
use PHPUnit\Framework\TestCase;

class PromptBuilderTest extends TestCase
{
    /**
     * @var PromptBuilder
     */
    private $promptBuilder;

    /**
     * Set up test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        // Direct instantiation without ObjectManager
        $this->promptBuilder = new PromptBuilder();
    }

    /**
     * Test building translation prompt with both source and target languages
     *
     * @dataProvider buildTranslationPromptDataProvider
     * @param string $text
     * @param string $targetLang
     * @param string|null $sourceLang
     * @param array $expectedPrompt
     * @return void
     */
    public function testBuildTranslationPrompt(
        string  $text,
        string  $targetLang,
        ?string $sourceLang,
        array   $expectedPrompt
    ): void
    {
        $result = $this->promptBuilder->buildTranslationPrompt($text, $targetLang, $sourceLang);
        $this->assertIsArray($result);
        $this->assertEquals($expectedPrompt, $result);
    }

    /**
     * Data provider for testBuildTranslationPrompt
     *
     * @return array
     */
    public function buildTranslationPromptDataProvider(): array
    {
        return [
            'with_source_language' => [
                'text' => 'Hello world',
                'targetLang' => 'es',
                'sourceLang' => 'en',
                'expectedPrompt' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional translator. Translate the text exactly as provided, maintaining any HTML or XML tags if present. Only return the translated text without any explanations or additional content. The text to be translated should be added between /// and ///. Do not include the /// in the translation.'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Translate the following text to es from en: ///Hello world///'
                    ]
                ]
            ],
            'without_source_language' => [
                'text' => 'Hello world',
                'targetLang' => 'fr',
                'sourceLang' => null,
                'expectedPrompt' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional translator. Translate the text exactly as provided, maintaining any HTML or XML tags if present. Only return the translated text without any explanations or additional content. The text to be translated should be added between /// and ///. Do not include the /// in the translation.'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Translate the following text to fr: ///Hello world///'
                    ]
                ]
            ],
            'with_special_characters' => [
                'text' => "Hello 'world'",
                'targetLang' => 'de',
                'sourceLang' => 'en',
                'expectedPrompt' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional translator. Translate the text exactly as provided, maintaining any HTML or XML tags if present. Only return the translated text without any explanations or additional content. The text to be translated should be added between /// and ///. Do not include the /// in the translation.'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Translate the following text to de from en: ///Hello \'world\'///'
                    ]
                ]
            ],
            'with_multiline_text' => [
                'text' => "Hello\nworld",
                'targetLang' => 'it',
                'sourceLang' => 'en',
                'expectedPrompt' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional translator. Translate the text exactly as provided, maintaining any HTML or XML tags if present. Only return the translated text without any explanations or additional content. The text to be translated should be added between /// and ///. Do not include the /// in the translation.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Translate the following text to it from en: ///Hello\nworld///"
                    ]
                ]
            ],
            'empty_text' => [
                'text' => '',
                'targetLang' => 'pt',
                'sourceLang' => 'en',
                'expectedPrompt' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional translator. Translate the text exactly as provided, maintaining any HTML or XML tags if present. Only return the translated text without any explanations or additional content. The text to be translated should be added between /// and ///. Do not include the /// in the translation.'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Translate the following text to pt from en: //////'
                    ]
                ]
            ]
        ];
    }

    /**
     * Test building translation prompt with only target language
     *
     * @return void
     */
    public function testBuildTranslationPromptWithoutSourceLang(): void
    {
        $text = 'Hello world';
        $targetLang = 'es';

        $result = $this->promptBuilder->buildTranslationPrompt($text, $targetLang);

        $expectedPrompt = [

            [
                'role' => 'system',
                'content' => 'You are a professional translator. Translate the text exactly as provided, maintaining any HTML or XML tags if present. Only return the translated text without any explanations or additional content. The text to be translated should be added between /// and ///. Do not include the /// in the translation.'
            ],
            [
                'role' => 'user',
                'content' => 'Translate the following text to es: ///Hello world///'
            ]
        ];

        $this->assertIsArray($result);
        $this->assertEquals($expectedPrompt, $result);
    }

    /**
     * Test building translation prompt with HTML content
     *
     * @return void
     */
    public function testBuildTranslationPromptWithHtmlContent(): void
    {
        $text = '<p>Hello <strong>world</strong></p>';
        $targetLang = 'es';
        $sourceLang = 'en';

        $result = $this->promptBuilder->buildTranslationPrompt($text, $targetLang, $sourceLang);

        $expectedPrompt = [
            [
                'role' => 'system',
                'content' => 'You are a professional translator. Translate the text exactly as provided, maintaining any HTML or XML tags if present. Only return the translated text without any explanations or additional content. The text to be translated should be added between /// and ///. Do not include the /// in the translation.'
            ],
            [
                'role' => 'user',
                'content' => 'Translate the following text to es from en: ///<p>Hello <strong>world</strong></p>///'
            ]
        ];

        $this->assertIsArray($result);
        $this->assertEquals($expectedPrompt, $result);
    }

    /**
     * Test building translation prompt with long text
     *
     * @return void
     */
    public function testBuildTranslationPromptWithLongText(): void
    {
        $text = str_repeat('Lorem ipsum dolor sit amet. ', 20);
        $targetLang = 'es';
        $sourceLang = 'en';

        $result = $this->promptBuilder->buildTranslationPrompt($text, $targetLang, $sourceLang);

        $expectedPrompt = [
            [
                'role' => 'system',
                'content' => 'You are a professional translator. Translate the text exactly as provided, maintaining any HTML or XML tags if present. Only return the translated text without any explanations or additional content. The text to be translated should be added between /// and ///. Do not include the /// in the translation.'
            ],
            [
                'role' => 'user',
                'content' => 'Translate the following text to es from en: ///' . $text . '///'
            ]
        ];

        $this->assertIsArray($result);
        $this->assertEquals($expectedPrompt, $result);
    }
}
