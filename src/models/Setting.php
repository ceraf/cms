<?php

namespace App\Models;

/**
 * @Entity
 * @Table(name="settings")
 */
class Setting
{
    /**
	* @Id
	* @Column(type="integer")
	* @GeneratedValue(strategy="AUTO")
	*/
    protected $id;
    
	/**
	* @column(type="string", length=30)
	*/
    private $title;

	/**
	* @column(type="string", length=30)
	*/
    private $value;
    
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
     * Get title
     *
     * @return integer
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Set title
     *
     * @param integer $title
     *
     * @return Setting
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
