<?php

/*
 * This file is part of the ZeldaLogs website.
 *
 * (c) 2010-2011 Julien Brochet <mewt@madalynn.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZeldaLogs;

class Log
{
    protected $date;
    protected $file;
    protected $number;
    protected $dateFormatter;
    protected $content;

    public function __construct(\IntlDateFormatter $dateFormatter, \DateTime $date, \SplFileInfo $file, $number = 400)
    {
        $this->date = $date;
        $this->file = $file;
        $this->dateFormatter = $dateFormatter;

        if (false === is_numeric($number)) {
            throw new \InvalidArgumentException('$number must be a number.');
        }

        $this->number = $number;
    }

    public function getFormattedDate()
    {
        return $this->dateFormatter->format($this->date);
    }

    public function load($force = false)
    {
        if (false === $force && $this->content) {
            return $this;
        }

        $this->content = file($this->file->getPathname(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return $this;
    }

    public function search($search)
    {
        if (null === $this->content) {
            throw new \BadFunctionCallException('You need to call "Log::load" first.');
        }

        $tmp = array();
        foreach ($this->content as $line) {
            if (false !== strpos($search, $line)) {
                $tmp[] = $line;
            }
        }

        return $tmp;
    }

    public function getPage($page)
    {
        if ($page <= 0) {
            throw new \InvalidArgumentException('The page must be a positive integer');
        }

        if (null === $this->content) {
            throw new \BadFunctionCallException('You need to call "Log::load" first.');
        }

        $start = ($page - 1) * $this->number;
        $end = $page * $this->number;
        $count = count($this->content);

        if ($end > $count) {
            $end = $count;
        }

        $tmp = array();
        for ($i = $start ; $i < $end ; $i++) {
            $tmp[] = utf8_encode($this->content[$i]);
        }

        return $tmp;
    }

    public function getNumberOfPages()
    {
        if (null === $this->content) {
            throw new \BadFunctionCallException('You need to call "Log::load" first.');
        }

        return ceil(count($this->content) / $this->number);
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getUrl($page = null)
    {
        $url = '/' . implode('/', array(
            $this->date->format('Y'),
            $this->date->format('n'),
            $this->date->format('j')
        ));

        if (null !== $page) {
            $url .= '/' . $page;
        }

        return $url;
    }
}