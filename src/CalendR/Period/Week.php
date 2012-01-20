<?php

namespace CalendR\Period;

/**
 * A calendar week
 */
class Week implements \Iterator, PeriodInterface
{
    /**
     * @var int
     */
    private $number;

    /**
     * @var \DateTime
     */
    private $begin;

    /**
     * @var \DateTime
     */
    private $end;

    private $current = null;

    public function __construct(\DateTime $start)
    {
        if (!self::isValid($start)) {
            throw new Exception\NotAWeek;
        }

        $this->begin = clone $start;
        $this->end = clone $start;
        $this->end->add(new \DateInterval('P7D'));
        $this->number = $start->format('W');
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function contains(\DateTime $date)
    {
        return $date->format('W') == $this->number;
    }

    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->begin;
    }

    public static function isValid(\DateTime $start)
    {
        if (1 != $start->format('w')) {
            return false;
        }

        return true;
    }

    /**
     * @return Week
     */
    function getNext()
    {
        return new self($this->end);
    }

    /**
     * @return Week
     */
    function getPrevious()
    {
        $start = clone $this->begin;
        $start->sub(new \DateInterval('1W'));

        return new self($start);
    }

    /*
    * Iterator implementation
    */
    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        if (!$this->valid()) {
            $this->current = new Day($this->begin);
        } else {
            $this->current = $this->current->getNext();
            if (!$this->contains($this->current->getBegin())) {
                $this->current = null;
            }
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return scalar scalar on success, integer
     * 0 on failure.
     */
    public function key()
    {
        return $this->current->getBegin()->format('d-m-Y');
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return null !== $this->current;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->current = null;
        $this->next();
    }


}
