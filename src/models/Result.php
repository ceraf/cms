<?php

namespace App\Models;

/**
 * @Entity
 * @Table(name="results")
 */
class Result
{
    /**
	* @Id
	* @Column(type="integer")
	* @GeneratedValue(strategy="AUTO")
	*/
    protected $id;
    
    /**
     * @Column(type="integer")
     */
    private $level;

    /**
     * @Column(type="integer")
     */
    private $min;
    
    /**
     * @Column(type="integer")
     */
    private $max;
    
    /**
     * @Column(type="integer")
     */
    private $num;
    
    /**
     * @Column(type="integer")
     */
    private $res;
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set num
     *
     * @param integer $num
     *
     * @return Result
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set min
     *
     * @param integer $min
     *
     * @return Result
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get min
     *
     * @return integer
     */
    public function getMin()
    {
        return $this->min;
    }
    
    /**
     * Set max
     *
     * @param integer $max
     *
     * @return Result
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return integer
     */
    public function getMax()
    {
        return $this->max;
    }
    
    /**
     * Set level
     *
     * @param integer $level
     *
     * @return Result
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }
    
    /**
     * Set res
     *
     * @param integer $res
     *
     * @return Result
     */
    public function setRes($res)
    {
        $this->res = $res;

        return $this;
    }

    /**
     * Get res
     *
     * @return integer
     */
    public function getRes()
    {
        return $this->res;
    }
}
