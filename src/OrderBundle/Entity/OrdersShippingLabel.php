<?php

namespace OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * OrderProductVariant
 *
 * @ORM\Table(name="orders_shipping_labels")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @Assert\Callback(methods={"isFileUploadedOrExists"})
 */
class OrdersShippingLabel
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $tracking_number;

//    /**
//     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\Orders", inversedBy="shipping_labels")
//     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
//     */
//    private $order;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="OrdersProductVariant", inversedBy="shipping_labels")
//     */
//    private $orders_product_variant;

    /**
     * @ORM\ManyToOne(targetEntity="OrdersWarehouseInfo", inversedBy="shipping_labels")
     */
    private $orders_warehouse_info;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if(null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        $tmp = __DIR__ . '/../../../web/' . $this->getUploadDir();
        return $tmp;
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/shipping';
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getTrackingNumber()
    {
        return $this->tracking_number;
    }

    /**
     * @param mixed $tracking_number
     */
    public function setTrackingNumber($tracking_number)
    {
        $this->tracking_number = $tracking_number;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }


    /**
     * Set orderProductVariant
     *
     * @param \OrderBundle\Entity\OrdersProductVariant $orderProductVariant
     *
     * @return OrdersShippingLabel
     */
    public function setOrdersProductVariant(\OrderBundle\Entity\OrdersProductVariant $orderProductVariant = null)
    {
        $this->orders_product_variant = $orderProductVariant;

        return $this;
    }

    /**
     * Get orderProductVariant
     *
     * @return \OrderBundle\Entity\OrdersProductVariant
     */
    public function getOrdersProductVariant()
    {
        return $this->orders_product_variant;
    }

    /**
     * Set ordersWarehouseInfo
     *
     * @param \OrderBundle\Entity\OrdersWarehouseInfo $ordersWarehouseInfo
     *
     * @return OrdersShippingLabel
     */
    public function setOrdersWarehouseInfo(\OrderBundle\Entity\OrdersWarehouseInfo $ordersWarehouseInfo = null)
    {
        $this->orders_warehouse_info = $ordersWarehouseInfo;

        return $this;
    }

    /**
     * Get ordersWarehouseInfo
     *
     * @return \OrderBundle\Entity\OrdersWarehouseInfo
     */
    public function getOrdersWarehouseInfo()
    {
        return $this->orders_warehouse_info;
    }
}
