<?php
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
