<?php
/**
 *
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\GroupedProduct\Controller\Adminhtml\Product\Initialization\Helper\ProductLinks\Plugin;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Controller\Adminhtml\Product\Initialization\Helper\ProductLinks\Plugin\Grouped
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getGroupedReadonly', 'setGroupedLinkData', '__wakeup'),
            array(),
            '',
            false
        );
        $this->subjectMock = $this->getMock(
            'Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\ProductLinks',
            array(),
            array(),
            '',
            false
        );
        $this->model = new Grouped($this->requestMock);
    }

    public function testAfterInitializeLinksRequestDoesNotHaveGrouped()
    {
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getPost'
        )->with(
            'links'
        )->will(
            $this->returnValue(array())
        );
        $this->productMock->expects($this->never())->method('getGroupedReadonly');
        $this->productMock->expects($this->never())->method('setGroupedLinkData');
        $this->assertEquals(
            $this->productMock,
            $this->model->afterInitializeLinks($this->subjectMock, $this->productMock)
        );
    }

    public function testAfterInitializeLinksRequestHasGrouped()
    {
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getPost'
        )->with(
            'links'
        )->will(
            $this->returnValue(array('grouped' => 'value'))
        );

        $this->productMock->expects($this->once())->method('getGroupedReadonly')->will($this->returnValue(false));
        $this->productMock->expects($this->once())->method('setGroupedLinkData')->with(array('value'));
        $this->assertEquals(
            $this->productMock,
            $this->model->afterInitializeLinks($this->subjectMock, $this->productMock)
        );
    }

    public function testAfterInitializeLinksProductIsReadonly()
    {
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getPost'
        )->with(
            'links'
        )->will(
            $this->returnValue(array('grouped' => 'value'))
        );

        $this->productMock->expects($this->once())->method('getGroupedReadonly')->will($this->returnValue(true));
        $this->productMock->expects($this->never())->method('setGroupedLinkData');
        $this->assertEquals(
            $this->productMock,
            $this->model->afterInitializeLinks($this->subjectMock, $this->productMock)
        );
    }
}
