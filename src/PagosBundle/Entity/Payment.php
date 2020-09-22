<?php

namespace PagosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Payment
 *
 * @ORM\Table(name="payment",uniqueConstraints={@ORM\UniqueConstraint(name="index_ext_ref_pay_meth", columns={"external_reference","paymentMethod_id"})})
 * @ORM\Entity(repositoryClass="PagosBundle\Repository\PaymentRepository")
 *
 */
class Payment
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
     * @var \DateTime
     *
     * @ORM\Column(name="payment_date", type="datetime")
     */
    private $paymentDate;

    /**
     * @var string     
     * 
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="payments")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")     
     */
    private $company;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     * @Assert\Type(
     *     type="integer",
     *     message="Error validating fields: amount is not of the valid type."
     * )
     */
    private $amount;

    /**
     * @var string     
     * 
     * @ORM\ManyToOne(targetEntity="PaymentMethod", inversedBy="payments")
     * @ORM\JoinColumn(name="paymentMethod_id", referencedColumnName="id")
     */
    private $paymentMethod;

    /**
     * @var string 
     *
     * @ORM\Column(name="external_reference", type="string", length=255)
     */
    private $externalReference;

    /**
     * @var string
     *
     * @ORM\Column(name="terminal", type="string", length=255)
     */
    private $terminal;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Status", inversedBy="payments")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * 
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set paymentDate
     *
     * @param \DateTime $paymentDate
     *
     * @return Payment
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate
     *
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Set company
     *
     * @param string $company
     *
     * @return Payment
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     *
     * @return Payment
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set externalReference
     *
     * @param string $externalReference
     *
     * @return Payment
     */
    public function setExternalReference($externalReference)
    {
        $this->externalReference = $externalReference;

        return $this;
    }

    /**
     * Get externalReference
     *
     * @return string
     */
    public function getExternalReference()
    {
        return $this->externalReference;
    }

    /**
     * Set terminal
     *
     * @param string $terminal
     *
     * @return Payment
     */
    public function setTerminal($terminal)
    {
        $this->terminal = $terminal;

        return $this;
    }

    /**
     * Get terminal
     *
     * @return string
     */
    public function getTerminal()
    {
        return $this->terminal;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Payment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Payment
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
    
    /**
     * Get Data General
     *
     * @return array
     */
    /*public function getData()
    {
    	return array(
    			"id"=> $this->getId(),
    			"payment_date"=> $this->getPaymentDate()->format("Y-m-d\TH:i:sP"),
    			"company"=> $this->getCompany(),
    			"amount"=> $this->getAmount()*100,
    			"payment_method"=> $this->getPaymentMethod(),
    			"external_reference"=> $this->getExternalReference(),
    			"status"=> $this->getStatus(),
    			"terminal"=> $this->getTerminal(),
    			"reference"=> $this->getReference()
    	);
    }*/
    
}
