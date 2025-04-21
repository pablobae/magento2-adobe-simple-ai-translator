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

namespace Pablobae\SimpleAiTranslator\Controller\Adminhtml\Translate;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Pablobae\SimpleAiTranslator\Service\Translator;

class Index extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Pablobae_SimpleAiTranslator::translate';

    public function __construct(
        Context $context,
        private Validator $formKeyValidator,
        private JsonFactory $resultJsonFactory,
        private Translator $translator,
        private LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    public function execute(): Json
    {
        $resultJson = $this->resultJsonFactory->create();
        try {
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                throw new LocalizedException(__('Invalid form key. Please refresh the page.'));
            }

            $params = $this->getRequest()->getParams();
            $text = trim($params['text'] ?? '');
            $storeId = (int)($params['storeId'] ?? -1);

            $this->logger->info('Translation request:', ['text' => $text, 'storeId' => $storeId]);

            if (empty($text)) {
                throw new LocalizedException(__('Text parameter is required.'));
            }

            if ($storeId < 0) {
                throw new LocalizedException(__('Valid Store ID parameter is required.'));
            }

            $translatedText = $this->translator->translate($text, (string) $storeId);
            $this->logger->info('Translation successful', ['result' => $translatedText]);

            return $resultJson->setData(['success' => true, 'translation' => $translatedText]);
        } catch (LocalizedException $e) {
            $this->logger->error('Translation validation error: ' . $e->getMessage(), [
                'text' => $text ?? '',
                'storeId' => $storeId ?? '',
            ]);
            return $resultJson->setData(['success' => false, 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->logger->error('Translation error: ' . $e->getMessage(), [
                'text' => $text ?? '',
                'storeId' => $storeId ?? '',
                'trace' => $e->getTraceAsString()
            ]);
            return $resultJson->setData([
                'success' => false,
                'message' => __('An error occurred while processing your request. Please try again.')
            ]);
        }
    }
}
