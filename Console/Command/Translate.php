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


namespace Pablobae\SimpleAiTranslator\Console\Command;

use Exception;
use Pablobae\SimpleAiTranslator\Service\Translator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Translate extends Command
{
    private const OPTION_TEXT = 'text';
    private const OPTION_TARGET_LANGUAGE = 'language';

    public function __construct(
        private readonly Translator $translator,
        ?string                     $name = null
    )
    {
        parent::__construct($name);
    }

    /**
     * Initialization of the command.
     */
    protected function configure()
    {
        $this->setName('pablobae:translate')
            ->setDescription('Translates text into the specified target language using several AI engines API.')
            ->addOption(
                self::OPTION_TEXT,
                null,
                InputOption::VALUE_REQUIRED,
                'Text to translate (enclosed in single quotes).'
            )
            ->addOption(
                self::OPTION_TARGET_LANGUAGE,
                null,
                InputOption::VALUE_REQUIRED,
                'Target language ISO code (e.g., "es", "fr").'
            );
        parent::configure();
    }

    /**
     * CLI command description.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $text = $input->getOption(self::OPTION_TEXT);
        $targetLanguage = $input->getOption(self::OPTION_TARGET_LANGUAGE);

        if (!$this->areOptionsValid($text, $targetLanguage)) {
            $output->writeln('<error>Both --text and --target_language options are required.</error>');
            return Command::FAILURE;
        }

        try {
            $translatedText = $this->translator->translateToLanguage($text, $targetLanguage);

            $output->writeln('<info>Original Text:</info> ' . $text);
            $output->writeln('<info>Translated Text:</info> ' . $translatedText);

            return Command::SUCCESS;
        } catch
        (Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    private function areOptionsValid(?string $text, ?string $targetLanguage): bool
    {
        if (!$text || !$targetLanguage) {
            return false;
        }
        return true;
    }
}
