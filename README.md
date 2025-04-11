# Pablobae SimpleAiTranslator for Magento 2

SimpleAiTranslator is a Magento 2 extension that provides automated translation capabilities using AI-powered translation services. Currently, it supports DeepL's translation API, with the possibility to extend to other AI translation services.

## Features

- Integration with DeepL API for high-quality AI translations
- Support for both DeepL API Free and Pro versions
- Automatic language detection from store locale
- Configurable target language settings
- Store-specific configurations
- Command-line interface for translations
- Integration with Magento's product data provider

## Requirements

- Magento 2.4.x
- PHP 7.4 or higher
- DeepL API key (Free or Pro)

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

1. Go to **Stores > Configuration > Pablobae Extensions > Simple AI Translator**
2. Configure the following settings:
   - Enable/Disable the module
   - Select AI Engine (DeepL)
   - Enter your DeepL API Key
   - Choose DeepL API domain (api.deepl.com for Pro, api-free.deepl.com for Free)
   - Set default target language (optional)
   - Configure request timeout

## Usage

### Admin Panel

The extension integrates with Magento's product management interface, allowing you to:
- Translate product descriptions
- Translate product attributes

### Command Line

Use the CLI command for batch translations:
```bash
bin/magento pablobae:simpleaitranslator:translate [text] [options]
```

### Programmatic Usage

```php
use Pablobae\SimpleAiTranslator\Service\Translator;

class YourClass {
    private $translator;

    public function __construct(Translator $translator) {
        $this->translator = $translator;
    }

    public function translate($text, $storeId) {
        return $this->translator->translate($text, $storeId);
    }
}
```

## Supported Languages

The extension supports all languages available in the DeepL API, including:
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

## Error Handling

The extension includes comprehensive error handling for:
- Missing API keys
- Invalid API responses
- Network timeouts
- Invalid language codes

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

If you encounter any issues or have questions, please:
1. Check the [issues page](https://github.com/pablobae/magento2-simple-ai-translator/issues)
2. Create a new issue if your problem isn't already listed

## License

[MIT License](LICENSE.md)

## Credits

Developed by Pablo Baenas
