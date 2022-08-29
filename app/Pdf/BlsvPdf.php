<?php

namespace App\Pdf;

class BlsvPdf extends BasePdf
{
    private $stats;
    private $year;
    private $clubName;
    private $even;

    function Header()
    {
        $cellHeight = 7;

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, $cellHeight, utf8_decode('Jahresstatistik für den Landessportverband'), 0, 1, 'C');

        $this->SetFont('Arial', '', 12);
        $this->Cell(0, $cellHeight, 'Statistik per ' . $this->year, 0, 1, 'C');

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, $cellHeight, utf8_decode($this->clubName), 0, 1, 'C');

        $this->SetFont('Arial', '', 12);
        if ($this->PageNo() == 1)
        {
            $this->Cell(0, $cellHeight, 'Komprimierte Altersstatistik', 0, 1, 'C');
        }
        else
        {
            $this->Cell(0, $cellHeight, 'Altersstatistik nach Abteilungen', 0, 1, 'C');

        }

        $this->SetDrawColor(150, 150, 150);
    }

    function Footer()
    {
        $this->SetY(-15);
        $tmp = $this->GetY();
        $this->Line(10, $tmp, 200, $tmp);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 7, date('d.m.Y H:i') . ' Seite ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function printCompressedStat()
    {
        $heads = array( 'bis 5 Jahre', '6 bis 13 Jahre', '14 bis 17 Jahre', '18-26 Jahre',
            '27-40 Jahre', '41 bis 60 Jahre', 'Ab 61 Jahre');
        $widths = array( 40, 25, 25, 25 );
        $cellHeight = 7;
        $rightMargin = 45;
        $sumMale = 0;
        $sumFemale = 0;

        $this->SetDrawColor(150, 150, 150);
        $this->SetFillColor(240, 240, 240);
        $this->SetY(80);

        $this->setX($rightMargin);
        $this->Cell($widths[0], $cellHeight, 'Altersgruppe');
        $this->Cell($widths[1], $cellHeight, utf8_decode('Männlich'), 0, 0, 'R');
        $this->Cell($widths[2], $cellHeight, 'Weiblich', 0, 0, 'R');
        $this->Cell($widths[3], $cellHeight, 'Zusammen', 0, 1, 'R');
        $tmp = $this->GetY();
        $this->Line(10, $tmp, 200, $tmp);

        for ($i = 0; $i < 7; $i++)
        {
            if ($i % 2 == 0)
            {
                $tmp = $this->GetY() + 0.2;
                $this->Rect(10, $tmp, 190, $cellHeight - 0.2, 'F');
            }

            $male = $this->stats[-1][$i]['m'];
            $female = $this->stats[-1][$i]['w'];
            $sumMale += $male;
            $sumFemale += $female;
            $this->setX($rightMargin);
            $this->Cell($widths[0], $cellHeight, $heads[$i]);
            $this->Cell($widths[1], $cellHeight, $male, 0, 0, 'R');
            $this->Cell($widths[2], $cellHeight, $female, 0, 0, 'R');
            $this->Cell($widths[3], $cellHeight, $male + $female, 0, 1, 'R');
        }

        $sum = $sumMale + $sumFemale;
        $tmp = $this->GetY();
        $this->Line(10, $tmp, 200, $tmp);
        $this->setX($rightMargin);
        $this->Cell($widths[0], $cellHeight, 'Gesamt');
        $this->Cell($widths[1], $cellHeight, $sumMale, 0, 0, 'R');
        $this->Cell($widths[2], $cellHeight, $sumFemale, 0, 0, 'R');
        $this->Cell($widths[3], $cellHeight, $sum, 0, 1, 'R');

    }

    function printSectionStats()
    {
        $this->SetDrawColor(150, 150, 150);
        $this->SetFillColor(240, 240, 240);
        $cellHeight = 7;
        $reducedHeight = 4;
        $this->even = false;
        $this->SetY(70);

        $this->Cell(5, $reducedHeight, '');
        $this->Cell(23, $reducedHeight, '');
        $this->Cell(20, $reducedHeight, 'bis 5', 0, 0, 'C');
        $this->Cell(20, $reducedHeight, '6-13', 0, 0, 'C');
        $this->Cell(20, $reducedHeight, '14-17', 0, 0, 'C');
        $this->Cell(20, $reducedHeight, '18-26', 0, 0, 'C');
        $this->Cell(20, $reducedHeight, '27-40', 0, 0, 'C');
        $this->Cell(20, $reducedHeight, '41-60', 0, 0, 'C');
        $this->Cell(20, $reducedHeight, 'ab 61', 0, 1, 'C');

        $this->Cell(5, $cellHeight, '');
        $this->Cell(23, $cellHeight, 'Abteilung');
        for ($i = 0; $i < 7; $i++)
        {
            $this->Cell(10, $cellHeight, 'M', 0, 0, 'R');
            $this->Cell(10, $cellHeight, 'W', 0, 0, 'R');
        }
        $this->Cell(20, $cellHeight, 'Gesamt', 0, 1, 'R');

        $tmp = $this->GetY();
        $this->Line(10, $tmp, 200, $tmp);

        foreach ($this->stats as $key => $stat)
        {
            $sumMale = 0;
            $sumFemale = 0;
            $this->even = !$this->even;
            if ($this->even)
            {
                $tmp = $this->GetY() + 0.2;
                $this->Rect(10, $tmp, 190, $cellHeight - 0.2, 'F');
            }
            $this->Cell(5, $cellHeight, $key);
            $this->Cell(23, $cellHeight, $stat['name']);

            for ($i = 0; $i < 7; $i++)
            {
                $male = $stat[$i]['m'];
                $female = $stat[$i]['w'];
                $sumMale += $male;
                $sumFemale += $female;
                $maleString = $male ?  : '';
                $femaleString = $female ? : '';
                $this->Cell(10, $cellHeight, $maleString, 0, 0, 'R');
                $this->Cell(10, $cellHeight, $femaleString, 0, 0, 'R');
            }
            $this->Cell(20, $cellHeight, $sumMale + $sumFemale, 0, 1, 'R');

        }

        $tmp = $this->GetY();
        $this->Line(10, $tmp, 200, $tmp);
    }

    public function getOutput($stats, $year, $clubName)
    {
        $this->stats = $stats;
        $this->year = $year;
        $this->clubName = $clubName;

        $this->AliasNbPages();
        $this->AddPage();
        $this->SetFont('Arial', '', 9);

        $this->printCompressedStat();

        unset ($this->stats[-1]);

        $this->AddPage();

        $this->printSectionStats();

        return $this->Output('', 'S');
    }

}