<?php

namespace App\Models;

/**
 * @Entity
 * @Table(name="questions")
 */
class Question
{
    /**
	* @Id
	* @Column(type="integer")
	* @GeneratedValue(strategy="AUTO")
	*/
    protected $id;
    
    /**
     * @Column(type="integer", options={"unsigned":true, "default":0})
     */
    private $num;

    /**
     * @Column(type="integer", options={"unsigned":true, "default":0})
     */
    private $complexity;
    
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
     * @return Question
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
     * Set complexity
     *
     * @param integer $complexity
     *
     * @return Question
     */
    public function setComplexity($complexity)
    {
        $this->complexity = $complexity;

        return $this;
    }

    /**
     * Get complexity
     *
     * @return integer
     */
    public function getComplexity()
    {
        return $this->complexity;
    }
    
    
    public function incNum()
    {
        $this->num++;
        return $this;
    }
}
