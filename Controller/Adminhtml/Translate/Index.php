<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Controller\Adminhtml\Translate;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Pablobae\SimpleAiTranslator\Service\Translator;
use Psr\Log\LoggerInterface;

class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Pablobae_SimpleAiTranslator::translate';


    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Translator $translatorService
     * @param LoggerInterface $logger
     */
    public function __construct(
        private Context         $context,
        private JsonFactory     $resultJsonFactory,
        private Translator      $translatorService,
        private LoggerInterface $logger
    )
    {
        parent::__construct($context);

    }

    /**
     * Execute method for handling translation requests
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json */
        $result = $this->resultJsonFactory->create();

        try {
            $fieldValue = trim($this->getRequest()->getParam('text'));
            $storeId = trim($this->getRequest()->getParam('storeId'));

            if (!$fieldValue || $storeId === null) {
                return $result->setHttpResponseCode(400)->setData([
                    'success' => false,
                    'message' => __('Invalid input data.')
                ]);
            }

            $translatedValue = $this->translatorService->translate($fieldValue, $storeId);

            return $result->setData([
                'success' => true,
                'translatedValue' => $translatedValue
            ]);
        } catch (Exception $e) {
            $this->logger->error('Translation Error: ' . $e->getMessage());
            return $result->setHttpResponseCode(500)->setData([
                'success' => false,
                'message' => __('An error occurred during translation. ') . $e->getMessage()
            ]);
        }
    }
}
