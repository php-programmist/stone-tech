<?php

namespace App\Model\Admin;



use App\Entity\Category;
use App\Entity\Content;

class UpdatePrices
{
    private ?Content $content = null;

    /**
     * @var float Процент увеличения цены
     */
    private float $percent = 0.00;

    /**
     * @return Content|null
     */
    public function getContent(): ?Content
    {
        return $this->content;
    }

    /**
     * @param Content|null $content
     * @return $this
     */
    public function setContent(?Content $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return float
     */
    public function getPercent(): float
    {
        return $this->percent;
    }

    /**
     * @param float $percent
     * @return $this
     */
    public function setPercent(float $percent): self
    {
        $this->percent = $percent;
        return $this;
    }


}