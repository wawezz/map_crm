<?php

namespace backend\db\models;

use backend\db\normalizers\LeadNormalizer;
use yii\helpers\Json;

class Lead
{

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $client;

    /**
     * @var int
     */
    public $createdBy;

    /**
     * @var int
     */
    public $createdBySecret;

    /**
     * @var int
     */
    public $responsible;

    /**
     * @var int
     */
    public $responsibleSecret;

    /**
     * @var int
     */
    public $status;
    
    /**
    * @var int
    */
    public $budget;

    /**
    * @var int
    */
    public $orderId;

    /**
    * @var \DateTimeImmutable
    */
    public $firstCallAt;

    /**
    * @var int
    */   
    public $countryId;
 
    /**
    * @var int
    */    
    public $currency;

    /**
    * @var int
    */    
    public $product;

    /**
    * @var int
    */   
    public $productCount;

    /**
    * @var int
    */      
    public $productPrice;

    /**
    * @var int
    */      
    public $shippingPrice;

    /**
    * @var bool
    */      
    public $postOrder;

    /**
    * @var string
    */      
    public $rejectionReason;

    /**
    * @var string
    */      
    public $countryName;

    /**
    * @var string
    */      
    public $statusName;
    
    /**
     * @var \DateTimeImmutable
     */
    public $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    public $updatedAt;

    /**
     * @var \DateTimeImmutable
     */
    public $completedAt;

    /**
     * Lead constructor.
     * @throws \Exception
     */
    public function __construct()
    {

        // Compress properties after pdo hydration
        LeadNormalizer::normalize($this);

        if (null === $this->createdAt) {
            $this->createdAt = new \DateTimeImmutable;
        }

        if (null === $this->updatedAt) {
            $this->updatedAt = new \DateTimeImmutable;
        }
        
    }

    public function publicBundle(): array
    {

        return [ 
            'id' => $this->id,
            'name' => $this->name,
            'client' => $this->client,
            'responsible' => ($this->responsibleSecret)?$this->responsible.'-'.$this->responsibleSecret:$this->responsible,
            'createdBy' => ($this->createdBySecret)?$this->createdBy.'-'.$this->createdBySecret:$this->createdBy,
            'status' => $this->status,
            'statusName' => $this->statusName,
            'createdAt' => $this->createdAt,
            'completedAt' => $this->completedAt,
            'budget' => $this->budget,
            'orderId' => $this->orderId,
            'firstCallAt' => $this->firstCallAt,
            'countryId' => $this->countryId,
            'countryName' => $this->countryName,
            'currency' => $this->currency,
            'product' => $this->product,
            'productCount' => $this->productCount,
            'productPrice' => $this->productPrice,
            'shippingPrice' => $this->shippingPrice,
            'postOrder' => $this->postOrder,
            'rejectionReason' => $this->rejectionReason
        ];
    }

}
