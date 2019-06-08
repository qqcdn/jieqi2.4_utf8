<?php

interface PHPExcel_RichText_ITextElement
{
    public function getText();
    public function setText($pText);
    public function getFont();
    public function getHashCode();
}