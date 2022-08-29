<?php

namespace App\Pdf;
use Fpdf\Fpdf;

class BasePdf extends Fpdf
{
    function ClippingText($x, $y, $txt, $outline=false)
    {
        $op=$outline ? 5 : 7;
        $this->_out(sprintf('q BT %.2f %.2f Td %d Tr (%s) Tj 0 Tr ET',
            $x*$this->k,
            ($this->h-$y)*$this->k,
            $op,
            $this->_escape($txt)));
    }

    function ClippingRect($x, $y, $w, $h, $outline=false)
    {
        $op=$outline ? 'S' : 'n';
        $this->_out(sprintf('q %.2f %.2f %.2f %.2f re W %s',
            $x*$this->k,
            ($this->h-$y)*$this->k,
            $w*$this->k, -$h*$this->k,
            $op));
    }

    function ClippingEllipse($x, $y, $rx, $ry, $outline=false)
    {
        $op=$outline ? 'S' : 'n';
        $lx=4/3*(M_SQRT2-1)*$rx;
        $ly=4/3*(M_SQRT2-1)*$ry;
        $k=$this->k;
        $h=$this->h;
        $this->_out(sprintf('q %.2f %.2f m %.2f %.2f %.2f %.2f %.2f %.2f c',
            ($x+$rx)*$k, ($h-$y)*$k,
            ($x+$rx)*$k, ($h-($y-$ly))*$k,
            ($x+$lx)*$k, ($h-($y-$ry))*$k,
            $x*$k, ($h-($y-$ry))*$k));
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
            ($x-$lx)*$k, ($h-($y-$ry))*$k,
            ($x-$rx)*$k, ($h-($y-$ly))*$k,
            ($x-$rx)*$k, ($h-$y)*$k));
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
            ($x-$rx)*$k, ($h-($y+$ly))*$k,
            ($x-$lx)*$k, ($h-($y+$ry))*$k,
            $x*$k, ($h-($y+$ry))*$k));
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c W %s',
            ($x+$lx)*$k, ($h-($y+$ry))*$k,
            ($x+$rx)*$k, ($h-($y+$ly))*$k,
            ($x+$rx)*$k, ($h-$y)*$k,
            $op));
    }

    function UnsetClipping()
    {
        $this->_out('Q');
    }

    function ClippedCell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='')
    {
        if($border || $fill || $this->y+$h>$this->PageBreakTrigger)
        {
            $this->Cell($w, $h, '', $border, 0, '', $fill);
            $this->x-=$w;
        }
        $this->ClippingRect($this->x, $this->y, $w, $h, false);
        $this->Cell($w, $h, $txt, '', $ln, $align, 0, $link);
        $this->UnsetClipping();
    }
}
