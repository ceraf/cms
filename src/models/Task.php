<?php

namespace App\Models;

/**
 * @Entity(repositoryClass="App\Models\Repository\TaskRepository")
 * @Table(name="tasks")
 */
class Task
{
    const MAX_TEXT_LENTH = 20;
    /**
	* @Id
	* @Column(type="integer")
	* @GeneratedValue(strategy="AUTO")
	*/
    protected $id;
    
    /**
     * @Column(type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @Column(type="string", length=50, unique=true)
     */
    private $email;
    
	/**
	* @column(type="text")
	*/
	protected $description;

    /**
	* @column(type="string", length=200, nullable=true)
	*/
	protected $preview;
    
    /**
     * @Column(type="boolean")
     */
    private $is_done;

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
     * Set username
     *
     * @param string $username
     *
     * @return Task
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Task
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set preview
     *
     * @param string $preview
     *
     * @return Task
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * Get preview
     *
     * @return string
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * Set isDone
     *
     * @param boolean $isDone
     *
     * @return Task
     */
    public function setIsDone($isDone)
    {
        $this->is_done = $isDone;

        return $this;
    }

    /**
     * Get isDone
     *
     * @return boolean
     */
    public function getIsDone()
    {
        return $this->is_done;
    }
    
    public function getShortText()
    { 
        $str = iconv ('UTF-8' , 'Windows-1251', $this->description);
        if (strlen($str) > self::MAX_TEXT_LENTH) {
            $str = substr($str, 0, self::MAX_TEXT_LENTH);
            $str =  iconv ('Windows-1251' , 'UTF-8', $str).'...';
        } else
            $str =  iconv ('Windows-1251', 'UTF-8', $str);
        return $str;
    }
}
