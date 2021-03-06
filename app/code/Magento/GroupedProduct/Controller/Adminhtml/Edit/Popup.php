<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GroupedProduct\Controller\Adminhtml\Edit;

class Popup extends \Magento\Backend\App\AbstractAction
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $factory;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $factory
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $factory,
        \Magento\Framework\Logger $logger
    ) {
        $this->registry = $registry;
        $this->factory = $factory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }

    /**
     * Get associated grouped products grid popup
     *
     * @return void
     */
    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('id');

        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->factory->create();
        $product->setStoreId($this->getRequest()->getParam('store', 0));

        $typeId = $this->getRequest()->getParam('type');
        if (!$productId && $typeId) {
            $product->setTypeId($typeId);
        }
        $product->setData('_edit_mode', true);

        if ($productId) {
            try {
                $product->load($productId);
            } catch (\Exception $e) {
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
                $this->logger->logException($e);
            }
        }

        $setId = (int)$this->getRequest()->getParam('set');
        if ($setId) {
            $product->setAttributeSetId($setId);
        }
        $this->registry->register('current_product', $product);

        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
