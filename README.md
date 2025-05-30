# Simple AI Translator for Magento 2

---
SimpleAiTranslator is a Magento 2 extension that provides intelligent translation services by integrating with AI-powered APIs, including ChatGPT and DeepL, to offer easy and efficient multilingual content management.

![Supported Magento version](https://badgen.net/badge/magento/2.4.4-2.4.7+) [![Latest Stable Version](http://poser.pugx.org/pablobae/module-simple-ai-translator/v)](https://packagist.org/packages/pablobae/module-simple-ai-translator) [![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/pablobae/magento2-adobe-simple-ai-translator) [![License](http://poser.pugx.org/pablobae/module-simple-ai-translator/license)](https://packagist.org/packages/pablobae/module-simple-ai-translator) [![PHP Version Require](http://poser.pugx.org/pablobae/module-simple-ai-translator/require/php)](https://packagist.org/packages/pablobae/module-simple-ai-translator)

![alt Simple AI extension for Adobe/Magento 2](https://raw.githubusercontent.com/pablobae/markdown-images/master/magento2-adobe-simple-ai-translator/magento2-adobe-simple-ai-translator.gif?raw=true "Simple AI extension for Adobe/Magento 2")

---
## Features

- **ChatGPT Integration**: Translate text using OpenAI's powerful ChatGPT API.
- **DeepL Integration**: Utilize DeepL for high-quality translation.
- **Admin Configuration**: Fully configurable via Magento Admin (`Stores > Configuration`).
- **Unit Tests**: Ensures reliable performance and quality.
- **Error handling and logging**
- **Command-line** interface for translations
- **Unit Tests**: Ensures reliable performance and quality.

## Requirements

- Magento 2.4.x
- PHP 8.1 or higher
- DeepL API key (Free or Pro) and/or ChatGPT API key

## Installation

### Using Composer

```bash
composer require pablobae/module-simple-ai-translator
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

### Manual Installation

1. Create the following directory in your Magento installation:
   ```bash
   app/code/Pablobae/SimpleAiTranslator
   ```
2. Download the module and copy its contents to the above directory
3. Enable the module:
   ```bash
   bin/magento module:enable Pablobae_SimpleAiTranslator
   bin/magento setup:upgrade
   bin/magento setup:di:compile
   bin/magento cache:clean
   ```

## Configuration

Navigate to **Stores > Configuration > Pablobae Extensions > Simple AI Translator**

### Getting API Keys

#### DeepL API Key
1. Visit [DeepL API Account](https://www.deepl.com/pro-api)
2. Sign up for a DeepL API account (Free or Pro)
3. Access your API key from the account dashboard
4. Use api-free.deepl.com for Free API or api.deepl.com for Pro API

#### ChatGPT API Key
1. Visit [OpenAI Platform](https://platform.openai.com/)
2. Create or log into your OpenAI account
3. Navigate to API Keys section
4. Create a new secret key
5. Store the key securely as it won't be shown again

### Configuration Options

#### General
- Enable/Disable module
- Select AI Engine (DeepL or ChatGPT)

#### DeepL Settings
- API Key
- API Domain (Free/Pro)
- Default languages
- Advanced translation options

#### ChatGPT Settings
- API Key
- Model selection
- Temperature
- Default languages
- Request timeout

## Usage

### Admin Panel

The extension integrates with Magento's product management interface, allowing you to:
- Translate product descriptions
- Translate product attributes

### Command Line

Use the CLI command for batch translations:
```bash
bin/magento pablobae:simpleaitranslator:translate --text 'text to be translated' --language 'es'
```

### Programmatic Usage

```php
use Pablobae\SimpleAiTranslator\Service\Translator;

class YourClass {
    public function __construct(
        private readonly Translator $translator
    ) {}

    public function translate(string $text, ?string $storeId = null): string
    {
        return $this->translator->translate($text, $storeId);
    }
}
```

## Supported Languages

### DeepL API Languages
- English (US & UK)
- German
- French
- Spanish
- Italian
- Dutch
- Polish
- Portuguese (BR & PT)
- Russian
- Japanese
- Chinese
  And more...

### ChatGPT Languages
ChatGPT supports a broader range of languages including:
- All major European languages
- Asian languages (Chinese, Japanese, Korean)
- Arabic
- Hindi
- African languages
- Indigenous languages
  And many more, as ChatGPT can understand and generate content in most world languages.

Note: While ChatGPT supports more languages, DeepL typically provides more accurate translations for its supported language pairs.

## Error Handling

The extension includes comprehensive error handling for:
- Invalid/expired API keys
- Rate limits and quota exceeded
- Network timeouts
- Invalid language codes
- Malformed responses

## Testing

Unit tests are provided to verify the core functionalities of the API clients (ChatGPT and DeepL). To run the tests:

1. From the Magento root directory, execute:
   ```bash
   vendor/bin/phpunit -c dev/tests/unit/phpunit.xml
   ```

Make sure you have PHPUnit installed and configured for Magento.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

For issues and feature requests, please [create an issue](https://github.com/pablobae/magento2-simple-ai-translator/issues).

Alternatively, you can also join my [Slack channel](https://join.slack.com/t/aradaen/shared_invite/zt-182u0exau-Kt9YRcDG8QqRZbOyskFXaQ)

## License
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

This project is licensed under the GNU General Public License v3.0 or later. See the [LICENSE](./LICENSE) file for details.


## Credits

Developed by Pablo César Baenas Castelló (while testing AI agents) <br/>
[www.pablobaenas.com](https://www.pablobaenas.com) | [www.aradaen.com](https://www.aradaen.com)
