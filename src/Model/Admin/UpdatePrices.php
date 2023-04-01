<?php

namespace App\Model\Admin;



use App\Entity\Category;

class UpdatePrices
{
    private ?Category $category = null;

    /**
     * @var float Процент увеличения цены
     */
    private float $percent = 0.00;

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return $this
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;
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